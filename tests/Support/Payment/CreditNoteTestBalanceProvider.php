<?php

declare(strict_types=1);

namespace CreditNote\Tests\Support\Payment;

use Symfony\Component\HttpFoundation\Response;
use Thelia\Model\Order;
use Thelia\Module\AbstractPaymentModule;
use Thelia\Module\BalanceChargingPaymentModuleInterface;

/**
 * A provider that charges only what the credit notes leave, the way a module adapted to the
 * payment in several parts does. It confirms nothing by itself: the test does.
 */
final class CreditNoteTestBalanceProvider extends AbstractPaymentModule implements BalanceChargingPaymentModuleInterface
{
    /** @var list<int> the orders the provider was asked to pay */
    public static array $paidOrderIds = [];

    public static function reset(): void
    {
        self::$paidOrderIds = [];
    }

    public function pay(Order $order): ?Response
    {
        self::$paidOrderIds[] = (int) $order->getId();

        return null;
    }

    public function isValidPayment(): bool
    {
        return true;
    }

    public function supportsPaymentRetry(): bool
    {
        return true;
    }
}
