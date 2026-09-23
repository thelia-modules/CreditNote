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
 * Public: nothing in the module calls it yet (the checkout of #468 will), and a service nothing
 * references is dropped from the compiled container, out of reach of a command or a test.
 */
#[Autoconfigure(public: true)]
final readonly class CreditNoteConsumption
{
    private const USED = 'used';

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
            $this->lock($creditNoteId, $connection);

            $creditNote = CreditNoteQuery::create()->findPk($creditNoteId, $connection)
                ?? throw new CreditNoteConsumptionException(\sprintf('Credit note %d does not exist.', $creditNoteId));

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
                ->setAmountPrice(\sprintf('%.6F', $amount));
            $use->save($connection);

            if (round($remaining - $amount, 2) <= 0) {
                $used = CreditNoteStatusQuery::create()->findOneByCode(self::USED, $connection);

                if (null !== $used) {
                    $creditNote->setCreditNoteStatus($used);
                    $creditNote->save($connection);
                }
            }

            $connection->commit();

            return $use;
        } catch (\Throwable $throwable) {
            $connection->rollBack();

            throw $throwable;
        }
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
