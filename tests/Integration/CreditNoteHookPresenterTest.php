<?php

declare(strict_types=1);

namespace CreditNote\Tests\Integration;

use CreditNote\CreditNote;
use CreditNote\Service\CreditNoteHookPresenter;
use CreditNote\Tests\Support\CreditNoteFixture;
use PHPUnit\Framework\Attributes\Test;
use Thelia\Model\ModuleQuery;
use Thelia\Test\IntegrationTestCase;

/**
 * What the order and customer pages of the back-office show about credit notes: the rows of
 * the credit note tab, its count, and the lines a product carries.
 */
final class CreditNoteHookPresenterTest extends IntegrationTestCase
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
    public function theOrderTabListsTheCreditNotesOfTheOrderNewestFirst(): void
    {
        $customer = $this->fixture->customer();
        $order = $this->fixture->paidOrder($customer);
        $first = $this->fixture->creditNote($customer, $order, 'accepted');
        $first->save();
        $second = $this->fixture->creditNote($customer, $order);
        $second->save();
        $this->fixture->creditNote($customer, $this->fixture->paidOrder($customer))->save();

        $rows = $this->presenter()->tableRows(null, $order->getId(), 'en_US');

        self::assertSame([$second->getId(), $first->getId()], array_column($rows, 'id'));
        self::assertSame(2, $this->presenter()->countForOrder($order->getId()));

        $acceptedRow = $rows[1];
        self::assertSame($first->getRef(), $acceptedRow['ref']);
        self::assertSame($first->getInvoiceRef(), $acceptedRow['invoice_ref']);
        self::assertSame('Mohamed Diallo', $acceptedRow['customer_name']);
        self::assertSame($order->getRef(), $acceptedRow['order_ref']);
        self::assertSame('Accepted', $acceptedRow['status_title']);
        self::assertSame('Back Product', $acceptedRow['type_title']);
        self::assertStringContainsString('117', $acceptedRow['total_price_with_tax']);
        self::assertStringContainsString('98', $acceptedRow['total_price']);
    }

    #[Test]
    public function theCustomerPageListsEveryCreditNoteOfTheCustomerWhateverTheOrder(): void
    {
        $customer = $this->fixture->customer();
        $onOrder = $this->fixture->creditNote($customer, $this->fixture->paidOrder($customer));
        $onOrder->save();
        $withoutOrder = $this->fixture->creditNote($customer, null, 'proposed', 1, 'rebate');
        $withoutOrder->save();
        $this->fixture->creditNote($this->fixture->customer(), null, 'proposed', 1, 'rebate')->save();

        $rows = $this->presenter()->tableRows($customer->getId(), null, 'fr_FR');

        self::assertEqualsCanonicalizing([$onOrder->getId(), $withoutOrder->getId()], array_column($rows, 'id'));
        self::assertSame('Proposé', $rows[0]['status_title'], 'the titles follow the locale of the administrator');
        self::assertNull($rows[0]['order_ref'], 'a rebate names no order');
    }

    #[Test]
    public function anOrderWithoutCreditNoteShowsNothingUsed(): void
    {
        $order = $this->fixture->paidOrder($this->fixture->customer());

        self::assertSame(0, $this->presenter()->countForOrder($order->getId()));
        self::assertSame([], $this->presenter()->tableRows(null, $order->getId(), 'en_US'));
        self::assertSame([], $this->presenter()->creditNotesUsedOnOrder($order->getId()), 'no credit note has been consumed on a new order');
    }

    private function presenter(): CreditNoteHookPresenter
    {
        return $this->getService(CreditNoteHookPresenter::class);
    }
}
