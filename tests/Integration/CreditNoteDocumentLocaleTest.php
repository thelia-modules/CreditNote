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
 * The language a credit note is printed in follows its customer: French or English when
 * that is what the customer prefers, the default language of the shop otherwise.
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
    }

    #[Test]
    public function aCustomerWhoPrefersFrenchOrEnglishIsServedInThatLanguage(): void
    {
        $customer = $this->fixture->customer();
        $order = $this->fixture->paidOrder($customer);
        $creditNote = $this->fixture->creditNote($customer, $order, 'accepted');
        $creditNote->save();

        $customer->setLangId($this->lang('fr_FR')->getId())->save($this->getPropelConnection());
        self::assertSame('fr_FR', $this->locale()->forCreditNote($this->reload($creditNote)));

        $customer->setLangId($this->lang('en_US')->getId())->save($this->getPropelConnection());
        self::assertSame('en_US', $this->locale()->forCreditNote($this->reload($creditNote)));
    }

    #[Test]
    public function anotherPreferredLanguageFallsBackToTheDefaultLanguageOfTheShop(): void
    {
        $customer = $this->fixture->customer();
        $creditNote = $this->fixture->creditNote($customer, $this->fixture->paidOrder($customer), 'accepted');
        $creditNote->save();

        $other = $this->fixture->factory()->lang(['locale' => 'de_DE', 'code' => 'de', 'title' => 'Deutsch']);
        $customer->setLangId($other->getId())->save($this->getPropelConnection());

        $default = LangQuery::create()->findOneByByDefault(true);
        self::assertNotNull($default, 'the shop has a default language');
        self::assertSame($default->getLocale(), $this->locale()->forCreditNote($this->reload($creditNote)));
    }

    #[Test]
    public function aShopWithoutDefaultLanguageFallsBackToEnglish(): void
    {
        $customer = $this->fixture->customer();
        $creditNote = $this->fixture->creditNote($customer, $this->fixture->paidOrder($customer), 'accepted');
        $creditNote->save();

        $other = $this->fixture->factory()->lang(['locale' => 'de_DE', 'code' => 'de', 'title' => 'Deutsch']);
        $customer->setLangId($other->getId())->save($this->getPropelConnection());

        // Inside the test transaction: rolled back with the rest.
        $this->getPropelConnection()->exec('UPDATE lang SET by_default = 0');

        self::assertSame('en_US', $this->locale()->forCreditNote($this->reload($creditNote)));
    }

    #[Test]
    public function theDocumentIsRenderedInTheLanguageOfTheCustomerWhateverTheOrder(): void
    {
        $pdfTemplate = new TemplateDefinition(ConfigQuery::read(TemplateDefinition::PDF_CONFIG_NAME, 'default'), TemplateDefinition::PDF);

        if (!is_file($pdfTemplate->getAbsolutePath() . '/credit-note.html.twig')) {
            self::markTestSkipped(\sprintf('The active PDF template (%s) ships no credit note document.', $pdfTemplate->getName()));
        }

        $customer = $this->fixture->customer();
        $customer->setLangId($this->lang('fr_FR')->getId())->save($this->getPropelConnection());
        // The order was placed in English.
        $order = $this->fixture->paidOrder($customer);
        $order->setLangId($this->lang('en_US')->getId())->save($this->getPropelConnection());
        $creditNote = $this->fixture->creditNote($customer, $order, 'accepted');
        $creditNote->save();

        $html = $this->getService(CreditNotePdfRenderer::class)->renderHtml($this->reload($creditNote));

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

    private function reload(\CreditNote\Model\CreditNote $creditNote): \CreditNote\Model\CreditNote
    {
        // The customer was changed through another object: read the credit note again so its
        // relations are fetched fresh (instance pooling is off in the integration suite).
        return \CreditNote\Model\CreditNoteQuery::create()->findPk($creditNote->getId());
    }
}
