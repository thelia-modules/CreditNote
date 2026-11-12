<?php

declare(strict_types=1);

namespace CreditNote\Service;

use CreditNote\Exception\CreditNoteCreationException;
use CreditNote\Exception\CreditNoteExceedsOrderException;
use CreditNote\Helper\CreditNoteHelper;
use CreditNote\Model\CreditNote;
use CreditNote\Model\CreditNoteAddress;
use CreditNote\Model\CreditNoteComment;
use CreditNote\Model\CreditNoteDetail;
use CreditNote\Model\CreditNoteStatusQuery;
use CreditNote\Model\CreditNoteTypeQuery;
use CreditNote\Model\Map\CreditNoteTableMap;
use Propel\Runtime\Propel;
use Thelia\Model\Order;
use Thelia\Model\OrderQuery;

/**
 * Creates a credit note from an order, outside the back-office form: the customer, the currency
 * and the invoice address are the ones of the order, the lines refund the quantities asked at
 * the prices the order was invoiced, and a full refund takes every line, the postage and the
 * discount of the order, as the back-office does.
 *
 * The ceiling of the order, the reference and the accounting number are applied on save by
 * CreditNoteListener, as for any credit note.
 */
final readonly class CreditNoteCreator
{
    /** The statuses a credit note may be created in. */
    private const CREATION_STATUSES = ['proposed', 'accepted'];

    public function __construct(
        private CreditNoteOrderLine $orderLine,
    ) {
    }

    /**
     * @param array<int, int> $quantities the quantity to refund by order product id; ignored for a full refund
     *
     * @throws CreditNoteCreationException     when what is asked cannot make a credit note
     * @throws CreditNoteExceedsOrderException when the credit notes of the order would refund more than it was worth
     */
    public function fromOrder(
        int $orderId,
        string $typeCode,
        array $quantities,
        string $statusCode = 'proposed',
        bool $allowPartialUse = true,
        ?string $comment = null,
        ?int $adminId = null,
    ): CreditNote {
        $order = OrderQuery::create()->findPk($orderId)
            ?? throw new CreditNoteCreationException(\sprintf('Order %d does not exist.', $orderId));

        if ($order->isNotPaid() || $order->isCancelled()) {
            throw new CreditNoteCreationException(\sprintf('Order %s is not invoiced.', $order->getRef()));
        }

        $type = CreditNoteTypeQuery::create()->findOneByCode($typeCode)
            ?? throw new CreditNoteCreationException(\sprintf('The credit note type "%s" does not exist.', $typeCode));

        if (!\in_array($statusCode, self::CREATION_STATUSES, true)) {
            throw new CreditNoteCreationException(\sprintf('A credit note is created "%s", not "%s".', implode('" or "', self::CREATION_STATUSES), $statusCode));
        }

        $status = CreditNoteStatusQuery::create()->findOneByCode($statusCode)
            ?? throw new CreditNoteCreationException(\sprintf('The credit note status "%s" does not exist.', $statusCode));

        $fullRefund = CreditNoteHelper::TYPE_ORDER_FULL_REFUND === $type->getCode();

        $creditNote = (new CreditNote())
            ->setCreditNoteType($type)
            ->setOrder($order)
            ->setCustomer($order->getCustomer())
            ->setCurrency($order->getCurrency())
            ->setCreditNoteAddress($this->invoiceAddressOf($order))
            ->setCreditNoteStatus($status)
            ->setAllowPartialUse($allowPartialUse);

        $this->addOrderLines($creditNote, $order, $quantities, $fullRefund);

        if ($fullRefund && (float) $order->getPostage() > 0) {
            $creditNote->addCreditNoteDetail(
                (new CreditNoteDetail())
                    ->setQuantity(1)
                    ->setTitle('Frais de port')
                    ->setType('shipping')
                    ->setPrice(\sprintf('%.6F', (float) $order->getPostage() - (float) $order->getPostageTax()))
                    ->setPriceWithTax(\sprintf('%.6F', (float) $order->getPostage()))
            );
        }

        if (0 === $creditNote->getCreditNoteDetails()->count()) {
            throw new CreditNoteCreationException('The credit note refunds nothing: give at least one line of the order with a quantity.');
        }

        $discount = \sprintf('%.6F', $fullRefund ? (float) $order->getDiscount() : 0.0);
        $creditNote->setDiscountWithoutTax($discount)->setDiscountWithTax($discount);
        $this->setTotals($creditNote);

        $comment = trim((string) $comment);

        if ('' !== $comment) {
            $creditNote->addCreditNoteComment((new CreditNoteComment())->setComment($comment)->setAdminId($adminId));
        }

        $connection = Propel::getServiceContainer()->getWriteConnection(CreditNoteTableMap::DATABASE_NAME);
        $connection->beginTransaction();

        try {
            $creditNote->save($connection);
            $connection->commit();
        } catch (\Throwable $throwable) {
            $connection->rollBack();

            throw $throwable;
        }

        return $creditNote;
    }

    /**
     * @param array<int, int> $quantities
     */
    private function addOrderLines(CreditNote $creditNote, Order $order, array $quantities, bool $fullRefund): void
    {
        $orderProducts = [];

        foreach ($order->getOrderProducts() as $orderProduct) {
            $orderProducts[(int) $orderProduct->getId()] = $orderProduct;
        }

        if (!$fullRefund) {
            foreach ($quantities as $orderProductId => $quantity) {
                $orderProduct = $orderProducts[(int) $orderProductId]
                    ?? throw new CreditNoteCreationException(\sprintf('Line %d is not a line of order %s.', $orderProductId, $order->getRef()));

                if ((int) $quantity <= 0 || (int) $quantity > (int) $orderProduct->getQuantity()) {
                    throw new CreditNoteCreationException(\sprintf('Line %d of order %s holds %s, %s asked.', $orderProductId, $order->getRef(), (string) $orderProduct->getQuantity(), (string) $quantity));
                }
            }
        }

        foreach ($orderProducts as $orderProductId => $orderProduct) {
            $quantity = $fullRefund ? (int) $orderProduct->getQuantity() : (int) ($quantities[$orderProductId] ?? 0);

            if ($quantity <= 0) {
                continue;
            }

            $creditNoteDetail = (new CreditNoteDetail())->setQuantity($quantity);
            $this->orderLine->fill($creditNoteDetail, $orderProduct);
            $creditNote->addCreditNoteDetail($creditNoteDetail);
        }
    }

    private function invoiceAddressOf(Order $order): CreditNoteAddress
    {
        $address = $order->getOrderAddressRelatedByInvoiceOrderAddressId()
            ?? throw new CreditNoteCreationException(\sprintf('Order %s has no invoice address.', $order->getRef()));

        return (new CreditNoteAddress())
            ->setCustomerTitleId($address->getCustomerTitle()?->getId())
            ->setAddress1($address->getAddress1())
            ->setAddress2($address->getAddress2())
            ->setAddress3($address->getAddress3())
            ->setFirstname($address->getFirstname())
            ->setLastname($address->getLastname())
            ->setCity($address->getCity())
            ->setZipcode($address->getZipcode())
            ->setCompany($address->getCompany())
            ->setCountryId($address->getCountry()?->getId());
    }

    private function setTotals(CreditNote $creditNote): void
    {
        $totalPrice = 0.0;
        $totalPriceWithTax = 0.0;

        foreach ($creditNote->getCreditNoteDetails() as $creditNoteDetail) {
            $totalPrice += (float) $creditNoteDetail->getPrice() * (float) $creditNoteDetail->getQuantity();
            $totalPriceWithTax += (float) $creditNoteDetail->getPriceWithTax() * (float) $creditNoteDetail->getQuantity();
        }

        $creditNote->setTotalPrice(\sprintf('%.6F', $totalPrice - (float) $creditNote->getDiscountWithoutTax()));
        $creditNote->setTotalPriceWithTax(\sprintf('%.6F', $totalPriceWithTax - (float) $creditNote->getDiscountWithTax()));
    }
}
