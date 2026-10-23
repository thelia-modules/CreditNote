<?php

declare(strict_types=1);

namespace CreditNote\Tests\Unit;

use CreditNote\Service\CreditNoteListFilters;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

/**
 * The filters of the credit note list, read from the query string and written back to it.
 */
final class CreditNoteListFiltersTest extends TestCase
{
    #[Test]
    public function theFiltersOfTheFormComeBackAsTheSameQueryString(): void
    {
        $query = [
            'q' => 'CN0001',
            'status_ids' => ['3', '1'],
            'type_ids' => ['2'],
            'invoice_date_from' => '2026-01-01',
            'invoice_date_to' => '2026-01-31',
            'min_amount' => '10',
            'max_amount' => '99.5',
            'order' => 'total',
            'direction' => 'asc',
        ];

        $filters = CreditNoteListFilters::fromRequest(new Request($query));

        self::assertSame([
            'status_ids' => [3, 1],
            'type_ids' => [2],
            'invoice_date_from' => '2026-01-01',
            'invoice_date_to' => '2026-01-31',
            'min_amount' => 10.0,
            'max_amount' => 99.5,
            'q' => 'CN0001',
            'order' => 'total',
            'direction' => 'asc',
        ], $filters->toQueryParams());
        self::assertSame(4, $filters->advancedCount(), 'status, type, date and amount are the filters of the collapsed panel');
    }

    #[Test]
    public function theParametersOfThePreviousListStillFilter(): void
    {
        $filters = CreditNoteListFilters::fromRequest(new Request([
            'status_id' => '2',
            'ref' => 'CN15',
            'creditNoteDateMin' => '2026-03-01',
            'creditNoteDateMax' => '2026-03-31',
        ]));

        self::assertSame([2], $filters->statusIds, 'the side menu links a status with status_id');
        self::assertSame('CN15', $filters->search);
        self::assertSame('2026-03-01 00:00:00', $filters->invoiceDateFrom?->format('Y-m-d H:i:s'));
        self::assertSame('2026-03-31 23:59:59', $filters->invoiceDateTo?->format('Y-m-d H:i:s'));

        $withEmptyFields = CreditNoteListFilters::fromRequest(new Request([
            'ref' => 'CN15',
            'q' => '',
            'creditNoteDateMin' => '2026-03-01',
            'invoice_date_from' => '',
        ]));

        self::assertSame('CN15', $withEmptyFields->search, 'an empty field of the form does not hide the previous parameter');
        self::assertSame('2026-03-01', $withEmptyFields->invoiceDateFrom?->format('Y-m-d'));
    }

    #[Test]
    public function everyDateColumnOfTheListSorts(): void
    {
        self::assertSame('created_at', CreditNoteListFilters::fromRequest(new Request(['order' => 'created_at']))->sort);
        self::assertSame('updated_at', CreditNoteListFilters::fromRequest(new Request(['order' => 'updated_at']))->sort);
    }

    #[Test]
    public function anEmptyQueryStringIsTheNewestCreditNotesFirstWithoutFilter(): void
    {
        $filters = CreditNoteListFilters::fromRequest(new Request());

        self::assertSame([], $filters->toQueryParams());
        self::assertSame('created_at', $filters->sort);
        self::assertSame('desc', $filters->direction);
        self::assertSame(0, $filters->advancedCount());
    }

    #[Test]
    public function aValueTheListCannotUseIsDropped(): void
    {
        $filters = CreditNoteListFilters::fromRequest(new Request([
            'status_ids' => ['0', '-1', 'abc', ['nested']],
            'invoice_date_from' => '2026-02-30',
            'min_amount' => '-5',
            'max_amount' => 'cheap',
            'order' => 'id; DROP TABLE credit_note',
            'direction' => 'sideways',
        ]));

        self::assertSame([], $filters->toQueryParams());
    }

    #[Test]
    public function aValueOfTheWrongShapeIsIgnoredInsteadOfRefused(): void
    {
        $request = new Request([
            'status_ids' => '1',
            'type_ids' => '2',
            'q' => ['x'],
            'status_id' => ['3'],
            'min_amount' => ['10'],
            'order' => ['total'],
            'page' => ['2'],
        ]);

        self::assertSame([], CreditNoteListFilters::fromRequest($request)->toQueryParams());
        self::assertSame(1, CreditNoteListFilters::pageFromRequest($request));
    }

    #[Test]
    public function anAmountTooLargeToBeAFigureIsDropped(): void
    {
        self::assertNull(CreditNoteListFilters::fromRequest(new Request(['min_amount' => '1e400']))->minAmount);
    }

    #[Test]
    public function thePageIsReadFromTheQueryString(): void
    {
        self::assertSame(3, CreditNoteListFilters::pageFromRequest(new Request(['page' => '3'])));
        self::assertSame(1, CreditNoteListFilters::pageFromRequest(new Request(['page' => '-2'])));
    }

    #[Test]
    public function boundsGivenTheWrongWayRoundAreSwapped(): void
    {
        $filters = CreditNoteListFilters::fromRequest(new Request([
            'invoice_date_from' => '2026-05-31',
            'invoice_date_to' => '2026-05-01',
            'min_amount' => '200',
            'max_amount' => '50',
        ]));

        self::assertSame('2026-05-01 00:00:00', $filters->invoiceDateFrom?->format('Y-m-d H:i:s'));
        self::assertSame('2026-05-31 23:59:59', $filters->invoiceDateTo?->format('Y-m-d H:i:s'));
        self::assertSame([50.0, 200.0], [$filters->minAmount, $filters->maxAmount]);
    }

    #[Test]
    public function removingAChipDropsThatFilterAndKeepsTheOthers(): void
    {
        $filters = CreditNoteListFilters::fromRequest(new Request([
            'q' => 'Diallo',
            'status_ids' => ['1'],
            'min_amount' => '10',
            'order' => 'ref',
        ]));

        self::assertSame(['status_ids' => [1], 'min_amount' => 10.0, 'order' => 'ref'], $filters->withoutFilter(CreditNoteListFilters::KEY_SEARCH)->toQueryParams());
        self::assertSame(['min_amount' => 10.0, 'q' => 'Diallo', 'order' => 'ref'],$filters->withoutFilter(CreditNoteListFilters::KEY_STATUS_IDS)->toQueryParams());
        self::assertSame(['status_ids' => [1], 'q' => 'Diallo', 'order' => 'ref'], $filters->withoutFilter(CreditNoteListFilters::KEY_AMOUNT_RANGE)->toQueryParams());
    }
}
