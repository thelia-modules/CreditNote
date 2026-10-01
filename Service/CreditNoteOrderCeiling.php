<?php

declare(strict_types=1);

namespace CreditNote\Service;

use CreditNote\CreditNote as CreditNoteModule;
use CreditNote\Exception\CreditNoteExceedsOrderException;
use CreditNote\Model\CreditNote;
use CreditNote\Model\CreditNoteQuery;
use Propel\Runtime\ActiveQuery\Criteria;

/**
 * The credit notes of an order never refund more than the order was worth: what the other
 * credit notes of the order already grant, plus the one being written, stays within the
 * total of the order (postage and discount included). A refused credit note grants nothing. The
 * shop can turn the ceiling off (`order_ceiling` setting of the module).
 */
final readonly class CreditNoteOrderCeiling
{
    private const REFUSED = 'refused';

    public function assertWithinOrder(CreditNote $creditNote): void
    {
        if (!CreditNoteModule::isOrderCeilingEnforced()) {
            return;
        }

        $order = $creditNote->getOrder();

        if (null === $order || self::REFUSED === $creditNote->getCreditNoteStatus()?->getCode()) {
            return;
        }

        $ceiling = round($order->getTotalAmount(), 2);
        $granted = $this->grantedByOthers($creditNote);
        $asked = round((float) $creditNote->getTotalPriceWithTax(), 2);

        if ($granted + $asked > $ceiling + 0.005) {
            throw new CreditNoteExceedsOrderException((string) $order->getRef(), $ceiling, $granted, $asked);
        }
    }

    /**
     * The total with tax the other credit notes of the order grant, refused ones left out.
     */
    public function grantedByOthers(CreditNote $creditNote): float
    {
        $query = CreditNoteQuery::create()
            ->filterByOrderId($creditNote->getOrderId())
            ->useCreditNoteStatusQuery()
                ->filterByCode(self::REFUSED, Criteria::NOT_EQUAL)
            ->endUse();

        if (null !== $creditNote->getId()) {
            $query->filterById($creditNote->getId(), Criteria::NOT_EQUAL);
        }

        $granted = $query
            ->withColumn('COALESCE(SUM(credit_note.total_price_with_tax), 0)', 'granted_amount')
            ->select(['granted_amount'])
            ->findOne();

        return round((float) $granted, 2);
    }
}
