<?php

declare(strict_types=1);

namespace CreditNote\Tests\Unit;

use CreditNote\Helper\CriteriaSearchHelper;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * The regular expression the customer and order search boxes of the back-office build
 * from what the administrator typed.
 */
final class CriteriaSearchHelperTest extends TestCase
{
    #[Test]
    public function aSingleWordMatchesAnywhereInTheSearchedColumns(): void
    {
        self::assertSame('.*Diallo.*', $this->helper()->getRegex('Diallo'));
    }

    #[Test]
    public function severalWordsMatchInBothOrders(): void
    {
        self::assertSame('.*Mohamed.+Diallo.*|.*Diallo.+Mohamed.*', $this->helper()->getRegex('Mohamed Diallo'));
    }

    #[Test]
    public function shortWordsAndPunctuationAreIgnored(): void
    {
        self::assertSame('.*Diallo.*', $this->helper()->getRegex('M. de Diallo'), 'a two-letter word and a word with a dot are not searched');
        self::assertSame('.*ORD000000000065.*', $this->helper()->getRegex('  ORD000000000065  '));
    }

    #[Test]
    public function aSearchWithoutUsableWordYieldsNoExpression(): void
    {
        self::assertNull($this->helper()->getRegex(''));
        self::assertNull($this->helper()->getRegex('a b c'));
        self::assertNull($this->helper()->getRegex("' OR 1=1 --"), 'nothing but letters and digits reaches the SQL REGEXP');
    }

    private function helper(): object
    {
        return new class {
            use CriteriaSearchHelper;
        };
    }
}
