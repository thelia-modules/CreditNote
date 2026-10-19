<?php

declare(strict_types=1);

namespace CreditNote\Exception;

/**
 * A credit note was created or saved without an invoice address: no customer picked, or
 * "Other" with the name left empty.
 */
final class CreditNoteInvoiceAddressMissingException extends \RuntimeException
{
    public function __construct()
    {
        parent::__construct('A credit note needs an invoice address.');
    }
}
