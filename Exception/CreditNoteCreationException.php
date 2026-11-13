<?php

declare(strict_types=1);

namespace CreditNote\Exception;

/**
 * A credit note that cannot be created from what was asked: an order that does not exist or is
 * not invoiced, a line that is not one of the order, a quantity the order does not hold. The
 * message says which, and is safe to hand back to the caller.
 */
final class CreditNoteCreationException extends \RuntimeException
{
}
