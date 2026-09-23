<?php

declare(strict_types=1);

namespace CreditNote\Tests\Integration;

use CreditNote\CreditNote;
use CreditNote\Tests\Support\CreditNoteFixture;
use PHPUnit\Framework\Attributes\Test;
use Thelia\Api\Service\DataAccess\LoopDataAccessService;
use Thelia\Model\ModuleQuery;
use Thelia\Test\IntegrationTestCase;

/**
 * The loops the PDF document and the back-office read a credit note through, run the way
 * the Twig `loop()` function runs them.
 */
final class CreditNoteLoopTest extends IntegrationTestCase
{
    private CreditNoteFixture $fixture;

    protected function setUp(): void
    {
        parent::setUp();

        if (!ModuleQuery::create()->findOneByCode(CreditNote::getModuleCode())?->getActivate()) {
            self::markTestSkipped('The CreditNote module is not active in the test database.');
        }

        $this->fixture = new CreditNoteFixture($this->getPropelConnection());
    }

    #[Test]
    public function theCreditNoteLoopHandsTheDocumentEverythingItPrints(): void
    {
        $customer = $this->fixture->customer();
        $order = $this->fixture->paidOrder($customer);
        $creditNote = $this->fixture->creditNote($customer, $order, 'accepted', 2);
        $creditNote->save();

        $rows = $this->loop('credit-note', ['id' => $creditNote->getId()]);

        self::assertCount(1, $rows);
        $row = $rows[0];
        self::assertSame($creditNote->getId(), $row['ID']);
        self::assertSame($creditNote->getRef(), $row['REF']);
        self::assertSame($creditNote->getInvoiceRef(), $row['INVOICE_REF']);
        self::assertSame($order->getId(), $row['ORDER_ID']);
        self::assertSame($order->getRef(), $row['ORDER_REF']);
        self::assertSame($customer->getId(), $row['CUSTOMER_ID']);
        self::assertSame('Mohamed Diallo', $row['CUSTOMER_NAME']);
        self::assertSame($creditNote->getInvoiceAddressId(), $row['INVOICE_ADDRESS_ID'], 'the address frozen on the credit note is reachable from the document');
        self::assertSame($order->getCurrencyId(), $row['CURRENCY_ID']);
        self::assertSame('196.000000', $row['TOTAL_PRICE'], 'decimals come back as strings');
        self::assertSame('235.200000', $row['TOTAL_PRICE_WITH_TAX']);
        self::assertSame('Accepted', $row['STATUS_TITLE']);
        self::assertSame('Back Product', $row['TYPE_TITLE']);
        self::assertInstanceOf(\DateTimeInterface::class, $row['INVOICE_DATE']);
    }

    #[Test]
    public function theDetailLoopListsTheLinesOfOneCreditNoteOnly(): void
    {
        $customer = $this->fixture->customer();
        $order = $this->fixture->paidOrder($customer);
        $creditNote = $this->fixture->creditNote($customer, $order, 'proposed', 2);
        $creditNote->save();
        $other = $this->fixture->creditNote($customer, $this->fixture->paidOrder($customer));
        $other->save();

        $rows = $this->loop('credit-note-detail', ['credit_note_id' => $creditNote->getId()]);

        self::assertCount(1, $rows);
        self::assertSame('Violet', $rows[0]['TITLE']);
        self::assertSame(2, $rows[0]['QUANTITY']);
        self::assertSame('98.000000', $rows[0]['PRICE']);
        self::assertSame('117.600000', $rows[0]['PRICE_WITH_TAX']);
        self::assertSame($order->getOrderProducts()->getFirst()->getId(), $rows[0]['ORDER_PRODUCT_ID']);
        self::assertSame('product', $rows[0]['TYPE']);
    }

    #[Test]
    public function theAddressLoopPrintsTheAddressFrozenOnTheCreditNote(): void
    {
        $customer = $this->fixture->customer();
        $creditNote = $this->fixture->creditNote($customer, null, 'proposed', 1, 'rebate');
        $creditNote->save();

        $rows = $this->loop('credit-note-address', ['id' => $creditNote->getInvoiceAddressId()]);

        self::assertCount(1, $rows);
        self::assertSame('Mohamed', $rows[0]['FIRSTNAME']);
        self::assertSame('Diallo', $rows[0]['LASTNAME']);
        self::assertSame('3 rue de Paris', $rows[0]['ADDRESS1']);
        self::assertSame('93100', $rows[0]['ZIPCODE']);
        self::assertSame('Montreuil', $rows[0]['CITY']);
        self::assertSame($customer->getTitleId(), $rows[0]['TITLE']);
    }

    #[Test]
    public function theCreditNoteLoopFiltersByCustomerAndByInvoicedState(): void
    {
        $customer = $this->fixture->customer();
        $order = $this->fixture->paidOrder($customer);
        $proposed = $this->fixture->creditNote($customer, $order);
        $proposed->save();
        $accepted = $this->fixture->creditNote($customer, $order, 'accepted');
        $accepted->save();

        $otherCustomer = $this->fixture->customer();
        $otherOrder = $this->fixture->paidOrder($otherCustomer);
        $this->fixture->creditNote($otherCustomer, $otherOrder, 'accepted')->save();

        $ids = static fn (array $rows): array => array_map(static fn (array $row): int => $row['ID'], $rows);

        self::assertEqualsCanonicalizing([$proposed->getId(), $accepted->getId()], $ids($this->loop('credit-note', ['customer_id' => $customer->getId()])));
        self::assertSame([$accepted->getId()], $ids($this->loop('credit-note', ['customer_id' => $customer->getId(), 'invoiced' => true])));
        self::assertSame([$proposed->getId()], $ids($this->loop('credit-note', ['customer_id' => $customer->getId(), 'invoiced' => false])));
        self::assertSame([], $ids($this->loop('credit-note', ['customer_id' => $customer->getId(), 'order_id' => $otherOrder->getId()])), 'the order of another customer yields nothing');
    }

    /**
     * @param array<string, mixed> $params
     *
     * @return list<array<string, mixed>>
     */
    private function loop(string $type, array $params): array
    {
        /** @var LoopDataAccessService $loops */
        $loops = $this->getService(LoopDataAccessService::class);

        return $loops->theliaLoop('test.' . $type, $type, $params);
    }
}
