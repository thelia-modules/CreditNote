<?php

declare(strict_types=1);

namespace CreditNote\Tests\Support\Payment;

use Symfony\Component\HttpFoundation\Response;
use Thelia\Model\Cart;
use Thelia\Model\Order;
use Thelia\Model\OrderPaymentPart;
use Thelia\Module\AbstractPaymentModule;
use Thelia\Module\InstantPaymentModuleInterface;

/**
 * Another means that pays part of an order inside the shop (a gift card, say), settled before
 * the credit notes: what the core gives back when the credit notes refuse their own part.
 */
final class CreditNoteTestInstantMeans extends AbstractPaymentModule implements InstantPaymentModuleInterface
{
    public static string $appliedAmount = '0';

    /** @var list<int> */
    public static array $settledPartIds = [];

    /** @var list<int> */
    public static array $releasedPartIds = [];

    /** What happens in the shop between the settlement of this means and the next one. */
    public static ?\Closure $onceSettled = null;

    public static function reset(): void
    {
        self::$onceSettled = null;
        self::$appliedAmount = '0';
        self::$settledPartIds = [];
        self::$releasedPartIds = [];
    }

    public function getAmountAppliedToCart(Cart $cart): string
    {
        return self::$appliedAmount;
    }

    public function settlePart(OrderPaymentPart $part): void
    {
        self::$settledPartIds[] = (int) $part->getId();

        if (null !== self::$onceSettled) {
            (self::$onceSettled)();
        }
    }

    public function releasePart(OrderPaymentPart $part): void
    {
        self::$releasedPartIds[] = (int) $part->getId();
    }

    public function pay(Order $order): ?Response
    {
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
