<?php

declare(strict_types=1);

namespace CreditNote\Service;

use CreditNote\Model\CreditNote;
use CreditNote\Model\OrderCreditNoteQuery;
use Propel\Runtime\Connection\ConnectionInterface;

/**
 * What is left of a credit note: its total with tax minus what the orders it was used on
 * consumed. Computed from the `order_credit_note` rows, never stored.
 */
final readonly class CreditNoteBalance
{
    public function used(CreditNote $creditNote, ?ConnectionInterface $connection = null): float
    {
        $used = OrderCreditNoteQuery::create()
            ->filterByCreditNoteId($creditNote->getId())
            ->withColumn('COALESCE(SUM(order_credit_note.amount_price), 0)', 'used_amount')
            ->select(['used_amount'])
            ->findOne($connection);

        return round((float) $used, 2);
    }

    public function remaining(CreditNote $creditNote, ?ConnectionInterface $connection = null): float
    {
        return round((float) $creditNote->getTotalPriceWithTax() - $this->used($creditNote, $connection), 2);
    }
}
