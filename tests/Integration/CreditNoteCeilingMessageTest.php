<?php

declare(strict_types=1);

namespace CreditNote\Tests\Integration;

use CreditNote\Tests\Support\BackOfficeRouteTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Thelia\Model\LangQuery;

/**
 * The message that refuses a credit note beyond the total of its order writes its amounts
 * with the separators of the language of the back-office. The order of the fixture is worth
 * 235.20 with tax, raised here to 1 411.20 so that the thousands separator shows.
 */
final class CreditNoteCeilingMessageTest extends BackOfficeRouteTestCase
{
    #[Test]
    #[DataProvider('languages')]
    public function theAmountsFollowTheLanguageOfTheBackOffice(string $locale): void
    {
        $lang = LangQuery::create()->findOneByLocale($locale);

        if (null === $lang || !$lang->getActive()) {
            self::markTestSkipped(\sprintf('The language %s is not active in the test database.', $locale));
        }

        $customer = $this->fixture->customer();
        $order = $this->fixture->paidOrder($customer);
        $order->getOrderProducts()->getFirst()->setQuantity(12)->save($this->getPropelConnection());
        $this->fixture->creditNote($customer, $order, 'accepted', 12)->save();
        $this->loginSuperAdmin($lang);

        $errors = $this->requestAndReadErrors('POST', '/admin/credit-note/create', $this->creationOf($order));

        $ceiling = number_format(1411.2, 2, $lang->getDecimalSeparator(), $lang->getThousandsSeparator());
        $asked = number_format(117.6, 2, $lang->getDecimalSeparator(), $lang->getThousandsSeparator());

        self::assertCount(1, $errors);
        self::assertStringContainsString('('.$ceiling.')', $errors[0]);
        self::assertStringContainsString($ceiling.' ', $errors[0]);
        self::assertStringContainsString($asked.' ', $errors[0]);
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function languages(): iterable
    {
        yield 'French' => ['fr_FR'];
        yield 'English' => ['en_US'];
    }

    #[Test]
    public function frenchAndEnglishDoNotWriteTheAmountTheSameWay(): void
    {
        $french = LangQuery::create()->findOneByLocale('fr_FR');
        $english = LangQuery::create()->findOneByLocale('en_US');

        if (null === $french || null === $english) {
            self::markTestSkipped('French and English are not both in the test database.');
        }

        self::assertNotSame(
            number_format(1411.2, 2, $french->getDecimalSeparator(), $french->getThousandsSeparator()),
            number_format(1411.2, 2, $english->getDecimalSeparator(), $english->getThousandsSeparator()),
            'the test above would pass with a single format otherwise',
        );
    }
}
