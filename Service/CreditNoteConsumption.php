<?php

declare(strict_types=1);

namespace CreditNote\Service;

use CreditNote\Exception\CreditNoteConsumptionException;
use CreditNote\Model\CreditNote;
use CreditNote\Model\CreditNoteQuery;
use CreditNote\Model\CreditNoteStatusQuery;
use CreditNote\Model\Map\CreditNoteTableMap;
use CreditNote\Model\OrderCreditNote;
use CreditNote\Model\OrderCreditNoteQuery;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\Propel;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use Thelia\Model\Order;

/**
 * Uses a credit note on an order. The balance is read again under an exclusive lock on the
 * credit note row, inside the transaction that writes the use: two orders paid at the same
 * time with the same credit note consume its balance once, the second one waits for the
 * first and is refused when nothing is left. A credit note that reaches a zero balance is
 * marked used.
 *
 * Public: the module class settles and releases the payment parts through it, and reads it from
 * the container.
 */
#[Autoconfigure(public: true)]
final readonly class CreditNoteConsumption
{
    private const USED = 'used';
    private const ACCEPTED = 'accepted';

    public function __construct(
        private CreditNoteBalance $balance,
    ) {
    }

    /**
     * @throws CreditNoteConsumptionException when the credit note cannot be used this way
     */
    public function consume(int $creditNoteId, Order $order, float $amount, ?ConnectionInterface $connection = null): OrderCreditNote
    {
        $connection ??= Propel::getConnection(CreditNoteTableMap::DATABASE_NAME);
        $connection->beginTransaction();

        try {
            $use = $this->consumeWithin($connection, $creditNoteId, $order, $amount);

            $connection->commit();

            return $use;
        } catch (\Throwable $throwable) {
            $connection->rollBack();

            throw $throwable;
        }
    }

    /**
     * The same use, written in a transaction the caller already holds and will end: nothing is
     * begun, committed or rolled back here. The settlement of a payment part runs inside the
     * transaction of the core, where a rollback would leave every part of the order unable to
     * commit; a refusal is thrown and the caller decides.
     *
     * @param int|null $paymentPartId the payment part of the order this use settles, kept to give
     *                                it back when the core releases that part
     *
     * @throws CreditNoteConsumptionException when the credit note cannot be used this way
     */
    public function consumeWithin(ConnectionInterface $connection, int $creditNoteId, Order $order, float $amount, ?int $paymentPartId = null): OrderCreditNote
    {
        $this->lock($creditNoteId, $connection);

        $creditNote = CreditNoteQuery::create()->findPk($creditNoteId, $connection)
            ?? throw new CreditNoteConsumptionException(\sprintf('Credit note %d does not exist.', $creditNoteId));

        // The row may come from the instance pool, as it was before the lock: read it again.
        $creditNote->reload(true, $connection);

        $this->assertUsable($creditNote, $order, $amount, $connection);

        $remaining = $this->balance->remaining($creditNote, $connection);

        if (!$creditNote->getAllowPartialUse() && abs($amount - $remaining) > 0.005) {
            throw new CreditNoteConsumptionException(\sprintf('Credit note %s must be used at once, for its whole balance of %.2f.', $creditNote->getRef(), $remaining));
        }

        if ($amount > $remaining + 0.005) {
            throw new CreditNoteConsumptionException(\sprintf('Credit note %s has %.2f left, %.2f asked.', $creditNote->getRef(), $remaining, $amount));
        }

        $use = (new OrderCreditNote())
            ->setOrderId($order->getId())
            ->setCreditNoteId($creditNote->getId())
            ->setAmountPrice(\sprintf('%.6F', $amount))
            ->setOrderPaymentPartId($paymentPartId);
        $use->save($connection);

        if (round($remaining - $amount, 2) <= 0) {
            $used = CreditNoteStatusQuery::create()->findOneByCode(self::USED, $connection);

            if (null !== $used) {
                $creditNote->setCreditNoteStatus($used);
                $creditNote->save($connection);
            }
        }

        return $use;
    }

    /**
     * Gives back what a payment part of an order consumed: its uses are removed, and a credit
     * note they had emptied is accepted again. A part with no use left was already given back,
     * or never settled: nothing is given twice. Written in the transaction of the caller, like
     * consumeWithin().
     *
     * @return int the number of uses given back
     */
    public function giveBackWithin(ConnectionInterface $connection, int $paymentPartId): int
    {
        $uses = OrderCreditNoteQuery::create()
            ->filterByOrderPaymentPartId($paymentPartId)
            ->orderByCreditNoteId()
            ->find($connection);

        foreach ($uses as $use) {
            $creditNoteId = (int) $use->getCreditNoteId();
            $this->lock($creditNoteId, $connection);
            $use->delete($connection);

            $creditNote = CreditNoteQuery::create()->findPk($creditNoteId, $connection);

            if (null === $creditNote) {
                continue;
            }

            // The row may come from the instance pool, as it was before it was marked used.
            $creditNote->reload(true, $connection);

            if (!$creditNote->getCreditNoteStatus($connection)?->getUsed()) {
                continue;
            }

            $accepted = CreditNoteStatusQuery::create()->findOneByCode(self::ACCEPTED, $connection);

            if (null !== $accepted && $this->balance->remaining($creditNote, $connection) > 0) {
                // Written past the model on purpose: the status flow has no way back from used,
                // and must not offer one to the back-office. Only a use given back reopens it.
                CreditNoteQuery::create()
                    ->filterById($creditNoteId)
                    ->update(['StatusId' => $accepted->getId(), 'UpdatedAt' => new \DateTime()], $connection);
                CreditNoteTableMap::removeInstanceFromPool($creditNote);
            }
        }

        return \count($uses);
    }

    private function lock(int $creditNoteId, ConnectionInterface $connection): void
    {
        // The lock is held until the surrounding transaction ends; the rows this
        // transaction reads afterwards are the ones a concurrent use will see committed.
        $statement = $connection->prepare('SELECT `id` FROM `credit_note` WHERE `id` = :id FOR UPDATE');
        $statement->bindValue(':id', $creditNoteId, \PDO::PARAM_INT);
        $statement->execute();
        $statement->fetchColumn();
    }

    private function assertUsable(CreditNote $creditNote, Order $order, float $amount, ConnectionInterface $connection): void
    {
        if ($amount <= 0) {
            throw new CreditNoteConsumptionException('The amount to use must be positive.');
        }

        $status = $creditNote->getCreditNoteStatus($connection);

        if (null === $status || !$status->getInvoiced()) {
            throw new CreditNoteConsumptionException(\sprintf('Credit note %s is not accepted.', $creditNote->getRef()));
        }

        if ($status->getUsed()) {
            throw new CreditNoteConsumptionException(\sprintf('Credit note %s is already used.', $creditNote->getRef()));
        }

        if ((int) $creditNote->getCustomerId() !== (int) $order->getCustomerId()) {
            throw new CreditNoteConsumptionException(\sprintf('Credit note %s does not belong to the customer of order %s.', $creditNote->getRef(), $order->getRef()));
        }

        $alreadyUsedOnThisOrder = OrderCreditNoteQuery::create()
            ->filterByOrderId($order->getId())
            ->filterByCreditNoteId($creditNote->getId())
            ->exists($connection);

        if ($alreadyUsedOnThisOrder) {
            throw new CreditNoteConsumptionException(\sprintf('Credit note %s is already used on order %s.', $creditNote->getRef(), $order->getRef()));
        }
    }
}
