<?php

declare(strict_types=1);

namespace CreditNote\Service;

use CreditNote\Model\CreditNote;
use Thelia\Model\Lang;
use Thelia\Model\LangQuery;

/**
 * The language a credit note is printed in: the one its customer prefers when the module
 * documents it in that language, otherwise the default language of the shop, and English
 * when the shop has none.
 */
final readonly class CreditNoteDocumentLocale
{
    public const FALLBACK = 'en_US';

    /** @var list<string> the languages the document is maintained in */
    public const PREFERRED = ['en_US', 'fr_FR'];

    public function forCreditNote(CreditNote $creditNote): string
    {
        $customerLocale = $this->customerLocale($creditNote);

        if (null !== $customerLocale && \in_array($customerLocale, self::PREFERRED, true)) {
            return $customerLocale;
        }

        $defaultLocale = $this->defaultLocale();

        return null !== $defaultLocale && '' !== $defaultLocale ? $defaultLocale : self::FALLBACK;
    }

    private function customerLocale(CreditNote $creditNote): ?string
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
