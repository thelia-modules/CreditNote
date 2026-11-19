<?php

declare(strict_types=1);

namespace CreditNote\Service;

use CreditNote\Model\CreditNote;

/**
 * The order credit notes are spent in, which is also the order their rows are locked in: the
 * ones to spend whole first, then the others, the oldest first. A payment and a release that
 * touch the same credit notes lock them in this one order, so neither waits on a row the other
 * holds while holding the row the other waits on.
 */
final class CreditNoteSpendingOrder
{
    public static function compare(CreditNote $a, CreditNote $b): int
    {
        return [(int) $a->getAllowPartialUse(), (int) $a->getId()] <=> [(int) $b->getAllowPartialUse(), (int) $b->getId()];
    }
}
