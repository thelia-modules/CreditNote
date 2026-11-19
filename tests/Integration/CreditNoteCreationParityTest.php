<?php

declare(strict_types=1);

namespace CreditNote\Tests\Integration;

use CreditNote\Exception\CreditNoteCreationException;
use CreditNote\Model\CreditNoteQuery;
use CreditNote\Model\CreditNoteStatusQuery;
use CreditNote\Model\CreditNoteTypeQuery;
use CreditNote\Service\CreditNoteCreator;
use CreditNote\Tests\Support\BackOfficeRouteTestCase;
use PHPUnit\Framework\Attributes\Test;
use Thelia\Model\OrderStatus;
use Thelia\Model\OrderStatusQuery;

/**
 * The back-office form and the API write the same credit note from the same order: what a
 * credit note takes from its order is one set of rules (CreditNoteOrderRules), not two.
 */
final class CreditNoteCreationParityTest extends BackOfficeRouteTestCase
{
    #[Test]
    public function aFullRefundIsTheSameCreditNoteFromTheBackOfficeAndFromTheApi(): void
    {
        $fullRefund = CreditNoteTypeQuery::create()->findOneByCode('order_full_refund');
        $fromTheForm = $this->orderWithPostageAndDiscount();
        $fromTheApi = $this->orderWithPostageAndDiscount();
        $this->loginSuperAdmin();

        $this->client->request('POST', '/admin/credit-note/create', $this->creationOf($fromTheForm, ['type_id' => $fullRefund->getId()]));
        self::assertTrue($this->client->getResponse()->isRedirection(), (string) $this->client->getResponse()->getContent());
        $this->getService(CreditNoteCreator::class)->fromOrder($fromTheApi->getId(), 'order_full_refund', []);

        $form = $this->shapeOf($fromTheForm->getId());
        $api = $this->shapeOf($fromTheApi->getId());

        self::assertSame($form, $api);
        // 2 x 117.60 of goods + 9.90 of postage - 5.00 of discount.
        self::assertSame('240.100000', $api['totalPriceWithTax']);
        self::assertSame(['product', 'shipping'], array_column($api['lines'], 'type'));
    }

    #[Test]
    public function anOrderNeverInvoicedIsRefusedByTheBackOfficeAsByTheApi(): void
    {
        $order = $this->fixture->paidOrder($this->fixture->customer());
        $order->setStatusId(OrderStatusQuery::create()->findOneByCode(OrderStatus::CODE_NOT_PAID)->getId())->save();
        $this->loginSuperAdmin();

        $errors = $this->requestAndReadErrors('POST', '/admin/credit-note/create', $this->creationOf($order));

        self::assertTrue($this->client->getResponse()->isRedirection(), 'a message, not an error page');
        self::assertSame(['Order '.$order->getRef().' is not invoiced: no credit note can be created on it.'], $errors);
        self::assertSame(0, CreditNoteQuery::create()->filterByOrderId($order->getId())->count());

        // The modal says so as soon as it opens: its order field only offers invoiced orders.
        $this->client->request('POST', '/admin/credit-note/ajax/modal/create', ['credit_note_create' => ['action' => 'view', 'order_id' => $order->getId()]]);
        $modal = (string) $this->client->getResponse()->getContent();
        self::assertSame(200, $this->client->getResponse()->getStatusCode());
        self::assertStringContainsString('Order '.$order->getRef().' is not invoiced', $modal);
        self::assertStringNotContainsString('<form', $modal, 'with nothing to fill');

        try {
            $this->getService(CreditNoteCreator::class)->fromOrder($order->getId(), 'order_full_refund', []);
            self::fail('the API wrote a credit note on an order that was never invoiced');
        } catch (CreditNoteCreationException $refusal) {
            self::assertStringContainsString('is not invoiced', $refusal->getMessage());
        }
    }

    #[Test]
    public function aCreditNoteProposedOnAnOrderCancelledSinceCanStillBeOpenedAndRefused(): void
    {
        $customer = $this->fixture->customer();
        $order = $this->fixture->paidOrder($customer);
        $creditNote = $this->fixture->creditNote($customer, $order);
        $creditNote->save();
        $order->setStatusId(OrderStatusQuery::create()->findOneByCode(OrderStatus::CODE_CANCELED)->getId())->save();
        $this->loginSuperAdmin();

        $this->client->request('POST', \sprintf('/admin/credit-note/%d', $creditNote->getId()), ['credit_note_create' => ['action' => 'view', 'order_id' => $order->getId()]]);
        self::assertSame(200, $this->client->getResponse()->getStatusCode(), 'the modal opens');

        $this->client->request('POST', \sprintf('/admin/credit-note/%d/_update', $creditNote->getId()), $this->creationOf($order, [
            'action' => 'update',
            'status_id' => CreditNoteStatusQuery::create()->findOneByCode('refused')->getId(),
        ]));

        self::assertTrue($this->client->getResponse()->isRedirection(), (string) $this->client->getResponse()->getContent());
        self::assertSame('refused', CreditNoteQuery::create()->findPk($creditNote->getId())->getCreditNoteStatus()->getCode());
    }

    private function orderWithPostageAndDiscount(): \Thelia\Model\Order
    {
        $order = $this->fixture->paidOrder($this->fixture->customer());
        $order->setPostage('9.900000')->setPostageTax('1.650000')->setDiscount('5.000000')->save();

        return $order;
    }

    /**
     * @return array<string, mixed>
     */
    private function shapeOf(int $orderId): array
    {
        $creditNote = CreditNoteQuery::create()->filterByOrderId($orderId)->findOne();
        self::assertNotNull($creditNote);
        $lines = [];

        foreach ($creditNote->getCreditNoteDetails() as $detail) {
            $lines[] = [
                'type' => $detail->getType(),
                'title' => $detail->getTitle(),
                'quantity' => (int) $detail->getQuantity(),
                'price' => \sprintf('%.6F', (float) $detail->getPrice()),
                'priceWithTax' => \sprintf('%.6F', (float) $detail->getPriceWithTax()),
            ];
        }

        usort($lines, static fn (array $a, array $b): int => $a['type'] <=> $b['type']);

        return [
            'totalPrice' => \sprintf('%.6F', (float) $creditNote->getTotalPrice()),
            'totalPriceWithTax' => \sprintf('%.6F', (float) $creditNote->getTotalPriceWithTax()),
            'discountWithoutTax' => \sprintf('%.6F', (float) $creditNote->getDiscountWithoutTax()),
            'discountWithTax' => \sprintf('%.6F', (float) $creditNote->getDiscountWithTax()),
            'lines' => $lines,
        ];
    }
}
