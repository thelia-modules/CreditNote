<?php

declare(strict_types=1);

namespace CreditNote\Tests\Integration;

use CreditNote\CreditNote;
use CreditNote\Service\CreditNoteDocumentLocale;
use CreditNote\Service\CreditNotePdfRenderer;
use CreditNote\Tests\Support\CreditNoteFixture;
use PHPUnit\Framework\Attributes\Test;
use Thelia\Core\Template\TemplateDefinition;
use Thelia\Model\ConfigQuery;
use Thelia\Model\LangQuery;
use Thelia\Model\ModuleQuery;
use Thelia\Test\IntegrationTestCase;

/**
 * The language a credit note is printed in is its own, or the one its customer prefers for a
 * credit note written before credit notes had a language: a language the shop prints its credit
 * notes in (French or English unless the `document_locales` setting says otherwise), the
 * default language of the shop otherwise.
 */
final class CreditNoteDocumentLocaleTest extends IntegrationTestCase
{
    private CreditNoteFixture $fixture;

    protected function setUp(): void
    {
        parent::setUp();

        if (!ModuleQuery::create()->findOneByCode(CreditNote::getModuleCode())?->getActivate()) {
            self::markTestSkipped('The CreditNote module is not active in the test database.');
        }

        $this->fixture = new CreditNoteFixture($this->getPropelConnection());

        // The setting as a shop leaves it until it maintains its own list (English and French).
        // Inside the test transaction: rolled back with the rest.
        CreditNote::setConfigValue(CreditNote::CONFIG_KEY_DOCUMENT_LOCALES, '');
    }

    #[Test]
    public function aCustomerWhoPrefersFrenchOrEnglishIsServedInThatLanguage(): void
    {
        $customer = $this->fixture->customer();
        $order = $this->fixture->paidOrder($customer);
        $creditNote = $this->withoutLanguage($this->fixture->creditNote($customer, $order, 'accepted'));

        $customer->setLangId($this->lang('fr_FR')->getId())->save($this->getPropelConnection());
        self::assertSame('fr_FR', $this->locale()->forCreditNote($this->reload($creditNote)));

        $customer->setLangId($this->lang('en_US')->getId())->save($this->getPropelConnection());
        self::assertSame('en_US', $this->locale()->forCreditNote($this->reload($creditNote)));
    }

    #[Test]
    public function anotherPreferredLanguageFallsBackToTheDefaultLanguageOfTheShop(): void
    {
        $customer = $this->fixture->customer();
        $creditNote = $this->withoutLanguage($this->fixture->creditNote($customer, $this->fixture->paidOrder($customer), 'accepted'));

        $other = $this->fixture->factory()->lang(['locale' => 'de_DE', 'code' => 'de', 'title' => 'Deutsch']);
        $customer->setLangId($other->getId())->save($this->getPropelConnection());

        $default = LangQuery::create()->findOneByByDefault(true);
        self::assertNotNull($default, 'the shop has a default language');
        self::assertSame($default->getLocale(), $this->locale()->forCreditNote($this->reload($creditNote)));
    }

    #[Test]
    public function aLanguageTheShopPrintsItsCreditNotesInIsServed(): void
    {
        $customer = $this->fixture->customer();
        $creditNote = $this->withoutLanguage($this->fixture->creditNote($customer, $this->fixture->paidOrder($customer), 'accepted'));

        $german = LangQuery::create()->findOneByLocale('de_DE')
            ?? $this->fixture->factory()->lang(['locale' => 'de_DE', 'code' => 'de', 'title' => 'Deutsch']);
        $customer->setLangId($german->getId())->save($this->getPropelConnection());

        // Inside the test transaction: rolled back with the rest. A shop without demo data has
        // English as its default language: make it French, so that leaving English is visible.
        $this->getPropelConnection()->exec("UPDATE lang SET by_default = (locale = 'fr_FR')");
        CreditNote::setConfigValue(CreditNote::CONFIG_KEY_DOCUMENT_LOCALES, 'fr_FR, de_DE');

        self::assertSame(['fr_FR', 'de_DE'], CreditNote::documentLocales());
        self::assertSame('de_DE', $this->locale()->forCreditNote($this->reload($creditNote)));

        $customer->setLangId($this->lang('en_US')->getId())->save($this->getPropelConnection());
        self::assertSame('fr_FR', $this->locale()->forCreditNote($this->reload($creditNote)), 'English is no longer in the list: the default language of the shop answers');

        CreditNote::setConfigValue(CreditNote::CONFIG_KEY_DOCUMENT_LOCALES, '');
        self::assertSame(CreditNoteDocumentLocale::PREFERRED, CreditNote::documentLocales(), 'empty: English and French');
    }

