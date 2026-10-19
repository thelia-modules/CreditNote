<?php

declare(strict_types=1);

namespace CreditNote\Tests\Integration;

use CreditNote\CreditNote;
use CreditNote\Model\CreditNoteQuery;
use CreditNote\Service\CreditNoteLanguage;
use CreditNote\Tests\Support\CreditNoteFixture;
use PHPUnit\Framework\Attributes\Test;
use Thelia\Model\Lang;
use Thelia\Model\LangQuery;
use Thelia\Model\ModuleQuery;
use Thelia\Test\IntegrationTestCase;

/**
 * A credit note gets a language when it is written: the one of its order, of its customer
 * when it has no order, the default language of the shop otherwise. The back-office offers the
 * active languages, plus the one the credit note already has.
 */
final class CreditNoteLanguageTest extends IntegrationTestCase
{
    private CreditNoteFixture $fixture;

    protected function setUp(): void
    {
        parent::setUp();

        if (!ModuleQuery::create()->findOneByCode(CreditNote::getModuleCode())?->getActivate()) {
            self::markTestSkipped('The CreditNote module is not active in the test database.');
        }

        $this->fixture = new CreditNoteFixture($this->getPropelConnection());
    }

    #[Test]
    public function aCreditNoteOnAnOrderTakesTheLanguageOfTheOrder(): void
    {
        $customer = $this->fixture->customer();
        $customer->setLangId($this->lang('fr_FR')->getId())->save($this->getPropelConnection());
        $order = $this->fixture->paidOrder($customer);
        $order->setLangId($this->german()->getId())->save($this->getPropelConnection());

        $creditNote = $this->fixture->creditNote($customer, $order);
        $creditNote->save();

        self::assertSame($this->german()->getId(), CreditNoteQuery::create()->findPk($creditNote->getId())->getLangId());
    }

    #[Test]
    public function aCreditNoteWithoutOrderTakesTheLanguageOfTheCustomer(): void
    {
        $customer = $this->fixture->customer();
        $customer->setLangId($this->german()->getId())->save($this->getPropelConnection());

        $creditNote = $this->fixture->creditNote($customer, null);
        $creditNote->save();

        self::assertSame($this->german()->getId(), CreditNoteQuery::create()->findPk($creditNote->getId())->getLangId());
    }

    #[Test]
    public function aLanguageGivenByTheWriterIsKept(): void
    {
        $customer = $this->fixture->customer();
        $order = $this->fixture->paidOrder($customer);
        $order->setLangId($this->lang('fr_FR')->getId())->save($this->getPropelConnection());

        $creditNote = $this->fixture->creditNote($customer, $order)->setLangId($this->german()->getId());
        $creditNote->save();

        self::assertSame($this->german()->getId(), CreditNoteQuery::create()->findPk($creditNote->getId())->getLangId());
    }

    #[Test]
    public function withoutOrderNorCustomerLanguageTheDefaultLanguageOfTheShopAnswers(): void
    {
        $customer = $this->fixture->customer();
        $creditNote = $this->fixture->creditNote($customer, null);
        // A customer row always has a language in practice; the service is asked directly.
        $creditNote->getCustomer()->setLangId(null);

        $default = LangQuery::create()->findOneByByDefault(true);
        self::assertNotNull($default, 'the shop has a default language');
        self::assertSame($default->getId(), $this->language()->defaultFor($creditNote)?->getId());
    }

    #[Test]
    public function theActiveLanguagesAndTheOwnLanguageOfTheCreditNoteAreSelectable(): void
    {
        $customer = $this->fixture->customer();
        $creditNote = $this->fixture->creditNote($customer, $this->fixture->paidOrder($customer));
        $creditNote->save();

        // Inside the test transaction: rolled back with the rest.
        $inactive = LangQuery::create()->findOneByLocale('it_IT')
            ?? $this->fixture->factory()->lang(['locale' => 'it_IT', 'code' => 'it', 'title' => 'Italiano']);
        $inactive->setActive(false)->setByDefault(0)->save($this->getPropelConnection());

        self::assertFalse($this->language()->isSelectable($creditNote, $inactive->getId()), 'an inactive language is refused');
        self::assertFalse($this->language()->isSelectable($creditNote, 999999), 'an unknown language is refused');

        foreach (LangQuery::create()->filterByActive(true)->find() as $active) {
            self::assertTrue($this->language()->isSelectable($creditNote, $active->getId()));
        }

        // A credit note printed in a language the shop turned off since keeps it.
        $creditNote->setLangId($inactive->getId())->save($this->getPropelConnection());
        self::assertTrue($this->language()->isSelectable($creditNote, $inactive->getId()));
    }

    private function language(): CreditNoteLanguage
    {
        return $this->getService(CreditNoteLanguage::class);
    }

    private function german(): Lang
    {
        // Active: a shop without demo data may have German turned off, or not at all.
        $german = LangQuery::create()->findOneByLocale('de_DE')
            ?? $this->fixture->factory()->lang(['locale' => 'de_DE', 'code' => 'de', 'title' => 'Deutsch']);

        if (!$german->getActive()) {
            $german->setActive(true)->save($this->getPropelConnection());
        }

        return $german;
    }

    private function lang(string $locale): Lang
    {
        return LangQuery::create()->findOneByLocale($locale)
            ?? self::markTestSkipped(\sprintf('The %s language is not in the test database.', $locale));
    }
}
