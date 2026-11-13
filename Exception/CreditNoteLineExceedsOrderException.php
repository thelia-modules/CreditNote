<?php

declare(strict_types=1);

namespace CreditNote\Exception;

/**
 * The credit notes of an order would refund more units of one of its lines than it holds.
 */
final class CreditNoteLineExceedsOrderException extends \RuntimeException
{
    public function __construct(
        public readonly string $orderRef,
        public readonly string $lineTitle,
        public readonly int $held,
        public readonly int $refunded,
        public readonly int $asked,
    ) {
        parent::__construct(\sprintf(
            'Line "%s" of order %s holds %d: %d already refunded, %d asked.',
            $lineTitle,
            $orderRef,
            $held,
            $refunded,
            $asked,
        ));
    }
}
