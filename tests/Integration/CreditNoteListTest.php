<?php

declare(strict_types=1);

namespace CreditNote\Tests\Integration;

use CreditNote\CreditNote;
use CreditNote\Model\CreditNoteStatusQuery;
use CreditNote\Model\CreditNoteTypeQuery;
use CreditNote\Service\CreditNoteListFilterPresenter;
use CreditNote\Service\CreditNoteListFilters;
use CreditNote\Service\CreditNoteListReader;
use CreditNote\Service\CreditNoteListRowPresenter;
use CreditNote\Tests\Support\CreditNoteFixture;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\HttpFoundation\Request;
use Thelia\Model\ModuleQuery;
use Thelia\Test\IntegrationTestCase;

/**
 * The credit note list of the back-office: what its search and filters keep, and how a row
 * shows a credit note (its number under its reference, its customer as on the order list).
 * Every search names the customer of the test, so the credit notes already in the database
 * stay out of the results.
 */
final class CreditNoteListTest extends IntegrationTestCase
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
    public function theSearchFindsACreditNoteByItsReferenceItsNumberItsOrderOrItsCustomer(): void
    {
        $customer = $this->fixture->customer();
        $order = $this->fixture->paidOrder($customer);
        $onOrder = $this->fixture->creditNote($customer, $order, 'accepted');
        $onOrder->save();
        $rebate = $this->fixture->creditNote($customer, null, 'proposed', 1, 'rebate');
        $rebate->save();

        self::assertEqualsCanonicalizing([$onOrder->getId(), $rebate->getId()], $this->ids(['q' => $customer->getEmail()]));
        self::assertSame([$onOrder->getId()], $this->ids(['q' => $order->getRef()]));
        self::assertSame([$onOrder->getId()], $this->ids(['q' => $onOrder->getRef()]));
        self::assertSame([$onOrder->getId()], $this->ids(['q' => $onOrder->getInvoiceRef()]), 'the number of an accepted credit note is searched too');
    }

    #[Test]
    public function theStatusTypeAndAmountFiltersNarrowTheSearch(): void
    {
        $customer = $this->fixture->customer();
        $accepted = $this->fixture->creditNote($customer, $this->fixture->paidOrder($customer), 'accepted');
        $accepted->save();
        $rebate = $this->fixture->creditNote($customer, null, 'proposed', 1, 'rebate');
        $rebate->save();
        $search = ['q' => $customer->getEmail()];

        $acceptedStatus = CreditNoteStatusQuery::create()->findOneByCode('accepted');
        $rebateType = CreditNoteTypeQuery::create()->findOneByCode('rebate');

        self::assertSame([$accepted->getId()], $this->ids($search + ['status_ids' => [$acceptedStatus->getId()]]));
        self::assertSame([$rebate->getId()], $this->ids($search + ['type_ids' => [$rebateType->getId()]]));
        self::assertSame([$accepted->getId()], $this->ids($search + ['min_amount' => '100']), '117.60 with tax against 12.00');
        self::assertSame([$rebate->getId()], $this->ids($search + ['max_amount' => '12']), 'the bounds are inclusive');
    }

    #[Test]
    public function everyWordOfTheSearchMustBeFoundWhateverTheirOrder(): void
    {
        $customer = $this->fixture->customer();
        $creditNote = $this->fixture->creditNote($customer, null, 'proposed', 1, 'rebate');
        $creditNote->save();

        self::assertSame([$creditNote->getId()], $this->ids(['q' => 'Diallo Mohamed '.$customer->getEmail()]), 'the last name before the first name');
        self::assertSame([], $this->ids(['q' => 'Diallo Nobody '.$customer->getEmail()]), 'a word found nowhere excludes the credit note');
    }

    #[Test]
    public function theDateFilterBoundsTheInvoiceDateWhichADraftCarriesFromItsCreation(): void
    {
        $customer = $this->fixture->customer();
        $accepted = $this->fixture->creditNote($customer, $this->fixture->paidOrder($customer), 'accepted');
        $accepted->save();
        $draft = $this->fixture->creditNote($customer, null);
        $draft->save();
        $today = (new \DateTimeImmutable())->format('Y-m-d');
        $yesterday = (new \DateTimeImmutable('-1 day'))->format('Y-m-d');
        $search = ['q' => $customer->getEmail()];

        self::assertNotNull($draft->getInvoiceDate(), 'the module dates a credit note when it gives it its reference');
        self::assertEqualsCanonicalizing([$accepted->getId(), $draft->getId()], $this->ids($search + ['invoice_date_from' => $today, 'invoice_date_to' => $today]));
        self::assertSame([], $this->ids($search + ['invoice_date_to' => $yesterday]));
    }

    #[Test]
    public function aRowShowsTheInvoiceReferenceWithItsDateAndTheCustomerAsOnTheOrderList(): void
    {
        $customer = $this->fixture->customer();
        $numbered = $this->fixture->creditNote($customer, $this->fixture->paidOrder($customer), 'accepted');
        $numbered->save();
        $draft = $this->fixture->creditNote($customer, null);
        $draft->save();

        $rows = $this->rows(['q' => $customer->getEmail(), 'order' => 'created_at', 'direction' => 'asc']);
        [$numberedRow, $draftRow] = $rows;

        self::assertNotSame('', (string) $numbered->getInvoiceRef(), 'an accepted credit note is numbered on save');
        self::assertStringContainsString((string) $numbered->getRef(), $numberedRow['ref_html']);
        self::assertStringNotContainsString((string) $numbered->getInvoiceRef(), $numberedRow['ref_html'], 'the invoice reference has its own column');
        self::assertStringContainsString((string) $numbered->getInvoiceRef(), $numberedRow['invoice_html']);
        self::assertStringContainsString((string) $numbered->getInvoiceDate()?->format('d/m/Y'), $numberedRow['invoice_html'], 'the date of the document sits under its reference');
        self::assertTrue($numberedRow['numbered']);
        self::assertSame('Accepted', $numberedRow['status'], 'the title of the status in the locale asked');
        self::assertSame('Back Product', $numberedRow['type']);

        self::assertStringNotContainsString('credit-note-invoice-ref', $draftRow['invoice_html'], 'a draft has no invoice reference yet');
        self::assertFalse($draftRow['numbered'], 'a draft can still be edited and deleted');

        self::assertStringContainsString((string) $numbered->getCreatedAt()?->format('d/m/Y H:i'), $numberedRow['created_html']);
        self::assertStringContainsString((string) $numbered->getUpdatedAt()?->format('d/m/Y H:i'), $numberedRow['updated_html']);

        self::assertStringContainsString('Mohamed', $numberedRow['customer_html']);
        self::assertStringContainsString('Diallo', $numberedRow['customer_html']);
        self::assertStringContainsString((string) $customer->getEmail(), $numberedRow['customer_html'], 'the e-mail sits under the name, as on the order list');
        self::assertStringContainsString('117', $numberedRow['amount_html']);
        self::assertStringContainsString('<span class="text-muted">-</span>', $draftRow['order_html'], 'a credit note without order names none');
    }

    #[Test]
    public function aZeroAmountBoundIsAChipLikeAnyOther(): void
    {
        $filters = CreditNoteListFilters::fromRequest(new Request(['min_amount' => '0', 'max_amount' => '50']));

        $chips = $this->getService(CreditNoteListFilterPresenter::class)->present($filters, 'en_US')['active_chips'];

        self::assertSame(['0 - 50'], array_column($chips, 'value'));
    }

    #[Test]
    public function aPagePastTheLastOneShowsTheLastPage(): void
    {
        $customer = $this->fixture->customer();
        $this->fixture->creditNote($customer, null)->save();
        $filters = CreditNoteListFilters::fromRequest(new Request(['q' => $customer->getEmail()]));

        $page = $this->getService(CreditNoteListReader::class)->page($filters, 9, 20);

        self::assertSame(1, $page['current_page']);
        self::assertCount(1, $page['rows']);

        $nothing = CreditNoteListFilters::fromRequest(new Request(['q' => $customer->getEmail().'-nobody']));
        $empty = $this->getService(CreditNoteListReader::class)->page($nothing, 3, 20);

        self::assertSame([1, 1, []], [$empty['current_page'], $empty['last_page'], $empty['rows']], 'an empty list is its first page');
    }

    /**
     * @param array<string, mixed> $query
     *
     * @return list<int>
     */
    private function ids(array $query): array
    {
        return array_column($this->rows($query), 'id');
    }

    /**
     * @param array<string, mixed> $query
     *
     * @return list<array<string, mixed>>
     */
    private function rows(array $query): array
    {
        $page = $this->getService(CreditNoteListReader::class)->page(CreditNoteListFilters::fromRequest(new Request($query)), 1, 50);

        return $this->getService(CreditNoteListRowPresenter::class)->presentAll($page['rows'], 'en_US');
    }
}
