<?php

declare(strict_types=1);

namespace CreditNote\Exception;

/**
 * The credit notes granted on an order would refund more than the order was worth.
 */
final class CreditNoteExceedsOrderException extends \RuntimeException
{
    public function __construct(
        public readonly string $orderRef,
        public readonly float $ceiling,
        public readonly float $granted,
        public readonly float $asked,
    ) {
        parent::__construct(\sprintf(
            'The credit notes on order %s cannot exceed its total of %.2f: %.2f already granted, %.2f asked.',
            $orderRef,
            $ceiling,
            $granted,
            $asked,
        ));
    }
}
