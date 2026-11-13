<?php

declare(strict_types=1);

namespace CreditNote\Service;

use Thelia\Model\CurrencyQuery;

/**
 * An amount as the themes write theirs: the currency by its ISO code, laid out for a language,
 * the way the Twig `format_currency` filter the front-office and back-office themes use does
 * (the symbol before the amount in English, after it in French). The amounts of the module then
 * read like the ones the theme prints beside them.
 *
 * Without a language, the one of the request is used, as the filter does: the back-office
 * templates call it that way.
 */
final readonly class LocalizedMoney
{
    public function format(float $amount, ?int $currencyId, ?string $locale = null): string
    {
        $code = null !== $currencyId ? CurrencyQuery::create()->findPk($currencyId)?->getCode() : null;
        $locale = null !== $locale && '' !== $locale ? $locale : \Locale::getDefault();

        if (null === $code || '' === $code) {
            $plain = new \NumberFormatter($locale, \NumberFormatter::DECIMAL);
            $plain->setAttribute(\NumberFormatter::MIN_FRACTION_DIGITS, 2);
            $plain->setAttribute(\NumberFormatter::MAX_FRACTION_DIGITS, 2);

            return (string) $plain->format($amount);
        }

        return (string) (new \NumberFormatter($locale, \NumberFormatter::CURRENCY))->formatCurrency($amount, $code);
    }
}
