<?php

declare(strict_types=1);

namespace CreditNote\Service;

use CreditNote\CreditNote as CreditNoteModule;
use CreditNote\Helper\BadgeColor;
use CreditNote\Model\CreditNote;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Thelia\Model\CurrencyQuery;
use Thelia\Tools\MoneyFormat;

/**
 * A credit note as a row of the list, laid out like the order list of the back-office: the
 * reference, the invoice reference with its date, the customer with the flag of the invoice
 * country and the e-mail, the creation and update dates with their age. The actions are built
 * by the template (row_action()).
 */
final readonly class CreditNoteListRowPresenter
{
    private const NO_OPTION = ['title' => '', 'color' => BadgeColor::FALLBACK];
    private const CLOCK_TOLERANCE = 60;

    public function __construct(
        private UrlGeneratorInterface $urls,
        private TranslatorInterface $translator,
        private RequestStack $requestStack,
        private CreditNoteListReader $reader,
    ) {
    }

    /**
     * The titles of the statuses and types and the symbols of the currencies are read once for
     * the page, not once per row.
     *
     * @param iterable<CreditNote> $creditNotes
     *
     * @return list<array<string, mixed>>
     */
    public function presentAll(iterable $creditNotes, string $locale): array
    {
        $statuses = array_column($this->reader->statuses($locale), null, 'id');
        $types = array_column($this->reader->types($locale), null, 'id');
        $symbols = [];

        $rows = [];
        foreach ($creditNotes as $creditNote) {
            $currencyId = (int) $creditNote->getCurrencyId();
            $symbols[$currencyId] ??= (string) CurrencyQuery::create()->findPk($currencyId)?->getSymbol();

            $rows[] = $this->present(
                $creditNote,
                $statuses[(int) $creditNote->getStatusId()] ?? self::NO_OPTION,
                $types[(int) $creditNote->getTypeId()] ?? self::NO_OPTION,
                $symbols[$currencyId],
            );
        }

        return $rows;
    }

    /**
     * @param array{title: string, color: string} $status
     * @param array{title: string, color: string} $type
     *
     * @return array<string, mixed>
     */
    private function present(CreditNote $creditNote, array $status, array $type, string $currencySymbol): array
    {
        $id = (int) $creditNote->getId();
        $viewUrl = $this->urls->generate('credit_note_view', ['id' => $id]);

        return [
            'id' => $id,
            'ref' => (string) $creditNote->getRef(),
            'label' => (string) $creditNote->getRef() !== '' ? (string) $creditNote->getRef() : '#'.$id,
            'ref_html' => $this->renderRef($creditNote, $viewUrl),
            'invoice_html' => $this->renderInvoice($creditNote),
            'customer_html' => $this->renderCustomer($creditNote),
            'order_html' => $this->renderOrder($creditNote),
            'status_id' => (int) $creditNote->getStatusId(),
            'status' => $status['title'],
            'status_color' => $status['color'],
            'type_id' => (int) $creditNote->getTypeId(),
            'type' => $type['title'],
            'type_color' => $type['color'],
            'amount_html' => $this->renderAmount($creditNote, $currencySymbol),
            'created_html' => $this->renderDate($creditNote->getCreatedAt()),
            'updated_html' => $this->renderDate($creditNote->getUpdatedAt()),
            'numbered' => (string) $creditNote->getInvoiceRef() !== '',
            'view_url' => $viewUrl,
            'delete_url' => $this->urls->generate('credit_note_delete', ['id' => $id]),
            'pdf_url' => $this->urls->generate('credit_note_invoice_pdf', ['creditNoteId' => $id, 'browser' => 0]),
        ];
    }

    /**
     * The reference opens the modal of the credit note, as its row buttons do.
     */
    private function renderRef(CreditNote $creditNote, string $viewUrl): string
    {
        $ref = (string) $creditNote->getRef();

        return \sprintf(
            '<button type="button" class="btn btn-link p-0 fw-semibold text-decoration-none text-start" data-bs-toggle="modal" data-bs-target="#modal-credit-note" data-ajax-url="%s" data-action="view" data-testid="credit-note-ref-link">%s</button>',
            self::escape($viewUrl),
            self::escape($ref !== '' ? $ref : '#'.$creditNote->getId()),
        );
    }

    /**
     * The invoice reference of the credit note (drawn on acceptance, from the series of the
     * invoices when `invoice_ref_with_thelia_order` is on) and the date of its document.
     */
    private function renderInvoice(CreditNote $creditNote): string
    {
        $number = (string) $creditNote->getInvoiceRef();
        if ($number === '') {
            return \sprintf(
                '<span class="text-muted small fst-italic">%s</span>',
                self::escape($this->translator->trans('Not numbered yet', [], CreditNoteModule::DOMAIN_MESSAGE)),
            );
        }

        $date = $creditNote->getInvoiceDate();
        if (null === $date) {
            return \sprintf('<div data-testid="credit-note-invoice-ref">%s</div>', self::escape($number));
        }

        return \sprintf(
            '<div data-testid="credit-note-invoice-ref">%s</div><div class="text-muted small"><time datetime="%s">%s</time></div>',
            self::escape($number),
            self::escape($date->format(\DateTimeInterface::ATOM)),
            self::escape($date->format('d/m/Y')),
        );
    }

    private function renderCustomer(CreditNote $creditNote): string
    {
        $customer = $creditNote->getCustomer();
        if (null === $customer) {
            return '<span class="text-muted">-</span>';
        }

        $country = $creditNote->getCreditNoteAddress()?->getCountry();

        return \sprintf(
            '<div class="bo-order-customer"><a class="fw-semibold text-reset text-decoration-none" href="%s">%s%s %s</a><div class="text-muted small text-truncate">%s</div></div>',
            self::escape($this->urls->generate('admin.customer.update.view', ['customer_id' => $customer->getId()])),
            self::countryFlag($country?->getIsoalpha2()),
            self::escape((string) $customer->getFirstname()),
            self::escape((string) $customer->getLastname()),
            self::escape((string) $customer->getEmail()),
        );
    }

    private function renderOrder(CreditNote $creditNote): string
    {
        $order = $creditNote->getOrder();
        if (null === $order) {
            return '<span class="text-muted">-</span>';
        }

        return \sprintf(
            '<a href="%s" class="text-decoration-none">%s</a>',
            self::escape($this->urls->generate('admin.order.update.view', ['order_id' => $order->getId()])),
            self::escape((string) $order->getRef()),
        );
    }

    private function renderAmount(CreditNote $creditNote, string $currencySymbol): string
    {
        return \sprintf(
            '<div class="text-nowrap" dir="ltr">%s</div><div class="text-muted small text-nowrap">%s</div>',
            self::escape($this->formatMoney($creditNote->getTotalPriceWithTax(), $currencySymbol)),
            self::escape($this->translator->trans('%amount% excl. tax', ['%amount%' => $this->formatMoney($creditNote->getTotalPrice(), $currencySymbol)], CreditNoteModule::DOMAIN_MESSAGE)),
        );
    }

    private function renderDate(?\DateTimeInterface $date): string
    {
        if (null === $date) {
            return '<span class="text-muted">-</span>';
        }

        $display = $date->format('d/m/Y H:i');
        $age = $this->age($date);

        return \sprintf(
            '<time datetime="%s" title="%s"><div>%s</div>%s</time>',
            self::escape($date->format(\DateTimeInterface::ATOM)),
            self::escape($display),
            self::escape($display),
            null === $age ? '' : '<div class="text-muted small">'.self::escape($age).'</div>',
        );
    }

    /**
     * The age of a recent date; none past 30 days, where it would only repeat the date, nor for
     * a date ahead of the clock (a shifted server clock or an import), which has no age.
     *
     * A date saved a moment ago can read up to a second ahead: Propel writes the microseconds
     * and the DATETIME column rounds them to the nearest second. That is still "just now".
     */
    private function age(\DateTimeInterface $date): ?string
    {
        $seconds = time() - $date->getTimestamp();

        return match (true) {
            $seconds < -self::CLOCK_TOLERANCE => null,
            $seconds < 60 => $this->translator->trans('Just now', [], CreditNoteModule::DOMAIN_MESSAGE),
            $seconds < 3600 => $this->translator->trans('%count% min ago', ['%count%' => intdiv($seconds, 60)], CreditNoteModule::DOMAIN_MESSAGE),
            $seconds < 86400 => $this->translator->trans('%count% h ago', ['%count%' => intdiv($seconds, 3600)], CreditNoteModule::DOMAIN_MESSAGE),
            $seconds < 30 * 86400 => $this->translator->trans('%count% d ago', ['%count%' => intdiv($seconds, 86400)], CreditNoteModule::DOMAIN_MESSAGE),
            default => null,
        };
    }

    private function formatMoney(float|string|null $amount, string $currencySymbol): string
    {
        return MoneyFormat::getInstance($this->requestStack->getCurrentRequest())
            ->format((float) $amount, null, null, null, $currencySymbol);
    }

    private static function countryFlag(?string $iso): string
    {
        if (null === $iso || 1 !== preg_match('/^[A-Za-z]{2}$/D', $iso)) {
            return '';
        }

        $offset = 0x1F1E6 - \ord('A');
        $upper = strtoupper($iso);

        return mb_chr(\ord($upper[0]) + $offset).mb_chr(\ord($upper[1]) + $offset).' ';
    }

    private static function escape(string $value): string
    {
        return htmlspecialchars($value, \ENT_QUOTES | \ENT_HTML5);
    }
}
