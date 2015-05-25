<?php

namespace CreditNote\Hook\Back;

use CreditNote\Model\Map\OrderCreditNoteTableMap;
use CreditNote\Model\OrderCreditNoteQuery;
use Thelia\Core\Event\Hook\HookRenderEvent;
use Thelia\Core\Hook\BaseHook;
use Thelia\Model\OrderQuery;

/**
 * Order related back-office hooks.
 */
class OrderHook extends BaseHook
{
    /**
     * Add a credit note creation form to the order page.
     * @param HookRenderEvent $event
     */
    public function onOrderTabContent(HookRenderEvent $event)
    {
        $event->add($this->render(
            "order-credit-note-order-tab.html",
            [
                "order_id" => $event->getArgument("id"),
                "order_credit_note_order" => $this->getRequest()->get("order_credit_note_order"),
            ]
        ));
    }

    /**
     * Add a header for credit notes total amount in the orders table.
     * @param HookRenderEvent $event
     */
    public function onOrdersTableHeader(HookRenderEvent $event)
    {
        $event->add(
            $this->render(
                "order-credit-note-order-table-header.html"
            )
        );
    }

    /**
     * Add credit notes total amount on order rows.
     * @param HookRenderEvent $event
     */
    public function onOrdersTableRow(HookRenderEvent $event)
    {
        $order = OrderQuery::create()->findPk($event->getArgument("order_id"));
        if ($order === null) {
            return;
        }

        $totalCreditNotesAmountQuery = OrderCreditNoteQuery::create();
        $totalCreditNotesAmountQuery->filterByOrder($order);
        $totalCreditNotesAmountQuery->addAsColumn(
            "total_credit_notes_amount",
            "SUM(".OrderCreditNoteTableMap::AMOUNT.")"
        );
        $totalCreditNotesAmountQuery->select("total_credit_notes_amount");

        $totalCreditNotesAmount = $totalCreditNotesAmountQuery->findOne();

        $event->add(
            $this->render(
                "order-credit-note-order-table-row.html",
                [
                    "total_credit_notes_amount" => $totalCreditNotesAmount,
                    "order_currency_symbol" => $order->getCurrency()->getSymbol(),
                ]
            )
        );
    }
}
