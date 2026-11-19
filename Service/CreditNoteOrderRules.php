<?php

declare(strict_types=1);

namespace CreditNote\Service;

use CreditNote\Exception\CreditNoteCreationException;
use CreditNote\Model\CreditNote;
use CreditNote\Model\CreditNoteDetail;
use Thelia\Model\Order;

/**
 * What a credit note takes from its order, whoever writes it: the back-office form and the API
 * apply the same rules. The order must be invoiced; a full refund gives the postage back on a
 * line of its own and takes the discount of the order; the totals are the lines minus the
 * discount.
 */
final readonly class CreditNoteOrderRules
{
    public const SHIPPING = 'shipping';

    /** Stored on the line and printed as it is on the document. */
    private const SHIPPING_TITLE = 'Frais de port';

    /**
     * @throws CreditNoteCreationException when the order was never invoiced: not paid, or cancelled
     */
    public function assertInvoiced(Order $order): void
    {
        if ($order->isNotPaid() || $order->isCancelled()) {
            throw new CreditNoteCreationException(\sprintf('Order %s is not invoiced.', $order->getRef()));
        }
    }

    /**
     * The postage line of a full refund: added, brought back to the postage of the order when
     * the credit note already has one, removed when the order has no postage.
     */
    public function putShippingLine(CreditNote $creditNote, Order $order): void
    {
        $postage = (float) $order->getPostage();
        $shippingLine = null;

        foreach ($creditNote->getCreditNoteDetails() as $creditNoteDetail) {
            if (self::SHIPPING !== $creditNoteDetail->getType()) {
                continue;
            }

            if ($postage <= 0) {
                $creditNote->removeCreditNoteDetail($creditNoteDetail);

                continue;
            }

            $shippingLine = $creditNoteDetail;
        }

        if ($postage <= 0) {
            return;
        }

        if (null === $shippingLine) {
            $shippingLine = (new CreditNoteDetail())->setQuantity(1)->setTitle(self::SHIPPING_TITLE)->setType(self::SHIPPING);
            $creditNote->addCreditNoteDetail($shippingLine);
        }

        $shippingLine
            ->setPrice(\sprintf('%.6F', $postage - (float) $order->getPostageTax()))
            ->setPriceWithTax(\sprintf('%.6F', $postage));
    }

    /** The discount a full refund takes back, without and with tax alike: the one of the order. */
    public function fullRefundDiscount(Order $order): string
    {
        return \sprintf('%.6F', (float) $order->getDiscount());
    }

    /**
     * Each line is rounded to the cent before it is added, as the order ceiling compares cents.
     */
    public function setTotals(CreditNote $creditNote): void
    {
        $totalPrice = 0;
        $totalPriceWithTax = 0;

        foreach ($creditNote->getCreditNoteDetails() as $creditNoteDetail) {
            $totalPrice += Cents::of((float) $creditNoteDetail->getPrice() * (float) $creditNoteDetail->getQuantity());
            $totalPriceWithTax += Cents::of((float) $creditNoteDetail->getPriceWithTax() * (float) $creditNoteDetail->getQuantity());
        }

        $creditNote->setTotalPrice(Cents::toDecimal($totalPrice - Cents::of($creditNote->getDiscountWithoutTax())));
        $creditNote->setTotalPriceWithTax(Cents::toDecimal($totalPriceWithTax - Cents::of($creditNote->getDiscountWithTax())));
    }
}