    #[Test]
    public function aShopWithoutDefaultLanguageFallsBackToEnglish(): void
    {
        $customer = $this->fixture->customer();
        $creditNote = $this->withoutLanguage($this->fixture->creditNote($customer, $this->fixture->paidOrder($customer), 'accepted'));

        $other = $this->fixture->factory()->lang(['locale' => 'de_DE', 'code' => 'de', 'title' => 'Deutsch']);
        $customer->setLangId($other->getId())->save($this->getPropelConnection());

        // Inside the test transaction: rolled back with the rest.
        $this->getPropelConnection()->exec('UPDATE lang SET by_default = 0');

        self::assertSame('en_US', $this->locale()->forCreditNote($this->reload($creditNote)));
    }

    #[Test]
    public function theLanguageOfTheCreditNoteWinsOverTheOneOfItsCustomer(): void
    {
        $customer = $this->fixture->customer();
        $customer->setLangId($this->lang('en_US')->getId())->save($this->getPropelConnection());
        $creditNote = $this->fixture->creditNote($customer, $this->fixture->paidOrder($customer), 'accepted')
            ->setLangId($this->lang('fr_FR')->getId());
        $creditNote->save();

        self::assertSame('fr_FR', $this->locale()->forCreditNote($this->reload($creditNote)));
    }

    #[Test]
    public function theDocumentIsRenderedInTheLanguageOfTheCreditNoteWhateverTheOrderAndTheCustomer(): void
    {
        // The wording checked below is the one of the default PDF template, whichever the shop uses.
        $pdfTemplate = new TemplateDefinition('default', TemplateDefinition::PDF);

        if (!is_file($pdfTemplate->getAbsolutePath() . '/credit-note.html.twig')) {
            self::markTestSkipped('The default PDF template ships no credit note document.');
        }

        $activePdfTemplate = ConfigQuery::read(TemplateDefinition::PDF_CONFIG_NAME, 'default');
        ConfigQuery::write(TemplateDefinition::PDF_CONFIG_NAME, 'default');

        // Customer and order in English, the credit note chosen in French.
        $customer = $this->fixture->customer();
        $customer->setLangId($this->lang('en_US')->getId())->save($this->getPropelConnection());
        $order = $this->fixture->paidOrder($customer);
        $order->setLangId($this->lang('en_US')->getId())->save($this->getPropelConnection());
        $creditNote = $this->fixture->creditNote($customer, $order, 'accepted')->setLangId($this->lang('fr_FR')->getId());
        $creditNote->save();

        try {
            $html = $this->getService(CreditNotePdfRenderer::class)->renderHtml($this->reload($creditNote));
        } finally {
            // The configuration is cached for the whole process: put the shop's value back.
            ConfigQuery::write(TemplateDefinition::PDF_CONFIG_NAME, $activePdfTemplate);
        }

        self::assertStringContainsString('AVOIR', $html);
        self::assertStringContainsString('Retour produit', $html, 'the type is named in the language of the document');
        self::assertStringNotContainsString('CREDIT NOTE', $html);
    }

    private function locale(): CreditNoteDocumentLocale
    {
        return $this->getService(CreditNoteDocumentLocale::class);
    }

    private function lang(string $locale): \Thelia\Model\Lang
    {
        return LangQuery::create()->findOneByLocale($locale)
            ?? self::markTestSkipped(\sprintf('The %s language is not in the test database.', $locale));
    }

    /**
     * Saved, then stripped of the language it got on insert: a credit note written before
     * credit notes had a language.
     */
    private function withoutLanguage(\CreditNote\Model\CreditNote $creditNote): \CreditNote\Model\CreditNote
    {
        $creditNote->save();
        $creditNote->setLangId(null)->save();

        return $creditNote;
    }

    private function reload(\CreditNote\Model\CreditNote $creditNote): \CreditNote\Model\CreditNote
    {
        // The customer was changed through another object: read the credit note again so its
        // relations are fetched fresh (instance pooling is off in the integration suite).
        return \CreditNote\Model\CreditNoteQuery::create()->findPk($creditNote->getId());
    }
}
