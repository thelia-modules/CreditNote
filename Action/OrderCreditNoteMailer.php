<?php

namespace CreditNote\Action;

use CreditNote\CreditNote;
use CreditNote\Event\OrderCreditNoteEvent;
use CreditNote\Event\OrderCreditNoteEvents;
use CreditNote\Model\OrderCreditNote;
use Propel\Runtime\Map\TableMap;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Thelia\Mailer\MailerFactory;

/**
 * Sends mails related to order credit notes.
 */
class OrderCreditNoteMailer implements EventSubscriberInterface
{
    /**
     * Mail service.
     * @var MailerFactory
     */
    protected $mailer;

    public function __construct(MailerFactory $mailer)
    {
        $this->mailer = $mailer;
    }

    public static function getSubscribedEvents()
    {
        return [
            OrderCreditNoteEvents::CREATE => ["notifyCustomerOfNewOrderCreditNote", 64],
        ];
    }

    public function notifyCustomerOfNewOrderCreditNote(OrderCreditNoteEvent $event)
    {
        // do not send a notification if no credit note has been created
        /** @var OrderCreditNote $orderCreditNote */
        if (null === $orderCreditNote = $event->getOrderCreditNote()) {
            return;
        }

        $this->mailer->sendEmailToCustomer(
            CreditNote::MESSAGE_NAME_ORDER_CREDIT_NOTE_CREATED_NOTIFY_CUSTOMER,
            $orderCreditNote->getOrder()->getCustomer(),
            [
                "order_credit_note_id" => $orderCreditNote->getId(),
                "order_ref" => $orderCreditNote->getOrder()->getRef(),
            ]
        );
    }
}
