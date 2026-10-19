<?php

declare(strict_types=1);

namespace CreditNote\Helper;

/**
 * The color of a status or type badge, written into a `style` attribute: HTML escaping does not
 * protect that context, so anything other than a hexadecimal color falls back to grey.
 */
final class BadgeColor
{
    public const FALLBACK = '#777';

    public static function css(?string $color): string
    {
        return null !== $color && 1 === preg_match('/^#[0-9a-fA-F]{3,8}$/D', $color) ? $color : self::FALLBACK;
    }
}
