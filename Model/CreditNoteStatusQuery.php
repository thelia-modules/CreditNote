<?php

namespace CreditNote\Model;

use CreditNote\Model\Base\CreditNoteStatusQuery as BaseCreditNoteStatusQuery;
use Propel\Runtime\ActiveQuery\Criteria;

/**
 * Skeleton subclass for performing query and update operations on the 'credit_note_status' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 */
class CreditNoteStatusQuery extends BaseCreditNoteStatusQuery
{
    /**
     * @param CreditNoteStatus $creditNoteStatus
     * @return CreditNoteStatus|null
     */
    public static function findNextCreditNoteUsedStatus(CreditNoteStatus $creditNoteStatus)
    {
        /** @var CreditNoteStatusFlow $statusFlow */
        $statusFlow = CreditNoteStatusFlowQuery::create()
            ->filterByFromStatusId($creditNoteStatus->getId())
            ->orderByPriority(Criteria::ASC)
            ->useCreditNoteStatusRelatedByToStatusIdQuery()
            ->filterByUsed(true)
            ->endUse()
            ->findOne();

        if (null === $statusFlow) {
            return null;
        }

        return $statusFlow->getCreditNoteStatusRelatedByToStatusId();
    }

    /**
     * @param CreditNoteStatus $creditNoteStatus
     * @return CreditNoteStatus|null
     */
    public static function findNextCreditNoteStatus(CreditNoteStatus $creditNoteStatus)
    {
        /** @var CreditNoteStatusFlow $statusFlow */
        $statusFlow = CreditNoteStatusFlowQuery::create()
            ->filterByFromStatusId($creditNoteStatus->getId())
            ->orderByPriority(Criteria::ASC)
            ->useCreditNoteStatusRelatedByToStatusIdQuery()
            ->filterByUsed($creditNoteStatus->getUsed())
            ->endUse()
            ->findOne();

        if (null === $statusFlow) {
            return null;
        }

        return $statusFlow->getCreditNoteStatusRelatedByToStatusId();
    }
}
