<?php

declare(strict_types=1);

namespace CreditNote\Service;

use CreditNote\Helper\CreditNoteHelper;
use CreditNote\Model\CreditNote;
use CreditNote\Model\CreditNoteCommentQuery;
use CreditNote\Model\CreditNoteStatusQuery;
use CreditNote\Model\CreditNoteTypeQuery;
use CreditNote\Model\OrderCreditNoteQuery;
use Propel\Runtime\ActiveQuery\Criteria;
use Symfony\Component\HttpFoundation\RequestStack;
use Thelia\Model\AddressQuery;
use Thelia\Model\AdminQuery;
use Thelia\Model\CountryQuery;
use Thelia\Model\CurrencyQuery;
use Thelia\Model\CustomerTitleQuery;
use Thelia\Model\OrderProductQuery;
use Thelia\Model\OrderProductTax;
use Thelia\Model\OrderQuery;
use Thelia\Model\TaxRuleQuery;
use Thelia\Tools\MoneyFormat;

/**
 * Supplies, in plain PHP, every option list / computed total the Smarty
 * ajax/credit-note-modal.html relied on through {loop} / {format_money}. The Twig BO
 * has no {loop} or money helper, so all of it is pre-resolved here for the template.
 */
final class CreditNoteModalPresenter
{
    public function __construct(
        private readonly RequestStack $requestStack,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function build(CreditNote $creditNote, string $locale): array
    {
        $currencyId = $creditNote->getCurrencyId();
        $customer = $creditNote->getCustomer();
        $order = $creditNote->getOrder();
        $type = $creditNote->getCreditNoteType();
        $invoiced = !empty($creditNote->getInvoiceRef());

        return [
            'cn' => [
                'id' => $creditNote->getId(),
                'ref' => $creditNote->getRef(),
                'invoice_ref' => $creditNote->getInvoiceRef(),
                'invoiced' => $invoiced,
                'customer_id' => $creditNote->getCustomerId(),
                'order_id' => $creditNote->getOrderId(),
                'status_id' => $creditNote->getStatusId(),
                'type_id' => $creditNote->getTypeId(),
                'type_code' => $type?->getCode(),
                'type_required_order' => $type !== null && (bool) $type->getRequiredOrder(),
                'currency_symbol' => $this->currencySymbol($currencyId),
                'discount_without_tax' => $creditNote->getDiscountWithoutTax(),
                'discount_with_tax' => $creditNote->getDiscountWithTax(),
                'order_discount' => $order?->getDiscount() ?? 0,
                'total_price' => $this->money($creditNote->getTotalPrice(), $currencyId),
                'taxes' => $this->money((float) $creditNote->getTotalPriceWithTax() - (float) $creditNote->getTotalPrice(), $currencyId),
                'total_price_with_tax' => $this->money($creditNote->getTotalPriceWithTax(), $currencyId),
                'address' => $this->address($creditNote),
                'comment' => '',
            ],
            'currencies' => $this->currencies(),
            'statuses' => $this->statuses($locale),
            'types' => $this->types($locale),
            'customer_option' => $customer !== null
                ? ['id' => $customer->getId(), 'label' => sprintf('%s : (%s %s)', $customer->getRef(), $customer->getFirstname(), $customer->getLastname())]
                : null,
            'order_options' => $this->orderOptions($creditNote),
            'addresses' => $this->customerAddresses($creditNote),
            'titles' => $this->titles($locale),
            'countries' => $this->countries($locale),
            'tax_rules' => $this->taxRules($locale),
            'order_block' => $order !== null ? $this->orderBlock($creditNote, $locale) : null,
            'free_amounts' => $this->freeAmounts($creditNote),
            'comments' => $this->comments($creditNote),
            'used_by_orders' => $invoiced ? $this->usedByOrders($creditNote) : [],
        ];
    }

    private function money(float|string|null $amount, ?int $currencyId): string
    {
        if ($amount === null) {
            return '';
        }

        return MoneyFormat::getInstance($this->requestStack->getCurrentRequest())
            ->format((float) $amount, null, null, null, $this->currencySymbol($currencyId));
    }

    private function currencySymbol(?int $currencyId): string
    {
        if (null !== $currencyId && null !== $currency = CurrencyQuery::create()->findPk($currencyId)) {
            return $currency->getSymbol();
        }
        if (null !== $default = CurrencyQuery::create()->findOneByByDefault(true)) {
            return $default->getSymbol();
        }

        return '';
    }

    /**
     * @return array<int, array{id:int,symbol:string}>
     */
    private function currencies(): array
    {
        $out = [];
        foreach (CurrencyQuery::create()->find() as $currency) {
            $out[] = ['id' => $currency->getId(), 'symbol' => $currency->getSymbol()];
        }

        return $out;
    }

    /**
     * @return array<int, array{id:int,title:string,color:string}>
     */
    private function statuses(string $locale): array
    {
        $out = [];
        foreach (CreditNoteStatusQuery::create()->orderByPosition()->find() as $status) {
            $title = $status->setLocale($locale)->getTitle();
            $out[] = [
                'id' => $status->getId(),
                'title' => $title !== null && $title !== '' ? $title : $status->getCode(),
                'color' => $status->getColor() ?? '#777',
            ];
        }

        return $out;
    }

    /**
     * @return array<int, array{id:int,title:string,color:string}>
     */
    private function types(string $locale): array
    {
        $out = [];
        foreach (CreditNoteTypeQuery::create()->find() as $type) {
            $title = $type->setLocale($locale)->getTitle();
            $out[] = [
                'id' => $type->getId(),
                'title' => $title !== null && $title !== '' ? $title : $type->getCode(),
                'color' => $type->getColor() ?? '#777',
            ];
        }

        return $out;
    }

    /**
     * Orders selectable for this credit note: scoped to the customer when known.
     * Statuses 1 (not paid) and 5 (cancelled) are excluded, as in the Smarty loop.
     *
     * @return array<int, array{id:int,label:string,selected:bool}>
     */
    private function orderOptions(CreditNote $creditNote): array
    {
        $customer = $creditNote->getCustomer();
        if (null === $customer && null === $creditNote->getOrder()) {
            return [];
        }

        $query = OrderQuery::create()->filterByStatusId([1, 5], Criteria::NOT_IN);
        if (null !== $customer) {
            $query->filterByCustomerId($customer->getId());
        } elseif (null !== $creditNote->getOrderId()) {
            $query->filterById($creditNote->getOrderId());
        }

        $out = [];
        foreach ($query->orderByCreatedAt(Criteria::DESC)->limit(50)->find() as $order) {
            $address = $order->getOrderAddressRelatedByInvoiceOrderAddressId();
            $out[] = [
                'id' => $order->getId(),
                'label' => sprintf('%s : (%s %s)', $order->getRef(), $address?->getFirstname() ?? '', $address?->getLastname() ?? ''),
                'selected' => $creditNote->getOrderId() === $order->getId(),
            ];
        }

        return $out;
    }

    /**
     * @return array<int, array{id:int,label:string}>
     */
    private function customerAddresses(CreditNote $creditNote): array
    {
        $customer = $creditNote->getCustomer();
        if (null === $customer) {
            return [];
        }

        $out = [];
        foreach (AddressQuery::create()->filterByCustomerId($customer->getId())->find() as $address) {
            $out[] = [
                'id' => $address->getId(),
                'label' => sprintf('(%s %s) : %s %s %s', $address->getFirstname(), $address->getLastname(), $address->getAddress1(), $address->getCity(), $address->getZipcode()),
            ];
        }

        return $out;
    }

    /**
     * @return array<int, array{id:int,label:string}>
     */
    private function titles(string $locale): array
    {
        $out = [];
        foreach (CustomerTitleQuery::create()->find() as $title) {
            $out[] = ['id' => $title->getId(), 'label' => $title->setLocale($locale)->getLong()];
        }

        return $out;
    }

    /**
     * @return array<int, array{id:int,title:string,is_default:bool}>
     */
    private function countries(string $locale): array
    {
        $out = [];
        foreach (CountryQuery::create()->filterByVisible(1)->find() as $country) {
            $out[] = [
                'id' => $country->getId(),
                'title' => $country->setLocale($locale)->getTitle(),
                'is_default' => (bool) $country->getByDefault(),
            ];
        }

        return $out;
    }

    /**
     * @return array<int, array{id:int,title:string}>
     */
    private function taxRules(string $locale): array
    {
        $out = [];
        foreach (TaxRuleQuery::create()->orderById()->find() as $taxRule) {
            $out[] = ['id' => $taxRule->getId(), 'title' => $taxRule->setLocale($locale)->getTitle()];
        }

        return $out;
    }

    /**
     * @return array{firstname:?string,lastname:?string,company:?string,address1:?string,address2:?string,zipcode:?string,city:?string,country_id:?int,title_id:?int}
     */
    private function address(CreditNote $creditNote): array
    {
        $address = $creditNote->getCreditNoteAddress();

        return [
            'firstname' => $address?->getFirstname(),
            'lastname' => $address?->getLastname(),
            'company' => $address?->getCompany(),
            'address1' => $address?->getAddress1(),
            'address2' => $address?->getAddress2(),
            'zipcode' => $address?->getZipcode(),
            'city' => $address?->getCity(),
            'country_id' => $address?->getCountryId(),
            'title_id' => $address?->getCustomerTitleId(),
        ];
    }

    /**
     * Order summary block (products + totals) shown when the credit note targets an order.
     *
     * @return array<string, mixed>
     */
    private function orderBlock(CreditNote $creditNote, string $locale): array
    {
        $order = $creditNote->getOrder();
        $currencyId = $order->getCurrencyId();
        $isFullRefund = $creditNote->getCreditNoteType()?->getCode() === CreditNoteHelper::TYPE_ORDER_FULL_REFUND;

        $existingQuantities = [];
        foreach ($creditNote->getCreditNoteDetails() as $detail) {
            if (null !== $detail->getOrderProductId()) {
                $existingQuantities[(int) $detail->getOrderProductId()] = $detail->getQuantity();
            }
        }

        $products = [];
        foreach (OrderProductQuery::create()->filterByOrderId($order->getId())->find() as $orderProduct) {
            $wasInPromo = (int) $orderProduct->getWasInPromo() === 1;
            $price = (float) ($wasInPromo ? $orderProduct->getPromoPrice() : $orderProduct->getPrice());
            $tax = 0.0;
            /** @var OrderProductTax $opt */
            foreach ($orderProduct->getOrderProductTaxes() as $opt) {
                $tax += (float) ($wasInPromo ? $opt->getPromoAmount() : $opt->getAmount());
            }

            $current = $isFullRefund
                ? (int) $orderProduct->getQuantity()
                : (int) ($existingQuantities[$orderProduct->getId()] ?? 0);

            $products[] = [
                'id' => $orderProduct->getId(),
                'title' => $orderProduct->getTitle(),
                'ref' => $orderProduct->getProductRef(),
                'quantity' => (int) $orderProduct->getQuantity(),
                'current_quantity' => $current,
                'price' => $this->money($price, $currencyId),
                'tax' => $this->money($tax, $currencyId),
                'taxed_price' => $this->money($price + $tax, $currencyId),
                'total' => $this->money(($price + $tax) * (int) $orderProduct->getQuantity(), $currencyId),
            ];
        }

        $tax = 0;
        $totalTaxed = $order->getTotalAmount($tax, true, true);

        return [
            'ref' => $order->getRef(),
            'id' => $order->getId(),
            'products' => $products,
            'is_full_refund' => $isFullRefund,
            'total_without_discount' => $this->money($totalTaxed - (float) $order->getPostage() + (float) $order->getDiscount(), $currencyId),
            'discount' => $this->money($order->getDiscount(), $currencyId),
            'total_with_discount' => $this->money($totalTaxed - (float) $order->getPostage(), $currencyId),
            'postage' => $this->money($order->getPostage(), $currencyId),
            'postage_tax' => $this->money($order->getPostageTax(), $currencyId),
            'total' => $this->money($totalTaxed, $currencyId),
        ];
    }

    /**
     * Free-amount detail rows (details NOT linked to an order product).
     *
     * @return array<int, array{id:?int,type:?string,title:?string,price:float|string|null,price_with_tax:float|string|null,tax_rule_id:?int}>
     */
    private function freeAmounts(CreditNote $creditNote): array
    {
        $out = [];
        foreach ($creditNote->getCreditNoteDetails() as $detail) {
            if (!empty($detail->getOrderProductId())) {
                continue;
            }
            $out[] = [
                'id' => $detail->getId(),
                'type' => $detail->getType(),
                'title' => $detail->getTitle(),
                'price' => $detail->getPrice(),
                'price_with_tax' => $detail->getPriceWithTax(),
                'tax_rule_id' => $detail->getTaxRuleId(),
            ];
        }

        return $out;
    }

    /**
     * @return array<int, array{admin:string,date:?\DateTimeInterface,comment:string}>
     */
    private function comments(CreditNote $creditNote): array
    {
        if (null === $creditNote->getId()) {
            return [];
        }

        $out = [];
        foreach (CreditNoteCommentQuery::create()->filterByCreditNoteId($creditNote->getId())->orderById(Criteria::DESC)->find() as $comment) {
            $admin = AdminQuery::create()->findPk($comment->getAdminId());
            $out[] = [
                'admin' => $admin !== null ? trim($admin->getFirstname().' '.$admin->getLastname()) : '',
                'date' => $comment->getCreatedAt(),
                'comment' => (string) $comment->getComment(),
            ];
        }

        return $out;
    }

    /**
     * @return array<int, array{ref:string,order_id:int}>
     */
    private function usedByOrders(CreditNote $creditNote): array
    {
        if (null === $creditNote->getId()) {
            return [];
        }

        $out = [];
        foreach (OrderCreditNoteQuery::create()->filterByCreditNoteId($creditNote->getId())->find() as $link) {
            $order = OrderQuery::create()->findPk($link->getOrderId());
            if (null === $order) {
                continue;
            }
            $out[] = ['ref' => $order->getRef(), 'order_id' => $order->getId()];
        }

        return $out;
    }
}
