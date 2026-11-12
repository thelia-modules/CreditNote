<?php

declare(strict_types=1);

namespace CreditNote\Service;

use CreditNote\CreditNote as CreditNoteModule;
use CreditNote\Exception\CreditNoteExceedsOrderException;
use CreditNote\Model\CreditNote;
use CreditNote\Model\Map\CreditNoteTableMap;
use Propel\Runtime\Propel;

/**
 * The credit notes of an order never refund more than the order was worth: what the other
 * credit notes of the order already grant, plus the one being written, stays within the
 * total of the order (postage and discount included). A refused credit note grants nothing. The
 * shop can turn the ceiling off (`order_ceiling` setting of the module).
 */
final readonly class CreditNoteOrderCeiling
{
    private const REFUSED = 'refused';

    public function assertWithinOrder(CreditNote $creditNote): void
    {
        if (!CreditNoteModule::isOrderCeilingEnforced()) {
            return;
        }

        $order = $creditNote->getOrder();

        if (null === $order || self::REFUSED === $creditNote->getCreditNoteStatus()?->getCode()) {
            return;
        }

        // Two credit notes written at the same time on one order would each read the total of
        // the others before the other one exists: the order row is locked until the transaction
        // that saves the credit note ends, so the second one waits and counts the first.
        OrderRowLock::take((int) $order->getId());

        $ceiling = round($order->getTotalAmount(), 2);
        $granted = $this->grantedByOthers($creditNote);
        $asked = round((float) $creditNote->getTotalPriceWithTax(), 2);

        if (Cents::of($granted) + Cents::of($asked) > Cents::of($ceiling)) {
            throw new CreditNoteExceedsOrderException((string) $order->getRef(), $ceiling, $granted, $asked);
        }
    }

    /**
     * The total with tax the other credit notes of the order grant, refused ones left out.
     */
    public function grantedByOthers(CreditNote $creditNote): float
    {
        // A locking read, not a plain one: it sees what is committed now, whatever the
        // transaction read before it. Under REPEATABLE READ a plain sum would be the one of the
        // first read of the transaction, taken before the credit note written meanwhile.
        $sql = 'SELECT COALESCE(SUM(cn.`total_price_with_tax`), 0)'
            .' FROM `credit_note` cn INNER JOIN `credit_note_status` s ON s.`id` = cn.`status_id`'
            .' WHERE cn.`order_id` = :order AND s.`code` <> :refused'
            .(null === $creditNote->getId() ? '' : ' AND cn.`id` <> :self')
            .' FOR UPDATE';
        $statement = Propel::getWriteConnection(CreditNoteTableMap::DATABASE_NAME)->prepare($sql);
        $statement->bindValue(':order', (int) $creditNote->getOrderId(), \PDO::PARAM_INT);
        $statement->bindValue(':refused', self::REFUSED);

        if (null !== $creditNote->getId()) {
            $statement->bindValue(':self', (int) $creditNote->getId(), \PDO::PARAM_INT);
        }

        $statement->execute();

        return round((float) $statement->fetchColumn(), 2);
    }
}
