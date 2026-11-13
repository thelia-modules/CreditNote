<?php

declare(strict_types=1);

namespace CreditNote\Tests\Integration;

use CreditNote\Service\LocalizedMoney;
use PHPUnit\Framework\Attributes\Test;
use Thelia\Model\CurrencyQuery;
use Thelia\Test\IntegrationTestCase;

/**
 * The amounts of the module in the front-office are written the way the theme writes its own:
 * the Twig `format_currency` filter, for the language of the page.
 */
final class LocalizedMoneyTest extends IntegrationTestCase
{
    #[Test]
    public function anAmountIsLaidOutForTheLanguageOfThePageLikeTheFilterOfTheTheme(): void
    {
        $euro = CurrencyQuery::create()->findOneByCode('EUR');
        self::assertNotNull($euro, 'the test database has the euro');
        $money = $this->getService(LocalizedMoney::class);
        $twig = $this->getService('twig');

        foreach (['en_US', 'fr_FR', 'de_DE'] as $locale) {
            self::assertSame(
                $twig->createTemplate("{{ 1234.5|format_currency('EUR', locale: locale) }}")->render(['locale' => $locale]),
                $money->format(1234.5, $euro->getId(), $locale),
                $locale,
            );
        }

        self::assertSame('€200.00', $money->format(200.0, $euro->getId(), 'en_US'));
        self::assertSame(
            $twig->createTemplate("{{ 200|format_currency('EUR') }}")->render(),
            $money->format(200.0, $euro->getId()),
            'the language of the request when none is given, as the filter does',
        );
        self::assertSame('200.00', $money->format(200.0, null, 'en_US'), 'no symbol for an unknown currency');
    }
}
