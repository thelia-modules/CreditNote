<?php

declare(strict_types=1);

namespace CreditNote\Api\State;

use CreditNote\Api\Resource\CreditNote as CreditNoteResource;
use CreditNote\Model\CreditNote;
use CreditNote\Service\CreditNoteBalance;

/**
 * A credit note as the API hands it over. The balance of a page is read in one query, by the
 * caller, and passed here.
 */
final readonly class CreditNoteResourceMapper
{
    public function __construct(
        private CreditNoteBalance $balance,
    ) {
    }

    /**
     * @param iterable<CreditNote> $creditNotes
     *
     * @return list<CreditNoteResource>
     */
    public function mapAll(iterable $creditNotes): array
    {
        $creditNotes = \is_array($creditNotes) ? $creditNotes : iterator_to_array($creditNotes, false);
        $balances = $this->balance->remainingOfAll($creditNotes);

        return array_map(
            fn (CreditNote $creditNote): CreditNoteResource => $this->map($creditNote, $balances[(int) $creditNote->getId()] ?? 0.0, false),
            $creditNotes,
        );
    }

    public function mapOne(CreditNote $creditNote): CreditNoteResource
    {
        return $this->map($creditNote, $this->balance->remaining($creditNote), true);
    }

    private function map(CreditNote $creditNote, float $balance, bool $withLines): CreditNoteResource
    {
        $resource = new CreditNoteResource();
        $resource->id = (int) $creditNote->getId();
        $resource->ref = $creditNote->getRef();
        $resource->invoiceRef = $creditNote->getInvoiceRef();
        $resource->invoiceDate = $creditNote->getInvoiceDate();
        $resource->status = $creditNote->getCreditNoteStatus()?->getCode();
        $resource->type = $creditNote->getCreditNoteType()?->getCode();
        $resource->customerId = $creditNote->getCustomerId();
        $resource->orderId = $creditNote->getOrderId();
        $resource->orderRef = $creditNote->getOrder()?->getRef();
        $resource->currency = $creditNote->getCurrency()?->getCode();
        $resource->totalPrice = \sprintf('%.6F', (float) $creditNote->getTotalPrice());
        $resource->totalPriceWithTax = \sprintf('%.6F', (float) $creditNote->getTotalPriceWithTax());
        $resource->balance = \sprintf('%.6F', $balance);
        $resource->allowPartialUse = (bool) $creditNote->getAllowPartialUse();
        $resource->createdAt = $creditNote->getCreatedAt();

        if ($withLines) {
            foreach ($creditNote->getCreditNoteDetails() as $detail) {
                $resource->lines[] = [
                    'orderProductId' => $detail->getOrderProductId(),
                    'title' => $detail->getTitle(),
                    'type' => $detail->getType(),
                    'quantity' => (int) $detail->getQuantity(),
                    'price' => \sprintf('%.6F', (float) $detail->getPrice()),
                    'priceWithTax' => \sprintf('%.6F', (float) $detail->getPriceWithTax()),
                ];
            }
        }

        return $resource;
    }
}
