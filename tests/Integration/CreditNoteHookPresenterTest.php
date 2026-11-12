<?php

declare(strict_types=1);

namespace CreditNote\Tests\Integration;

use CreditNote\CreditNote;
use CreditNote\Model\CreditNote as CreditNoteModel;
use CreditNote\Service\CreditNoteHookPresenter;
use CreditNote\Service\CreditNoteListReader;
use CreditNote\Service\CreditNoteListRowPresenter;
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

        $rows = $this->rows($this->reader()->ofOrder($order->getId()), 'en_US');

        self::assertSame([$second->getId(), $first->getId()], array_column($rows, 'id'));
        self::assertSame(2, $this->presenter()->countForOrder($order->getId()));

        $acceptedRow = $rows[1];
        self::assertSame($first->getRef(), $acceptedRow['ref']);
        self::assertTrue($acceptedRow['numbered']);
        self::assertStringContainsString($first->getInvoiceRef(), $acceptedRow['invoice_html']);
        self::assertStringContainsString($order->getRef(), $acceptedRow['order_html']);
        self::assertSame('Accepted', $acceptedRow['status']);
        self::assertSame('Back Product', $acceptedRow['type']);
        self::assertStringContainsString('117', $acceptedRow['amount_html']);
        self::assertStringContainsString('98', $acceptedRow['amount_html']);
        self::assertFalse($rows[0]['numbered'], 'a proposed credit note has no number yet');
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

        $rows = $this->rows($this->reader()->ofCustomer($customer->getId()), 'fr_FR');

        self::assertSame([$withoutOrder->getId(), $onOrder->getId()], array_column($rows, 'id'), 'newest first');
        self::assertSame('Proposé', $rows[0]['status'], 'the titles follow the locale of the administrator');
        self::assertStringNotContainsString('<a ', $rows[0]['order_html'], 'a rebate names no order');
        self::assertStringContainsString($onOrder->getOrder()->getRef(), $rows[1]['order_html']);
    }

    #[Test]
    public function anOrderWithoutCreditNoteShowsNothingUsed(): void
    {
        $order = $this->fixture->paidOrder($this->fixture->customer());

        self::assertSame(0, $this->presenter()->countForOrder($order->getId()));
        self::assertSame([], $this->reader()->ofOrder($order->getId()));
        self::assertSame(['rows' => [], 'remaining' => null], $this->presenter()->creditNotesUsedOnOrder($order->getId()), 'no credit note has been consumed on a new order');
    }

    private function presenter(): CreditNoteHookPresenter
    {
        return $this->getService(CreditNoteHookPresenter::class);
    }

    private function reader(): CreditNoteListReader
    {
        return $this->getService(CreditNoteListReader::class);
    }

    /**
     * @param list<CreditNoteModel> $creditNotes
     *
     * @return list<array<string, mixed>>
     */
    private function rows(array $creditNotes, string $locale): array
    {
        return $this->getService(CreditNoteListRowPresenter::class)->presentAll($creditNotes, $locale);
    }
}
