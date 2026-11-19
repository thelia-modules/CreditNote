<?php

declare(strict_types=1);

namespace CreditNote\Service;

use CreditNote\Model\CreditNoteDetail;
use Thelia\Model\OrderProduct;
use Thelia\Model\OrderProductTax;
use Thelia\Model\ProductSaleElementsQuery;
use Thelia\Model\TaxRuleQuery;

/**
 * A line of a credit note that refunds a line of an order: its title, its unit prices as the
 * order was invoiced (the promotional ones when the product was sold in promotion) and the tax
 * rule of the product. The quantity is the caller's. Shared by the back-office form and the API.
 */
final readonly class CreditNoteOrderLine
{
    public function fill(CreditNoteDetail $creditNoteDetail, OrderProduct $orderProduct): void
    {
        $inPromo = (bool) (int) $orderProduct->getWasInPromo();
        $priceWithoutTax = (float) ($inPromo ? $orderProduct->getPromoPrice() : $orderProduct->getPrice());
        $priceWithTax = $priceWithoutTax;

        /** @var OrderProductTax $orderProductTax */
        foreach ($orderProduct->getOrderProductTaxes() as $orderProductTax) {
            $priceWithTax += (float) ($inPromo ? $orderProductTax->getPromoAmount() : $orderProductTax->getAmount());
        }

        $creditNoteDetail
            ->setOrderProduct($orderProduct)
            ->setTitle($orderProduct->getTitle())
            ->setPrice(\sprintf('%.6F', $priceWithoutTax))
            ->setType('product')
            ->setPriceWithTax(\sprintf('%.6F', $priceWithTax));

        if (null !== $pse = ProductSaleElementsQuery::create()->findOneById($orderProduct->getProductSaleElementsId())) {
            $creditNoteDetail->setTaxRuleId(
                null === $pse->getProduct()
                    ? TaxRuleQuery::create()->findOneByIsDefault(true)?->getId()
                    : $pse->getProduct()->getTaxRuleId()
            );
        }
    }
}
