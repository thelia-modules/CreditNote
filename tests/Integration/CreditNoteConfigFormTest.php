<?php

declare(strict_types=1);

namespace CreditNote\Tests\Integration;

use CreditNote\CreditNote;
use CreditNote\Form\CreditNoteConfigForm;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Thelia\Core\Form\TheliaFormFactory;
use Thelia\Model\ModuleQuery;
use Thelia\Test\IntegrationTestCase;

/**
 * The `document_locales` field only accepts a list of locales (`fr_FR, de_DE`), as the credit note document looks
 * its templates up by locale: any other text is refused with the form.
 */
final class CreditNoteConfigFormTest extends IntegrationTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        if (!ModuleQuery::create()->findOneByCode(CreditNote::getModuleCode())?->getActivate()) {
            self::markTestSkipped('The CreditNote module is not active in the test database.');
        }
    }

    #[Test]
    #[DataProvider('acceptedValues')]
    public function aListOfLocalesIsAccepted(string $value): void
    {
        self::assertSame(0, $this->errorsOfDocumentLocales($value));
    }

    #[Test]
    #[DataProvider('refusedValues')]
    public function anythingElseIsRefused(string $value): void
    {
        self::assertGreaterThan(0, $this->errorsOfDocumentLocales($value));
    }

    /**
     * @return iterable<string, array{0: string}>
     */
    public static function acceptedValues(): iterable
    {
        yield 'empty (English and French)' => [''];
        yield 'one locale' => ['de_DE'];
        yield 'several, spaced or not' => ['fr_FR, de_DE,en_US'];
    }

    /**
     * @return iterable<string, array{0: string}>
     */
    public static function refusedValues(): iterable
    {
        yield 'a language name' => ['french'];
        yield 'a code without region' => ['fr'];
        yield 'a dash' => ['fr-FR'];
        yield 'a trailing comma' => ['fr_FR,'];
        yield 'a wrong case' => ['FR_fr'];
        yield 'a locale and a word' => ['fr_FR, german'];
    }

    private function errorsOfDocumentLocales(string $value): int
    {
        $form = static::getContainer()->get(TheliaFormFactory::class)
            ->createForm(CreditNoteConfigForm::getName(), options: ['csrf_protection' => false])
            ->getForm();
        $form->submit([CreditNote::CONFIG_KEY_DOCUMENT_LOCALES => $value]);

        return \count($form->get(CreditNote::CONFIG_KEY_DOCUMENT_LOCALES)->getErrors());
    }
}
