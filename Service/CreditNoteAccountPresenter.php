<?php

declare(strict_types=1);

namespace CreditNote\Service;

use CreditNote\Model\CreditNote;
use CreditNote\Model\CreditNoteQuery;
use Propel\Runtime\ActiveQuery\Criteria;
use Symfony\Component\HttpFoundation\RequestStack;
use Thelia\Model\CurrencyQuery;
use Thelia\Tools\MoneyFormat;

/**
 * The credit notes a customer sees in their account: the accepted ones (and the ones already
 * used), with the amount they were issued for and what is left of them. A proposed or
 * refused credit note is an internal draft of the merchant and stays out of the account.
 */
final readonly class CreditNoteAccountPresenter
{
    public function __construct(
        private RequestStack $requestStack,
        private CreditNoteBalance $balance,
    ) {
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function rows(int $customerId, ?int $orderId, string $locale): array
    {
        $query = CreditNoteQuery::create()
            ->filterByCustomerId($customerId)
            ->useCreditNoteStatusQuery()
                ->filterByInvoiced(true)
            ->endUse()
            ->orderByInvoiceDate(Criteria::DESC)
            ->orderById(Criteria::DESC);

        if (null !== $orderId) {
            $query->filterByOrderId($orderId);
        }

        $rows = [];

        foreach ($query->find() as $creditNote) {
            $rows[] = $this->row($creditNote, $locale);
        }

        return $rows;
    }

    /**
     * @return array<string, mixed>
     */
    private function row(CreditNote $creditNote, string $locale): array
    {
        $status = $creditNote->getCreditNoteStatus();
        $type = $creditNote->getCreditNoteType();
        $currencyId = $creditNote->getCurrencyId();

        return [
            'id' => $creditNote->getId(),
            'ref' => $creditNote->getRef(),
            'invoice_ref' => $creditNote->getInvoiceRef(),
            'date' => $creditNote->getInvoiceDate() ?? $creditNote->getCreatedAt(),
            'order_id' => $creditNote->getOrderId(),
            'order_ref' => $creditNote->getOrder()?->getRef(),
            'type_title' => $type?->setLocale($locale)->getTitle() ?? '',
            'status_code' => $status?->getCode() ?? '',
            'status_title' => $status?->setLocale($locale)->getTitle() ?? '',
            'allow_partial_use' => (bool) $creditNote->getAllowPartialUse(),
            'amount' => $this->money((float) $creditNote->getTotalPriceWithTax(), $currencyId),
            'balance' => $this->money($this->balance->remaining($creditNote), $currencyId),
            'balance_value' => $this->balance->remaining($creditNote),
        ];
    }

    private function money(float $amount, ?int $currencyId): string
    {
        $symbol = null !== $currencyId ? (CurrencyQuery::create()->findPk($currencyId)?->getSymbol() ?? '') : '';
        $request = $this->requestStack->getCurrentRequest();

        if (null === $request) {
            return number_format($amount, 2, '.', ' ') . ('' === $symbol ? '' : ' ' . $symbol);
        }

        return MoneyFormat::getInstance($request)->format($amount, null, null, null, $symbol);
    }
}
