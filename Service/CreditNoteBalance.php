<?php

declare(strict_types=1);

namespace CreditNote\Service;

use CreditNote\Model\CreditNote;
use CreditNote\Model\OrderCreditNoteQuery;

/**
 * What is left of a credit note: its total with tax minus what the orders it was used on
 * consumed. Computed from the `order_credit_note` rows, never stored.
 */
final readonly class CreditNoteBalance
{
    public function used(CreditNote $creditNote): float
    {
        $used = OrderCreditNoteQuery::create()
            ->filterByCreditNoteId($creditNote->getId())
            ->withColumn('COALESCE(SUM(order_credit_note.amount_price), 0)', 'used_amount')
            ->select(['used_amount'])
            ->findOne();

        return round((float) $used, 2);
    }

    public function remaining(CreditNote $creditNote): float
    {
        return round((float) $creditNote->getTotalPriceWithTax() - $this->used($creditNote), 2);
    }
}
