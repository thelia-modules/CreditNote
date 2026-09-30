<?php

declare(strict_types=1);

namespace CreditNote\Exception;

/**
 * A credit note cannot be used the way an order asks: unknown, not accepted, already used,
 * belonging to another customer, or asked for more than it is worth.
 */
final class CreditNoteConsumptionException extends \RuntimeException
{
}
