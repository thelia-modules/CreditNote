<?php

declare(strict_types=1);

namespace CreditNote\Service;

use CreditNote\CreditNote as CreditNoteModule;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * The filters of the credit note list as the template reads them: the values to put back in the
 * form, the options of the panel, and the chips of the active filters with the URL that drops each.
 */
final readonly class CreditNoteListFilterPresenter
{
    private const LIST_ROUTE = 'credit_note_list';

    public function __construct(
        private UrlGeneratorInterface $urls,
        private TranslatorInterface $translator,
        private CreditNoteListReader $reader,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function present(CreditNoteListFilters $filters, string $locale): array
    {
        $statuses = $this->reader->statuses($locale);
        $types = $this->reader->types($locale);

        return [
            'search_input' => $filters->search,
            'advanced_count' => $filters->advancedCount(),
            'active_chips' => $this->activeChips($filters, $statuses, $types),
            'clear_all_url' => $this->urls->generate(self::LIST_ROUTE),

            'status_options' => $statuses,
            'selected_status_ids' => $filters->statusIds,
            'type_options' => $types,
            'selected_type_ids' => $filters->typeIds,

            'invoice_date_from_input' => $filters->invoiceDateFrom?->format('Y-m-d') ?? '',
            'invoice_date_to_input' => $filters->invoiceDateTo?->format('Y-m-d') ?? '',
            'min_amount_input' => self::formatNumber($filters->minAmount),
            'max_amount_input' => self::formatNumber($filters->maxAmount),
            'amount_slider' => $this->reader->amountBounds(),

            'sort_field' => $filters->sort,
            'sort_direction' => $filters->direction,
        ];
    }

    /**
     * @param list<array{id: int, title: string, color: string}> $statuses
     * @param list<array{id: int, title: string, color: string}> $types
     *
     * @return list<array{key: string, label: string, value: string, remove_url: string}>
     */
    private function activeChips(CreditNoteListFilters $filters, array $statuses, array $types): array
    {
        $chips = [];

        if ($filters->statusIds !== []) {
            $chips[] = $this->chip($filters, CreditNoteListFilters::KEY_STATUS_IDS, $this->translator->trans('Status', [], CreditNoteModule::DOMAIN_MESSAGE), self::titles($filters->statusIds, $statuses));
        }
        if ($filters->typeIds !== []) {
            $chips[] = $this->chip($filters, CreditNoteListFilters::KEY_TYPE_IDS, $this->translator->trans('Type', [], CreditNoteModule::DOMAIN_MESSAGE), self::titles($filters->typeIds, $types));
        }
        if ('' !== $range = $this->rangeLabel($filters->invoiceDateFrom?->format('d/m/Y'), $filters->invoiceDateTo?->format('d/m/Y'))) {
            $chips[] = $this->chip($filters, CreditNoteListFilters::KEY_INVOICE_DATE_RANGE, $this->translator->trans('Invoice date', [], CreditNoteModule::DOMAIN_MESSAGE), $range);
        }
        if ('' !== $range = $this->rangeLabel(
            null !== $filters->minAmount ? self::formatNumber($filters->minAmount) : null,
            null !== $filters->maxAmount ? self::formatNumber($filters->maxAmount) : null,
        )) {
            $chips[] = $this->chip($filters, CreditNoteListFilters::KEY_AMOUNT_RANGE, $this->translator->trans('Amount', [], CreditNoteModule::DOMAIN_MESSAGE), $range);
        }
        if ($filters->search !== '') {
            $chips[] = $this->chip($filters, CreditNoteListFilters::KEY_SEARCH, $this->translator->trans('Search', [], CreditNoteModule::DOMAIN_MESSAGE), $filters->search);
        }

        return $chips;
    }

    /**
     * @return array{key: string, label: string, value: string, remove_url: string}
     */
    private function chip(CreditNoteListFilters $filters, string $key, string $label, string $value): array
    {
        return [
            'key' => $key,
            'label' => $label,
            'value' => $value,
            'remove_url' => $this->urls->generate(self::LIST_ROUTE, $filters->withoutFilter($key)->toQueryParams()),
        ];
    }

    private function rangeLabel(?string $from, ?string $to): string
    {
        return match (true) {
            null !== $from && null !== $to => $from.' - '.$to,
            null !== $from => $this->translator->trans('from %value%', ['%value%' => $from], CreditNoteModule::DOMAIN_MESSAGE),
            null !== $to => $this->translator->trans('up to %value%', ['%value%' => $to], CreditNoteModule::DOMAIN_MESSAGE),
            default => '',
        };
    }

    /**
     * @param list<int>                                           $ids
     * @param list<array{id: int, title: string, color: string}> $options
     */
    private static function titles(array $ids, array $options): string
    {
        $titles = array_column($options, 'title', 'id');

        return implode(', ', array_filter(array_map(static fn (int $id): string => $titles[$id] ?? '', $ids)));
    }

    private static function formatNumber(?float $value): string
    {
        if (null === $value) {
            return '';
        }

        return rtrim(rtrim(number_format($value, 2, '.', ''), '0'), '.');
    }
}
