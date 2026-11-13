<?php

declare(strict_types=1);

namespace CreditNote\Tests\Support\Payment;

use Symfony\Component\HttpFoundation\Response;
use Thelia\Model\Order;
use Thelia\Module\AbstractPaymentModule;

/**
 * A provider that charges the total of the order and knows nothing of the payment in several
 * parts: the core refuses to combine it with a credit note that leaves something to pay.
 */
final class CreditNoteTestPlainProvider extends AbstractPaymentModule
{
    public function pay(Order $order): ?Response
    {
        return null;
    }

    public function isValidPayment(): bool
    {
        return true;
    }
}
