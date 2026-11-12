<?php

declare(strict_types=1);

namespace CreditNote\Exception;

/**
 * A credit note the buyer may not put on this cart. The reason is a code the checkout turns into
 * a translated message; the message of the exception is for the logs.
 */
final class CreditNoteReservationException extends \RuntimeException
{
    public const NOT_USABLE = 'not_usable';
    public const WHOLE_BALANCE_EXCEEDS_CART = 'whole_balance_exceeds_cart';

    private function __construct(public readonly string $reason, string $message)
    {
        parent::__construct($message);
    }

    public static function notUsable(int $creditNoteId): self
    {
        return new self(self::NOT_USABLE, \sprintf('Credit note %d cannot be used on this cart.', $creditNoteId));
    }

    public static function wholeBalanceExceedsCart(string $ref, float $balance, float $due): self
    {
        return new self(
            self::WHOLE_BALANCE_EXCEEDS_CART,
            \sprintf('Credit note %s must be used at once for %.2f, the cart leaves %.2f to pay.', $ref, $balance, $due),
        );
    }
}
