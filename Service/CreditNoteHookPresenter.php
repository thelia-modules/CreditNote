<?php

declare(strict_types=1);

namespace CreditNote\Service;

use CreditNote\Model\CreditNoteQuery;
use CreditNote\Model\CreditNoteStatusQuery;
use CreditNote\Model\CreditNoteDetailQuery;
use CreditNote\Model\OrderCreditNoteQuery;
use Propel\Runtime\ActiveQuery\Criteria;
use Symfony\Component\HttpFoundation\RequestStack;
use Thelia\Model\CurrencyQuery;
use Thelia\Tools\MoneyFormat;

/**
 * Reproduces, in plain PHP, the Smarty {loop} data the back-office hook templates
 * relied on (the Twig BO has no {loop}/resources() helper).
 */
final class CreditNoteHookPresenter
{
    public function __construct(
        private readonly RequestStack $requestStack,
    ) {
    }

    private function formatMoney(float|string|null $amount, ?int $currencyId): string
    {
        if ($amount === null) {
            return '';
        }

        $symbol = '';
        if (null !== $currencyId && null !== $currency = CurrencyQuery::create()->findPk($currencyId)) {
            $symbol = $currency->getSymbol();
        }

        return MoneyFormat::getInstance($this->requestStack->getCurrentRequest())
            ->format((float) $amount, null, null, null, $symbol);
    }
    /**
     * Rows for the credit-note table rendered on the order and customer edit pages.
     *
     * @return array<int, array<string, mixed>>
     */
    public function tableRows(?int $customerId, ?int $orderId, string $locale): array
    {
        $query = CreditNoteQuery::create()->orderById(Criteria::DESC);

        if (null !== $customerId) {
            $query->filterByCustomerId($customerId);
        }
        if (null !== $orderId) {
            $query->filterByOrderId($orderId);
        }

        $rows = [];
        foreach ($query->find() as $creditNote) {
            $status = $creditNote->getCreditNoteStatus();
            $type = $creditNote->getCreditNoteType();
            $customer = $creditNote->getCustomer();
            $order = $creditNote->getOrder();

            $rows[] = [
                'id' => $creditNote->getId(),
                'ref' => $creditNote->getRef(),
                'invoice_ref' => $creditNote->getInvoiceRef(),
                'invoice_date' => $creditNote->getInvoiceDate(),
                'create_date' => $creditNote->getCreatedAt(),
                'update_date' => $creditNote->getUpdatedAt(),
                'customer_id' => $creditNote->getCustomerId(),
                'customer_name' => $customer ? trim($customer->getFirstname().' '.$customer->getLastname()) : '',
                'order_id' => $creditNote->getOrderId(),
                'order_ref' => $order?->getRef(),
                'currency_id' => $creditNote->getCurrencyId(),
                'status_title' => $status?->setLocale($locale)->getTitle() ?? '',
                'status_color' => $status?->getColor() ?? '#777',
                'type_title' => $type?->setLocale($locale)->getTitle() ?? '',
                'type_color' => $type?->getColor() ?? '#777',
                'total_price' => $this->formatMoney($creditNote->getTotalPrice(), $creditNote->getCurrencyId()),
                'total_price_with_tax' => $this->formatMoney($creditNote->getTotalPriceWithTax(), $creditNote->getCurrencyId()),
            ];
        }

        return $rows;
    }

    public function countForOrder(int $orderId): int
    {
        return CreditNoteQuery::create()->filterByOrderId($orderId)->count();
    }

    /**
     * Credit notes used as payment on an order (replaces the {loop type="order-credit-note"} nest).
     *
     * @return array<int, array{ref:string,credit_note_id:int,amount:string}>
     */
    public function creditNotesUsedOnOrder(int $orderId): array
    {
        $links = OrderCreditNoteQuery::create()->filterByOrderId($orderId)->find();

        $rows = [];
        foreach ($links as $link) {
            $creditNote = CreditNoteQuery::create()->findPk($link->getCreditNoteId());
            if (null === $creditNote) {
                continue;
            }
            $rows[] = [
                'ref' => $creditNote->getRef(),
                'credit_note_id' => $creditNote->getId(),
                'amount' => $this->formatMoney($link->getAmountPrice(), $creditNote->getCurrencyId()),
            ];
        }

        return $rows;
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
                'status_color' => $status?->getColor() ?? '#777',
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
                'color' => $status->getColor(),
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
