<?php

declare(strict_types=1);

namespace CreditNote\Tests\Unit;

use CreditNote\Service\Cents;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * Cents are the amount rounded to the cent the way the balance of a credit note is, whatever
 * the float holds: a balance and a use of the same amount are the same number of cents.
 */
final class CentsTest extends TestCase
{
    #[Test]
    #[DataProvider('amounts')]
    public function anAmountIsTheCentsOfItsRoundingToTwoDecimals(string|float $amount, int $cents): void
    {
        self::assertSame($cents, Cents::of($amount));
        self::assertSame((int) round(round((float) $amount, 2) * 100), Cents::of($amount));
        self::assertSame($cents, Cents::of(Cents::toDecimal($cents)), 'and back');
        self::assertSame($cents, Cents::of(Cents::toFloat($cents)));
    }

    /**
     * @return iterable<string, array{0: string|float, 1: int}>
     */
    public static function amounts(): iterable
    {
        yield 'two decimals' => ['117.600000', 11760];
        yield 'half a cent goes up, though 1.005 * 100 is 100.49999' => ['1.005000', 101];
        yield 'the same as a float' => [1.005, 101];
        yield 'six decimals under the half cent' => ['24.994999', 2499];
        yield 'six decimals over the half cent' => ['24.995001', 2500];
        yield 'floats that do not add up' => [0.1 + 0.2, 30];
        yield 'nothing' => ['0.000000', 0];
        yield 'a negative amount' => ['-1.005000', -101];
        yield 'a large amount' => ['1234567.890000', 123456789];
    }
}
