<?php

declare(strict_types=1);

namespace CreditNote\Tests\Unit;

use CreditNote\Helper\BadgeColor;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * A badge color reaches a `style` attribute as is only when it is a hexadecimal color.
 */
final class BadgeColorTest extends TestCase
{
    #[Test]
    #[DataProvider('hexadecimalColors')]
    public function aHexadecimalColorIsKept(string $color): void
    {
        self::assertSame($color, BadgeColor::css($color));
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function hexadecimalColors(): iterable
    {
        yield 'short' => ['#777'];
        yield 'long' => ['#00cc00'];
        yield 'upper case' => ['#CF7973'];
        yield 'with alpha' => ['#3399ffcc'];
    }

    #[Test]
    #[DataProvider('otherValues')]
    public function anythingElseFallsBackToGrey(?string $color): void
    {
        self::assertSame(BadgeColor::FALLBACK, BadgeColor::css($color));
    }

    /**
     * @return iterable<string, array{?string}>
     */
    public static function otherValues(): iterable
    {
        yield 'none' => [null];
        yield 'empty' => [''];
        yield 'name' => ['red'];
        yield 'declaration injected' => ['#fff;background-image:url(https://example.com/)'];
        yield 'line break' => ["#fff\n"];
    }
}
