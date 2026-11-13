<?php

declare(strict_types=1);

namespace CreditNote\EventListener;

use CreditNote\Exception\CreditNotesChangedException;
use CreditNote\Service\CartCreditNoteReservation;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Thelia\Core\Event\Order\OrderEvent;
use Thelia\Core\Event\TheliaEvents;
use Thelia\Model\CartQuery;

/**
 * Refuses to place an order whose credit notes changed while it was being placed.
 *
 * The core reads what the credit notes pay before it writes the order, from the reservations
 * that still stand (CartCreditNoteReservation). When one no longer stands as it was written, a
 * credit note spent elsewhere meanwhile for instance, the order would be placed for another
 * amount than the payment step showed the buyer. The cart is put right here and the order
 * refused once: the buyer is sent back to the payment step, where the amounts are the new ones,
 * and the same request goes through next.
 */
final readonly class CheckoutCreditNotesGuard implements EventSubscriberInterface
{
    public function __construct(
        private CartCreditNoteReservation $reservation,
        private TranslatorInterface $translator,
    ) {
    }

    public function refuseWhatTheBuyerDidNotSee(OrderEvent $event): void
    {
        $cartId = $event->getOrder()->getCartId();
        $cart = null === $cartId ? null : CartQuery::create()->findPk($cartId);

        if (null === $cart || [] === array_merge(...array_values($this->reservation->putRight($cart)))) {
            return;
        }

        throw new CreditNotesChangedException($this->translator->trans('The credit notes of your cart have changed: check what is left to pay, then confirm your order again.', [], 'creditnote.fo.default'));
    }

    public static function getSubscribedEvents(): array
    {
        // Before anything the placement writes: the coupons (200), the order (128).
        return [TheliaEvents::ORDER_PAY => ['refuseWhatTheBuyerDidNotSee', 255]];
    }
}
