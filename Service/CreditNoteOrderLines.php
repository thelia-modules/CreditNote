<?php

declare(strict_types=1);

namespace CreditNote\Service;

use CreditNote\Exception\CreditNoteLineExceedsOrderException;
use CreditNote\Model\CreditNote;
use CreditNote\Model\Map\CreditNoteTableMap;
use Propel\Runtime\Propel;

/**
 * A line of an order is refunded once: the credit notes of the order never give back more
 * units of a line than it holds, whatever the amounts and whether the ceiling of the order is
 * on or off. A refused credit note gives nothing back. A commercial gesture beyond the order
 * is a free amount, not a second refund of the same line.
 */
final readonly class CreditNoteOrderLines
{
    private const REFUSED = 'refused';

    /**
     * @throws CreditNoteLineExceedsOrderException
     */
    public function assertWithinOrder(CreditNote $creditNote): void
    {
        $order = $creditNote->getOrder();

        if (null === $order || self::REFUSED === $creditNote->getCreditNoteStatus()?->getCode()) {
            return;
        }

        $asked = [];
        $lines = [];

        foreach ($creditNote->getCreditNoteDetails() as $creditNoteDetail) {
            $orderProductId = $creditNoteDetail->getOrderProductId();

            if (null === $orderProductId || null === $creditNoteDetail->getOrderProduct()) {
                continue;
            }

            $asked[(int) $orderProductId] = ($asked[(int) $orderProductId] ?? 0) + (int) $creditNoteDetail->getQuantity();
            $lines[(int) $orderProductId] = $creditNoteDetail->getOrderProduct();
        }

        if ([] === $asked) {
            return;
        }

        // Two credit notes written at the same time on the order count each other.
        OrderRowLock::take((int) $order->getId());

        $refunded = $this->refundedByOthers($creditNote, array_values(array_keys($asked)));

        foreach ($asked as $orderProductId => $quantity) {
            $held = (int) $lines[$orderProductId]->getQuantity();
            $already = $refunded[$orderProductId] ?? 0;

            if ($already + $quantity > $held) {
                throw new CreditNoteLineExceedsOrderException((string) $order->getRef(), (string) $lines[$orderProductId]->getTitle(), $held, $already, $quantity);
            }
        }
    }

    /**
     * @param list<int> $orderProductIds
     *
     * @return array<int, int> the units the other credit notes refund, by order product id
     */
    private function refundedByOthers(CreditNote $creditNote, array $orderProductIds): array
    {
        // A locking read, for the reason given in CreditNoteOrderCeiling::grantedByOthers().
        $lines = implode(', ', array_map(static fn (int $index): string => ':line'.$index, array_keys($orderProductIds)));
        $sql = 'SELECT d.`order_product_id`, SUM(d.`quantity`)'
            .' FROM `credit_note_detail` d'
            .' INNER JOIN `credit_note` cn ON cn.`id` = d.`credit_note_id`'
            .' INNER JOIN `credit_note_status` s ON s.`id` = cn.`status_id`'
            .' WHERE d.`order_product_id` IN ('.$lines.') AND s.`code` <> :refused'
            .(null === $creditNote->getId() ? '' : ' AND cn.`id` <> :self')
            .' GROUP BY d.`order_product_id` FOR UPDATE';
        $statement = Propel::getWriteConnection(CreditNoteTableMap::DATABASE_NAME)->prepare($sql);
        $statement->bindValue(':refused', self::REFUSED);

        foreach ($orderProductIds as $index => $orderProductId) {
            $statement->bindValue(':line'.$index, $orderProductId, \PDO::PARAM_INT);
        }

        if (null !== $creditNote->getId()) {
            $statement->bindValue(':self', (int) $creditNote->getId(), \PDO::PARAM_INT);
        }

        $statement->execute();

        return array_map('intval', $statement->fetchAll(\PDO::FETCH_KEY_PAIR));
    }
}
