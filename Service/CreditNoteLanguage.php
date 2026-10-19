<?php

declare(strict_types=1);

namespace CreditNote\Service;

use CreditNote\Model\CreditNote as CreditNoteModel;
use Thelia\Model\Lang;
use Thelia\Model\LangQuery;

/**
 * The language of a credit note, chosen in the back-office: the language of its order by
 * default, of its customer when it has no order, and the default language of the shop
 * otherwise. Any active language can be chosen; the language a credit note already has stays
 * selectable even once inactive, so saving it again never changes it.
 */
final readonly class CreditNoteLanguage
{
    public function defaultFor(CreditNoteModel $creditNote): ?Lang
    {
        $langId = $creditNote->getOrder()?->getLangId() ?? $creditNote->getCustomer()?->getLangId();

        if (null !== $langId && null !== $lang = LangQuery::create()->findPk($langId)) {
            return $lang;
        }

        return LangQuery::create()->findOneByByDefault(true);
    }

    /**
     * @return list<Lang>
     */
    public function selectable(CreditNoteModel $creditNote): array
    {
        $query = LangQuery::create()->filterByActive(true);

        if (null !== $creditNote->getLangId()) {
            $query->_or()->filterById($creditNote->getLangId());
        }

        return iterator_to_array($query->orderByPosition()->find(), false);
    }

    public function isSelectable(CreditNoteModel $creditNote, int $langId): bool
    {
        foreach ($this->selectable($creditNote) as $lang) {
            if ($lang->getId() === $langId) {
                return true;
            }
        }

        return false;
    }
}
