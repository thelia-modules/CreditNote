<?php

declare(strict_types=1);

namespace CreditNote\Service;

use CreditNote\Helper\BadgeColor;
use CreditNote\Model\CreditNote;
use CreditNote\Model\CreditNoteQuery;
use CreditNote\Model\CreditNoteStatusQuery;
use CreditNote\Model\CreditNoteTypeQuery;
use CreditNote\Model\Map\CreditNoteTableMap;
use Thelia\Model\CurrencyQuery;

/**
 * What the credit note list reads: a page of credit notes under the filters, the statuses and
 * types offered as filters, and the bounds of the amount slider.
 */
final readonly class CreditNoteListReader
{
    /**
     * A page past the last one (a bookmark on a list that shrank) shows the last page.
     *
     * @return array{rows: list<CreditNote>, current_page: int, last_page: int}
     */
    public function page(CreditNoteListFilters $filters, int $page, int $perPage): array
    {
        $page = max(1, $page);
        $perPage = max(1, $perPage);

        $pager = $this->query($filters)->paginate($page, $perPage);
        // An empty list has no page at all for Propel: it still shows as its first page.
        $lastPage = max(1, $pager->getLastPage());
        if ($page > $lastPage) {
            $page = $lastPage;
            $pager = $this->query($filters)->paginate($page, $perPage);
        }

        $rows = [];
        foreach ($pager as $creditNote) {
            $rows[] = $creditNote;
        }

        return [
            'rows' => $rows,
            'current_page' => $page,
            'last_page' => $lastPage,
        ];
    }

    /**
     * The customer, the order and the invoice address with its country are read with the
     * credit notes: each row shows them. The statuses and types come from statuses() and types().
     */
    private function query(CreditNoteListFilters $filters): CreditNoteQuery
    {
        return $filters->applyTo(CreditNoteQuery::create())
            ->joinWith('Customer')
            ->leftJoinWith('Order')
            ->joinWith('CreditNoteAddress')
            ->joinWith('CreditNoteAddress.Country');
    }

    /**
     * The titles come with the statuses (one query, translations joined).
     *
     * @return list<array{id: int, title: string, color: string}>
     */
    public function statuses(string $locale): array
    {
        $options = [];
        foreach (CreditNoteStatusQuery::create()->joinWithI18n($locale)->orderByPosition()->find() as $status) {
            $options[] = [
                'id' => (int) $status->getId(),
                'title' => (string) $status->setLocale($locale)->getTitle(),
                'color' => BadgeColor::css($status->getColor()),
            ];
        }

        return $options;
    }

    /**
     * @return list<array{id: int, title: string, color: string}>
     */
    public function types(string $locale): array
    {
        $options = [];
        foreach (CreditNoteTypeQuery::create()->joinWithI18n($locale)->orderByPosition()->find() as $type) {
            $options[] = [
                'id' => (int) $type->getId(),
                'title' => (string) $type->setLocale($locale)->getTitle(),
                'color' => BadgeColor::css($type->getColor()),
            ];
        }

        return $options;
    }

    /**
     * Bounds of the amount slider, rounded up from the largest credit note so its steps stay round.
     *
     * @return array{min: float, max: float, step: int, currency: string}
     */
    public function amountBounds(): array
    {
        $largest = (float) CreditNoteQuery::create()
            ->withColumn('MAX('.CreditNoteTableMap::COL_TOTAL_PRICE_WITH_TAX.')', 'largest')
            ->select(['largest'])
            ->findOne();

        $max = self::roundUpToNice($largest);

        return [
            'min' => 0.0,
            'max' => (float) $max,
            'step' => $max >= 5000 ? 100 : ($max >= 1000 ? 50 : 10),
            'currency' => (string) (CurrencyQuery::create()->filterByByDefault(true)->findOne()?->getSymbol() ?? ''),
        ];
    }

    private static function roundUpToNice(float $value): int
    {
        return match (true) {
            $value <= 100 => 100,
            $value <= 500 => 500,
            $value <= 1000 => 1000,
            $value <= 5000 => (int) (ceil($value / 500) * 500),
            default => (int) (ceil($value / 1000) * 1000),
        };
    }
}
