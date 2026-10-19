<?php

declare(strict_types=1);

namespace CreditNote\Service;

use CreditNote\CreditNote;
use CreditNote\Model\CreditNote as CreditNoteModel;
use Thelia\Model\Lang;
use Thelia\Model\LangQuery;

/**
 * The language a credit note is printed in: its own language (chosen in the back-office, see
 * CreditNoteLanguage), or the one its customer prefers for a credit note without one, when the
 * shop prints its credit notes in that language (`document_locales` setting of the module,
 * English and French by default); otherwise the default language of the shop, and English
 * when the shop has none.
 */
final readonly class CreditNoteDocumentLocale
{
    public const FALLBACK = 'en_US';

    /** @var list<string> the languages the document is printed in when the shop names none */
    public const PREFERRED = ['en_US', 'fr_FR'];

    public function forCreditNote(CreditNoteModel $creditNote): string
    {
        $requestedLocale = $this->requestedLocale($creditNote);

        if (null !== $requestedLocale && \in_array($requestedLocale, CreditNote::documentLocales(), true)) {
            return $requestedLocale;
        }

        $defaultLocale = $this->defaultLocale();

        return null !== $defaultLocale && '' !== $defaultLocale ? $defaultLocale : self::FALLBACK;
    }

    private function requestedLocale(CreditNoteModel $creditNote): ?string
    {
        $langId = $creditNote->getLangId() ?? $creditNote->getCustomer()?->getLangId();

        if (null === $langId) {
            return null;
        }

        return LangQuery::create()->findPk($langId)?->getLocale();
    }

    private function defaultLocale(): ?string
    {
        // Read rather than Lang::getDefaultLanguage(): that one caches the first answer for the
        // whole process, and the default language of a shop can change under a long process.
        return LangQuery::create()->findOneByByDefault(true)?->getLocale();
    }
}
