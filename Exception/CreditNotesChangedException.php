<?php

declare(strict_types=1);

namespace CreditNote\Exception;

use Thelia\Domain\Checkout\Exception\InvalidPaymentException;

/**
 * The credit notes of the cart no longer pay what the payment step showed: one was spent or
 * taken back meanwhile. The order is not placed; the cart is already put right, so the buyer
 * reads the new amounts and the same request goes through next.
 */
final class CreditNotesChangedException extends InvalidPaymentException
{
}
