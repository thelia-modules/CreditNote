<?php

declare(strict_types=1);

namespace CreditNote\Loop;

use CreditNote\Model\OrderCreditNote as OrderCreditNoteModel;
use CreditNote\Model\OrderCreditNoteQuery;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Thelia\Core\Template\Element\BaseLoop;
use Thelia\Core\Template\Element\LoopResult;
use Thelia\Core\Template\Element\LoopResultRow;
use Thelia\Core\Template\Element\PropelSearchLoopInterface;
use Thelia\Core\Template\Loop\Argument\Argument;
use Thelia\Core\Template\Loop\Argument\ArgumentCollection;

/**
 * The credit notes that paid part of an order (`order-credit-note`): one row per credit note
 * used on the order, with its reference and the amount it paid. What the invoice of the order
 * prints its "paid by credit note" lines from.
 */
class OrderCreditNote extends BaseLoop implements PropelSearchLoopInterface
{
    protected $timestampable = true;

    protected function getArgDefinitions(): ArgumentCollection
    {
        return new ArgumentCollection(
            Argument::createIntTypeArgument('order', null, true),
        );
    }

    public function buildModelCriteria(): ModelCriteria
    {
        return OrderCreditNoteQuery::create()
            ->filterByOrderId($this->getOrder())
            ->joinWithCreditNote()
            ->orderByCreditNoteId();
    }

    public function parseResults(LoopResult $loopResult): LoopResult
    {
        /** @var OrderCreditNoteModel $use */
        foreach ($loopResult->getResultDataCollection() as $use) {
            $creditNote = $use->getCreditNote();
            $loopResultRow = new LoopResultRow($use);
            $loopResultRow
                ->set('ORDER_ID', $use->getOrderId())
                ->set('CREDIT_NOTE_ID', $use->getCreditNoteId())
                ->set('REF', $creditNote->getRef())
                ->set('INVOICE_REF', $creditNote->getInvoiceRef())
                ->set('AMOUNT', (float) $use->getAmountPrice());
            $this->addOutputFields($loopResultRow, $use);

            $loopResult->addRow($loopResultRow);
        }

        return $loopResult;
    }
}
