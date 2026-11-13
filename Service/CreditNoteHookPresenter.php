<?php

declare(strict_types=1);

namespace CreditNote\Service;

use CreditNote\Helper\BadgeColor;
use CreditNote\Model\CreditNoteQuery;
use CreditNote\Model\CreditNoteStatusQuery;
use CreditNote\Model\CreditNoteDetailQuery;
use CreditNote\Model\OrderCreditNoteQuery;
use Propel\Runtime\ActiveQuery\Criteria;
use Thelia\Model\OrderQuery;

/**
 * Reproduces, in plain PHP, the Smarty {loop} data the back-office hook templates
 * relied on (the Twig BO has no {loop}/resources() helper).
 */
final class CreditNoteHookPresenter
{
    public function __construct(
        private readonly LocalizedMoney $money,
    ) {
    }

    private function formatMoney(float|string|null $amount, ?int $currencyId): string
    {
        if ($amount === null) {
            return '';
        }

        return $this->money->format((float) $amount, $currencyId);
    }

    public function countForOrder(int $orderId): int
    {
        return CreditNoteQuery::create()->filterByOrderId($orderId)->count();
    }

    /**
     * The credit notes that paid part of an order, with what each one paid, and what they left
     * to the payment provider (null when no credit note was used).
     *
     * @return array{rows: list<array{ref:string,credit_note_id:int,amount:string}>, remaining: ?string}
     */
    public function creditNotesUsedOnOrder(int $orderId): array
    {
        $rows = [];
        $paid = 0.0;
        $currencyId = null;

        foreach (OrderCreditNoteQuery::create()->filterByOrderId($orderId)->orderByCreditNoteId()->find() as $link) {
            $creditNote = CreditNoteQuery::create()->findPk($link->getCreditNoteId());
            if (null === $creditNote) {
                continue;
            }
            $currencyId ??= $creditNote->getCurrencyId();
            $paid += (float) $link->getAmountPrice();
            $rows[] = [
                'ref' => (string) $creditNote->getRef(),
                'credit_note_id' => (int) $creditNote->getId(),
                'amount' => $this->formatMoney($link->getAmountPrice(), $creditNote->getCurrencyId()),
            ];
        }

        if ([] === $rows) {
            return ['rows' => [], 'remaining' => null];
        }

        $orderTotal = OrderQuery::create()->findPk($orderId)?->getTotalAmount() ?? 0.0;

        return ['rows' => $rows, 'remaining' => $this->formatMoney(max(0.0, round($orderTotal - $paid, 2)), $currencyId)];
    }

    /**
     * Credit-note details attached to an order product, with the status badge resolved.
     *
     * @return array<int, array<string, mixed>>
     */
    public function detailRowsForOrderProduct(int $orderProductId, string $locale): array
    {
        $details = CreditNoteDetailQuery::create()
            ->filterByQuantity(0, Criteria::GREATER_THAN)
            ->filterByOrderProductId($orderProductId)
            ->find();

        $rows = [];
        foreach ($details as $detail) {
            if ($detail->getQuantity() <= 0) {
                continue;
            }
            $creditNote = $detail->getCreditNote();
            $status = $creditNote?->getCreditNoteStatus();

            $rows[] = [
                'credit_note_id' => $creditNote?->getId(),
                'ref' => $creditNote?->getRef(),
                'status_title' => $status?->setLocale($locale)->getTitle() ?? '',
                'status_color' => BadgeColor::css($status?->getColor()),
                'quantity' => $detail->getQuantity(),
            ];
        }

        return $rows;
    }

    /**
     * Menu entries: one per credit-note status that has at least one credit note,
     * plus the colour/title for the badge.
     *
     * @return array<int, array{id:int,title:string,color:string,count:int}>
     */
    public function menuStatuses(string $locale): array
    {
        $statuses = CreditNoteStatusQuery::create()->orderByPosition()->find();

        $entries = [];
        foreach ($statuses as $status) {
            $count = CreditNoteQuery::create()->filterByStatusId($status->getId())->count();
            if ($count === 0) {
                continue;
            }
            $entries[] = [
                'id' => $status->getId(),
                'title' => $status->setLocale($locale)->getTitle(),
                'color' => BadgeColor::css($status->getColor()),
                'count' => $count,
            ];
        }

        return $entries;
    }

    public function totalCount(): int
    {
        return CreditNoteQuery::create()->count();
    }
}
