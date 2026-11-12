<?php

declare(strict_types=1);

namespace CreditNote\Service;

/**
 * Amounts of money counted in cents, as integers: two amounts are equal or they are not, with
 * no tolerance to choose. The decimal strings of the database and the floats of Thelia are
 * rounded to the cent once, here.
 */
final class Cents
{
    /**
     * Rounded to the cent first, as the balance of a credit note is (CreditNoteBalance): a
     * float times a hundred may fall just under the half cent the amount really holds.
     */
    public static function of(string|float|int|null $amount): int
    {
        return (int) round(round((float) $amount, 2) * 100);
    }

    public static function toFloat(int $cents): float
    {
        return $cents / 100;
    }

    /** As the decimal columns hold it: six decimals. */
    public static function toDecimal(int $cents): string
    {
        return \sprintf('%.6F', $cents / 100);
    }
}
