<?php

declare(strict_types=1);

namespace CreditNote\Service;

use CreditNote\Model\CreditNoteQuery;
use CreditNote\Model\Map\CreditNoteTableMap;
use Propel\Runtime\ActiveQuery\Criteria;
use Symfony\Component\HttpFoundation\Request;

/**
 * The filters of the credit note list, read from the query string: parsing, URL serialisation
 * and SQL application in one place, the same surface as the order list of the back-office.
 *
 * The free search covers the reference, the number, the order reference and the customer at
 * once. The date range bounds the date of the credit note document (`invoice_date`), as the
 * list always did. The parameters of the previous list (`status_id` from the side menu, `ref`,
 * `creditNoteDateMin`, `creditNoteDateMax`) are still read, so their links keep working.
 */
final readonly class CreditNoteListFilters
{
    public const DEFAULT_SORT = 'created_at';
    public const DEFAULT_DIRECTION = 'desc';
    public const ALLOWED_SORTS = ['ref', 'created_at', 'updated_at', 'total'];
    public const ALLOWED_DIRECTIONS = ['asc', 'desc'];
    private const MAX_SEARCH_WORDS = 5;

    public const KEY_STATUS_IDS = 'status_ids';
    public const KEY_TYPE_IDS = 'type_ids';
    public const KEY_INVOICE_DATE_RANGE = 'invoice_date_range';
    public const KEY_AMOUNT_RANGE = 'amount_range';
    public const KEY_SEARCH = 'search';

    /**
     * @param list<int> $statusIds
     * @param list<int> $typeIds
     */
    public function __construct(
        public array $statusIds = [],
        public array $typeIds = [],
        public ?\DateTimeImmutable $invoiceDateFrom = null,
        public ?\DateTimeImmutable $invoiceDateTo = null,
        public ?float $minAmount = null,
        public ?float $maxAmount = null,
        public string $search = '',
        public string $sort = self::DEFAULT_SORT,
        public string $direction = self::DEFAULT_DIRECTION,
    ) {
    }

    /**
     * Reads the raw query string: a value of the wrong shape (a list where a word is expected, or
     * the reverse, as in a hand-edited link) is ignored instead of answering 400, which the
     * typed accessors of InputBag do.
     */
    public static function fromRequest(Request $request): self
    {
        $query = $request->query->all();

        $statusIds = self::parseIntArray($query['status_ids'] ?? []);
        if ($statusIds === [] && null !== $single = self::parsePositiveInt(self::text($query, 'status_id'))) {
            $statusIds = [$single];
        }

        $invoiceDateFrom = self::parseDate(self::text($query, 'invoice_date_from', 'creditNoteDateMin'), endOfDay: false);
        $invoiceDateTo = self::parseDate(self::text($query, 'invoice_date_to', 'creditNoteDateMax'), endOfDay: true);
        if ($invoiceDateFrom !== null && $invoiceDateTo !== null && $invoiceDateFrom > $invoiceDateTo) {
            [$invoiceDateFrom, $invoiceDateTo] = [$invoiceDateTo->setTime(0, 0, 0), $invoiceDateFrom->setTime(23, 59, 59)];
        }

        $minAmount = self::parseAmount(self::text($query, 'min_amount'));
        $maxAmount = self::parseAmount(self::text($query, 'max_amount'));
        if ($minAmount !== null && $maxAmount !== null && $minAmount > $maxAmount) {
            [$minAmount, $maxAmount] = [$maxAmount, $minAmount];
        }

        $sort = self::text($query, 'order');
        if (!\in_array($sort, self::ALLOWED_SORTS, true)) {
            $sort = self::DEFAULT_SORT;
        }

        $direction = strtolower(self::text($query, 'direction'));
        if (!\in_array($direction, self::ALLOWED_DIRECTIONS, true)) {
            $direction = self::DEFAULT_DIRECTION;
        }

        return new self(
            statusIds: $statusIds,
            typeIds: self::parseIntArray($query['type_ids'] ?? []),
            invoiceDateFrom: $invoiceDateFrom,
            invoiceDateTo: $invoiceDateTo,
            minAmount: $minAmount,
            maxAmount: $maxAmount,
            search: trim(self::text($query, 'q', 'ref')),
            sort: $sort,
            direction: $direction,
        );
    }

    /**
     * The page asked in the query string, 1 when it is absent or not a number.
     */
    public static function pageFromRequest(Request $request): int
    {
        return self::parsePositiveInt(self::text($request->query->all(), 'page')) ?? 1;
    }

    /**
     * The filters of the collapsed panel that are set: the badge of its toggle.
     */
    public function advancedCount(): int
    {
        return \count(array_filter([
            $this->statusIds !== [],
            $this->typeIds !== [],
            $this->invoiceDateFrom !== null || $this->invoiceDateTo !== null,
            $this->minAmount !== null || $this->maxAmount !== null,
        ]));
    }

    /**
     * Omits the defaults, so the URLs stay short and bookmarkable.
     *
     * @return array<string, scalar|list<int>>
     */
    public function toQueryParams(): array
    {
        $params = [];

        if ($this->statusIds !== []) {
            $params['status_ids'] = $this->statusIds;
        }
        if ($this->typeIds !== []) {
            $params['type_ids'] = $this->typeIds;
        }
        if ($this->invoiceDateFrom !== null) {
            $params['invoice_date_from'] = $this->invoiceDateFrom->format('Y-m-d');
        }
        if ($this->invoiceDateTo !== null) {
            $params['invoice_date_to'] = $this->invoiceDateTo->format('Y-m-d');
        }
        if ($this->minAmount !== null) {
            $params['min_amount'] = $this->minAmount;
        }
        if ($this->maxAmount !== null) {
            $params['max_amount'] = $this->maxAmount;
        }
        if ($this->search !== '') {
            $params['q'] = $this->search;
        }
        if ($this->sort !== self::DEFAULT_SORT) {
            $params['order'] = $this->sort;
        }
        if ($this->direction !== self::DEFAULT_DIRECTION) {
            $params['direction'] = $this->direction;
        }

        return $params;
    }

    public function withoutFilter(string $key): self
    {
        return new self(
            statusIds: $key === self::KEY_STATUS_IDS ? [] : $this->statusIds,
            typeIds: $key === self::KEY_TYPE_IDS ? [] : $this->typeIds,
            invoiceDateFrom: $key === self::KEY_INVOICE_DATE_RANGE ? null : $this->invoiceDateFrom,
            invoiceDateTo: $key === self::KEY_INVOICE_DATE_RANGE ? null : $this->invoiceDateTo,
            minAmount: $key === self::KEY_AMOUNT_RANGE ? null : $this->minAmount,
            maxAmount: $key === self::KEY_AMOUNT_RANGE ? null : $this->maxAmount,
            search: $key === self::KEY_SEARCH ? '' : $this->search,
            sort: $this->sort,
            direction: $this->direction,
        );
    }

    public function applyTo(CreditNoteQuery $query): CreditNoteQuery
    {
        if ($this->statusIds !== []) {
            $query->filterByStatusId($this->statusIds, Criteria::IN);
        }
        if ($this->typeIds !== []) {
            $query->filterByTypeId($this->typeIds, Criteria::IN);
        }
        if ($this->invoiceDateFrom !== null) {
            $query->filterByInvoiceDate($this->invoiceDateFrom->format('Y-m-d H:i:s'), Criteria::GREATER_EQUAL);
        }
        if ($this->invoiceDateTo !== null) {
            $query->filterByInvoiceDate($this->invoiceDateTo->format('Y-m-d H:i:s'), Criteria::LESS_EQUAL);
        }
        if ($this->minAmount !== null) {
            $query->filterByTotalPriceWithTax((string) $this->minAmount, Criteria::GREATER_EQUAL);
        }
        if ($this->maxAmount !== null) {
            $query->filterByTotalPriceWithTax((string) $this->maxAmount, Criteria::LESS_EQUAL);
        }

        $this->applySearch($query);

        $direction = $this->direction === 'asc' ? Criteria::ASC : Criteria::DESC;
        match ($this->sort) {
            'ref' => $query->orderByRef($direction),
            'updated_at' => $query->orderByUpdatedAt($direction),
            'total' => $query->orderByTotalPriceWithTax($direction),
            default => $query->orderByCreatedAt($direction),
        };

        // Rows sharing the sorted value keep the same order from one page to the next.
        return $query->orderById($direction);
    }

    private function applySearch(CreditNoteQuery $query): void
    {
        if ($this->search === '') {
            return;
        }

        // Every word must be found in one of the fields, so "Diallo Mohamed" finds the customer
        // whatever the order of the names. condition() and combine() keep each word's
        // alternatives in one clause: chaining _or() would turn the whole WHERE into an OR with
        // the other filters.
        $words = \array_slice(preg_split('/\s+/u', $this->search, -1, \PREG_SPLIT_NO_EMPTY) ?: [], 0, self::MAX_SEARCH_WORDS);
        $clauses = [];
        foreach ($words as $index => $word) {
            $needle = '%'.addcslashes($word, '%_\\').'%';
            $query
                ->condition("ref_$index", CreditNoteTableMap::COL_REF.' LIKE ?', $needle, \PDO::PARAM_STR)
                ->condition("number_$index", CreditNoteTableMap::COL_INVOICE_REF.' LIKE ?', $needle, \PDO::PARAM_STR)
                ->condition(
                    "order_$index",
                    'EXISTS (SELECT 1 FROM `order` o WHERE o.id = '.CreditNoteTableMap::COL_ORDER_ID.' AND o.ref LIKE ?)',
                    $needle,
                    \PDO::PARAM_STR,
                )
                ->condition(
                    "customer_$index",
                    'EXISTS (SELECT 1 FROM customer c WHERE c.id = '.CreditNoteTableMap::COL_CUSTOMER_ID
                        .' AND CONCAT_WS(\' \', c.firstname, c.lastname, c.email) LIKE ?)',
                    $needle,
                    \PDO::PARAM_STR,
                )
                ->combine(["ref_$index", "number_$index", "order_$index", "customer_$index"], Criteria::LOGICAL_OR, "word_$index");
            $clauses[] = "word_$index";
        }

        $query->combine($clauses, Criteria::LOGICAL_AND);
    }

    /**
     * The text of the first of $keys given a non-empty word in the query string, '' otherwise.
     *
     * @param array<string, mixed> $query
     */
    private static function text(array $query, string ...$keys): string
    {
        foreach ($keys as $key) {
            // An empty field of the form does not hide the previous parameter it replaces.
            if (isset($query[$key]) && \is_scalar($query[$key]) && '' !== trim((string) $query[$key])) {
                return (string) $query[$key];
            }
        }

        return '';
    }

    /**
     * @return list<int>
     */
    private static function parseIntArray(mixed $value): array
    {
        if (!\is_array($value)) {
            return [];
        }

        $ids = [];
        foreach ($value as $raw) {
            if (\is_scalar($raw) && null !== $id = self::parsePositiveInt((string) $raw)) {
                $ids[$id] = $id;
            }
        }

        return array_values($ids);
    }

    private static function parsePositiveInt(string $value): ?int
    {
        $trimmed = trim($value);
        if ($trimmed === '' || !ctype_digit($trimmed)) {
            return null;
        }

        return (int) $trimmed > 0 ? (int) $trimmed : null;
    }

    private static function parseDate(string $value, bool $endOfDay): ?\DateTimeImmutable
    {
        $date = \DateTimeImmutable::createFromFormat('!Y-m-d', trim($value));
        if ($date === false || $date->format('Y-m-d') !== trim($value)) {
            return null;
        }

        return $endOfDay ? $date->setTime(23, 59, 59) : $date;
    }

    private static function parseAmount(string $value): ?float
    {
        $normalized = str_replace([' ', ','], ['', '.'], trim($value));
        if ($normalized === '' || !is_numeric($normalized)) {
            return null;
        }

        $amount = (float) $normalized;

        // "1e400" is numeric but overflows to INF, which the database would read as 0.
        return $amount >= 0 && is_finite($amount) ? $amount : null;
    }
}
