<?php

declare(strict_types=1);

namespace CreditNote\Service;

use CreditNote\CreditNote;
use CreditNote\Model\CreditNote as CreditNoteModel;
use Thelia\Model\Lang;
use Thelia\Model\LangQuery;

/**
 * The language a credit note is printed in: the one its customer prefers when the shop prints
 * its credit notes in that language (`document_locales` setting of the module, English and
 * French by default), otherwise the default language of the shop, and English when the shop
 * has none.
 */
final readonly class CreditNoteDocumentLocale
{
    public const FALLBACK = 'en_US';

    /** @var list<string> the languages the document is printed in when the shop names none */
    public const PREFERRED = ['en_US', 'fr_FR'];

    public function forCreditNote(CreditNoteModel $creditNote): string
    {
        $customerLocale = $this->customerLocale($creditNote);

        if (null !== $customerLocale && \in_array($customerLocale, CreditNote::documentLocales(), true)) {
            return $customerLocale;
        }

        $defaultLocale = $this->defaultLocale();

        return null !== $defaultLocale && '' !== $defaultLocale ? $defaultLocale : self::FALLBACK;
    }

    private function customerLocale(CreditNoteModel $creditNote): ?string
    {
        $langId = $creditNote->getCustomer()?->getLangId();

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
