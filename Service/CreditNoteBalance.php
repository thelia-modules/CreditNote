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

    /**
     * What is left of each credit note of a page, read in one query for the whole page.
     *
     * @param iterable<CreditNote> $creditNotes
     *
     * @return array<int, float> the balance by credit note id
     */
    public function remainingOfAll(iterable $creditNotes, ?ConnectionInterface $connection = null): array
    {
        $totals = [];

        foreach ($creditNotes as $creditNote) {
            $totals[(int) $creditNote->getId()] = (float) $creditNote->getTotalPriceWithTax();
        }

        if ([] === $totals) {
            return [];
        }

        $used = OrderCreditNoteQuery::create()
            ->filterByCreditNoteId(array_keys($totals))
            ->groupByCreditNoteId()
            ->withColumn('SUM(order_credit_note.amount_price)', 'used_amount')
            ->select(['CreditNoteId', 'used_amount'])
            ->find($connection)
            ->toKeyValue('CreditNoteId', 'used_amount');

        $balances = [];

        foreach ($totals as $id => $total) {
            $balances[$id] = round($total - (float) ($used[$id] ?? 0), 2);
        }

        return $balances;
    }
}
