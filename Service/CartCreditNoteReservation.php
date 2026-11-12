<?php

declare(strict_types=1);

namespace CreditNote\Service;

use CreditNote\Exception\CreditNoteReservationException;
use CreditNote\Model\CartCreditNote;
use CreditNote\Model\CartCreditNoteQuery;
use CreditNote\Model\CreditNote;
use CreditNote\Model\CreditNoteQuery;
use Propel\Runtime\Connection\ConnectionInterface;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Thelia\Model\Cart;
use Thelia\Module\BaseModule;

/**
 * The credit notes a buyer put on a cart to pay part of its order: the only writer of
 * `cart_credit_note`. A reservation keeps the balance the credit note had when it was chosen;
 * nothing is consumed before the order is paid (CreditNotePaymentPart).
 *
 * Several credit notes may be put on one cart. One that forbids partial use is spent whole or
 * not at all: it is refused when the cart is worth less than the credit notes of that kind
 * already on it plus this one, so the buyer never loses what is left without being told.
 */
#[Autoconfigure(public: true)]
final readonly class CartCreditNoteReservation
{
    private const TOLERANCE = 0.005;

    public function __construct(
        private CreditNoteBalance $balance,
        #[Autowire(service: 'service_container')]
        private ContainerInterface $container,
    ) {
    }

    /**
     * The credit notes the customer of the cart may pay with: accepted, theirs, with something
     * left. A cart without customer has none, and nothing is read.
     *
     * @return list<array{creditNote: CreditNote, balance: float, reserved: bool}>
     */
    public function choicesFor(Cart $cart, ?ConnectionInterface $connection = null): array
    {
        $customerId = $cart->getCustomerId();

        if (null === $customerId) {
            return [];
        }

        $creditNotes = CreditNoteQuery::create()
            ->filterByCustomerId($customerId)
            ->useCreditNoteStatusQuery()
                ->filterByInvoiced(true)
                ->filterByUsed(false)
            ->endUse()
            ->orderById()
            ->find($connection);

        if (0 === \count($creditNotes)) {
            return [];
        }

        $reservedIds = array_map(
            static fn (CartCreditNote $reservation): int => (int) $reservation->getCreditNoteId(),
            $this->reservationsOf((int) $cart->getId(), $connection),
        );
        $choices = [];

        foreach ($creditNotes as $creditNote) {
            $balance = $this->balance->remaining($creditNote, $connection);
            $reserved = \in_array((int) $creditNote->getId(), $reservedIds, true);

            // A credit note already on the cart stays listed once its order consumed it, so
            // the buyer of an unpaid order still sees what pays it.
            if ($balance > 0 || $reserved) {
                $choices[] = ['creditNote' => $creditNote, 'balance' => $balance, 'reserved' => $reserved];
            }
        }

        return $choices;
    }

    /**
     * @throws CreditNoteReservationException when the credit note may not be put on this cart
     */
    public function reserve(Cart $cart, int $creditNoteId, ?ConnectionInterface $connection = null): void
    {
        $creditNote = CreditNoteQuery::create()->findPk($creditNoteId, $connection);
        $status = $creditNote?->getCreditNoteStatus($connection);

        if (null === $creditNote
            || null === $cart->getCustomerId()
            || (int) $creditNote->getCustomerId() !== (int) $cart->getCustomerId()
            || null === $status
            || !$status->getInvoiced()
            || $status->getUsed()
        ) {
            throw CreditNoteReservationException::notUsable($creditNoteId);
        }

        $balance = $this->balance->remaining($creditNote, $connection);

        if ($balance <= 0) {
            throw CreditNoteReservationException::notUsable($creditNoteId);
        }

        if (!$creditNote->getAllowPartialUse()) {
            $due = $this->totalOf($cart) - $this->wholeAmountsOn($cart, $creditNoteId, $connection);

            if ($balance > $due + self::TOLERANCE) {
                throw CreditNoteReservationException::wholeBalanceExceedsCart((string) $creditNote->getRef(), $balance, max(0.0, $due));
            }
        }

        $reservation = CartCreditNoteQuery::create()
            ->filterByCartId($cart->getId())
            ->filterByCreditNoteId($creditNoteId)
            ->findOneOrCreate($connection);
        $reservation->setAmountPrice(\sprintf('%.6F', $balance));
        $reservation->save($connection);
    }

    public function release(Cart $cart, int $creditNoteId, ?ConnectionInterface $connection = null): void
    {
        CartCreditNoteQuery::create()
            ->filterByCartId($cart->getId())
            ->filterByCreditNoteId($creditNoteId)
            ->delete($connection);
    }

    /**
     * Takes off the cart the credit notes to spend whole that it is no longer worth, after its
     * content changed. Compared with the amounts kept at reservation, never with the balances:
     * the answer is the same once the order of the cart has consumed them.
     *
     * @return list<int> the credit notes taken off
     */
    public function dropWhatNoLongerFits(Cart $cart, ?ConnectionInterface $connection = null): array
    {
        $dropped = [];
        $due = null;

        foreach ($this->reservationsOf((int) $cart->getId(), $connection) as $reservation) {
            if ($reservation->getCreditNote($connection)?->getAllowPartialUse()) {
                continue;
            }

            $due ??= $this->totalOf($cart);
            $amount = (float) $reservation->getAmountPrice();

            if ($amount > $due + self::TOLERANCE) {
                $dropped[] = (int) $reservation->getCreditNoteId();
                $reservation->delete($connection);

                continue;
            }

            $due -= $amount;
        }

        return $dropped;
    }

    /**
     * What the credit notes on the cart are meant to pay, as a decimal string with six decimals:
     * one query, on the reservations only. The core caps it to what the order still owes.
     */
    public function amountReservedFor(Cart $cart, ?ConnectionInterface $connection = null): string
    {
        $reserved = CartCreditNoteQuery::create()
            ->filterByCartId($cart->getId())
            ->withColumn('COALESCE(SUM(cart_credit_note.amount_price), 0)', 'reserved_amount')
            ->select(['reserved_amount'])
            ->findOne($connection);

        return \sprintf('%.6F', (float) $reserved);
    }

    /**
     * The reservations of a cart in the order they are spent: the credit notes to spend whole
     * first, then the others, the oldest first.
     *
     * @return list<CartCreditNote>
     */
    public function reservationsOf(int $cartId, ?ConnectionInterface $connection = null): array
    {
        $reservations = iterator_to_array(
            CartCreditNoteQuery::create()
                ->filterByCartId($cartId)
                ->joinWithCreditNote()
                ->orderByCreditNoteId()
                ->find($connection),
            false,
        );

        usort(
            $reservations,
            static fn (CartCreditNote $a, CartCreditNote $b): int => [(int) $a->getCreditNote()->getAllowPartialUse(), (int) $a->getCreditNoteId()]
                <=> [(int) $b->getCreditNote()->getAllowPartialUse(), (int) $b->getCreditNoteId()],
        );

        return $reservations;
    }

    private function wholeAmountsOn(Cart $cart, int $exceptCreditNoteId, ?ConnectionInterface $connection): float
    {
        $amount = 0.0;

        foreach ($this->reservationsOf((int) $cart->getId(), $connection) as $reservation) {
            if ((int) $reservation->getCreditNoteId() !== $exceptCreditNoteId && !$reservation->getCreditNote()->getAllowPartialUse()) {
                $amount += (float) $reservation->getAmountPrice();
            }
        }

        return $amount;
    }

    /**
     * The total the cart shows the buyer, the way the core totals it to split the payment
     * (OrderPaymentComposer): goods, discount and postage, taxed for the delivery country.
     */
    private function totalOf(Cart $cart): float
    {
        [$country, $state] = BaseModule::resolveTaxationPlace($cart, $this->container);

        return round((float) $cart->getTaxedAmount($country, true, $state, true, true), 2);
    }
}
