<?php

declare(strict_types=1);

namespace CreditNote\Service;

use CreditNote\Exception\CreditNoteReservationException;
use CreditNote\Model\CartCreditNote;
use CreditNote\Model\CartCreditNoteQuery;
use CreditNote\Model\CreditNote;
use CreditNote\Model\CreditNoteQuery;
use CreditNote\Model\OrderCreditNoteQuery;
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
 *
 * A reservation may stop standing after it was written: the credit note spent on another cart
 * of the customer or no longer accepted, the cart worth less than a credit note to spend whole.
 * What the credit notes pay (amountReservedFor) is told from what still stands and writes
 * nothing; the cart is put right (putRight) where the buyer can be told: on the payment step,
 * and when an order is placed (CheckoutCreditNotesGuard).
 *
 * A credit note pays in its own currency only: no rate is applied.
 */
#[Autoconfigure(public: true)]
final readonly class CartCreditNoteReservation
{
    private const KEPT = 'kept';
    private const REDUCED = 'reduced';
    private const GONE = 'gone';
    private const TOO_BIG = 'too_big';

    public function __construct(
        private CreditNoteBalance $balance,
        #[Autowire(service: 'service_container')]
        private ContainerInterface $container,
    ) {
    }

    /**
     * The credit notes the customer of the cart may pay with: accepted, theirs, in the currency
     * of the cart, with something left. A cart without customer has none, and nothing is read.
     *
     * @return list<array{creditNote: CreditNote, balance: float, reserved: bool}>
     */
    public function choicesFor(Cart $cart, ?ConnectionInterface $connection = null): array
    {
        $customerId = $cart->getCustomerId();

        if (null === $customerId) {
            return [];
        }

        $query = CreditNoteQuery::create()
            ->filterByCustomerId($customerId)
            ->useCreditNoteStatusQuery()
                ->filterByInvoiced(true)
                ->filterByUsed(false)
            ->endUse()
            ->orderById();

        if (null !== $cart->getCurrencyId()) {
            $query->filterByCurrencyId($cart->getCurrencyId());
        }

        $creditNotes = iterator_to_array($query->find($connection), false);

        if ([] === $creditNotes) {
            return [];
        }

        $reservedIds = array_map(
            static fn (CartCreditNote $reservation): int => (int) $reservation->getCreditNoteId(),
            $this->reservationsOf((int) $cart->getId(), $connection),
        );
        $balances = $this->balance->remainingOfAll($creditNotes, $connection);
        $choices = [];

        foreach ($creditNotes as $creditNote) {
            $balance = $balances[(int) $creditNote->getId()] ?? 0.0;
            $reserved = \in_array((int) $creditNote->getId(), $reservedIds, true);

            // One with nothing left is only listed while it is on the cart.
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
            || !$this->isInTheCurrencyOf($cart, $creditNote)
            || null === $status
            || !$status->getInvoiced()
            || $status->getUsed()
        ) {
            throw CreditNoteReservationException::notUsable($creditNoteId);
        }

        $balance = Cents::of($this->balance->remaining($creditNote, $connection));

        if ($balance <= 0) {
            throw CreditNoteReservationException::notUsable($creditNoteId);
        }

        if (!$creditNote->getAllowPartialUse()) {
            $due = Cents::of($this->totalOf($cart)) - $this->wholeAmountsOn($cart, $creditNoteId, $connection);

            if ($balance > $due) {
                throw CreditNoteReservationException::wholeBalanceExceedsCart((string) $creditNote->getRef(), Cents::toFloat($balance), Cents::toFloat(max(0, $due)));
            }
        }

        $reservation = CartCreditNoteQuery::create()
            ->filterByCartId($cart->getId())
            ->filterByCreditNoteId($creditNoteId)
            ->findOneOrCreate($connection);
        $reservation->setAmountPrice(Cents::toDecimal($balance));
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
     * Writes on the cart what still stands of its reservations: the credit notes that can no
     * longer pay anything are taken off, the ones to spend whole that the cart is no longer
     * worth too, and one partly spent elsewhere is brought down to what is left of it.
     *
     * The credit notes an order of the cart has consumed are left as they are, at the amounts
     * kept at reservation: the answer is the same once the part is settled.
     *
     * @return array{gone: list<int>, tooBig: list<int>, reduced: list<int>} the credit notes
     *                                                                       changed, empty lists when the cart was right
     */
    public function putRight(Cart $cart, ?ConnectionInterface $connection = null): array
    {
        $changes = ['gone' => [], 'tooBig' => [], 'reduced' => []];

        foreach ($this->standingOn($cart, $connection) as $standing) {
            $reservation = $standing['reservation'];
            $creditNoteId = (int) $reservation->getCreditNoteId();

            if (self::KEPT === $standing['fate']) {
                continue;
            }

            if (self::REDUCED === $standing['fate']) {
                $changes['reduced'][] = $creditNoteId;
                $reservation->setAmountPrice(Cents::toDecimal($standing['amount']));
                $reservation->save($connection);

                continue;
            }

            $changes[self::GONE === $standing['fate'] ? 'gone' : 'tooBig'][] = $creditNoteId;
            $reservation->delete($connection);
        }

        return $changes;
    }

    /**
     * What the credit notes on the cart are meant to pay, as a decimal string with six decimals.
     * The core caps it to what the order still owes.
     *
     * Told from what still stands, and nothing is written: the core asks at every placement,
     * inside its own transaction too. Nothing is read for a cart without reservation, and the
     * total of the cart is only computed when a credit note to spend whole is on it.
     */
    public function amountReservedFor(Cart $cart, ?ConnectionInterface $connection = null): string
    {
        return Cents::toDecimal(array_sum(array_column($this->standingOn($cart, $connection), 'amount')));
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
            static fn (CartCreditNote $a, CartCreditNote $b): int => CreditNoteSpendingOrder::compare($a->getCreditNote(), $b->getCreditNote()),
        );

        return $reservations;
    }

    /**
     * Each reservation of the cart with what it still pays, in cents, and what became of it.
     * Read from the database, not from the models: the instance pool may hold the credit note
     * as it was when it was chosen.
     *
     * @return list<array{reservation: CartCreditNote, amount: int, fate: string}>
     */
    private function standingOn(Cart $cart, ?ConnectionInterface $connection): array
    {
        $cartId = (int) $cart->getId();
        $standing = [];
        $due = null;

        foreach ($this->reservationsOf($cartId, $connection) as $reservation) {
            $creditNoteId = (int) $reservation->getCreditNoteId();
            $amount = Cents::of($reservation->getAmountPrice());
            $fate = self::KEPT;
            $left = $this->leftToTheCustomerOf($cart, $creditNoteId, $connection);

            if ($left < $amount && !$this->paidAnOrderOf($cartId, $creditNoteId, $connection)) {
                $fate = $left <= 0 ? self::GONE : self::REDUCED;
                $amount = max(0, $left);
            }

            if (self::GONE !== $fate && !$reservation->getCreditNote($connection)?->getAllowPartialUse()) {
                $due ??= Cents::of($this->totalOf($cart));

                if ($amount > $due) {
                    $fate = self::TOO_BIG;
                    $amount = 0;
                } else {
                    $due -= $amount;
                }
            }

            $standing[] = ['reservation' => $reservation, 'amount' => $amount, 'fate' => $fate];
        }

        return $standing;
    }

    /**
     * What the credit note can still pay for the customer of the cart, in cents: nothing when
     * it is not theirs, not accepted, used or in another currency.
     */
    private function leftToTheCustomerOf(Cart $cart, int $creditNoteId, ?ConnectionInterface $connection): int
    {
        $creditNote = CreditNoteQuery::create()
            ->filterById($creditNoteId)
            ->filterByCustomerId((int) $cart->getCustomerId())
            ->useCreditNoteStatusQuery()
                ->filterByInvoiced(true)
                ->filterByUsed(false)
            ->endUse()
            ->findOne($connection);

        if (null === $creditNote || !$this->isInTheCurrencyOf($cart, $creditNote)) {
            return 0;
        }

        return Cents::of($this->balance->remaining($creditNote, $connection));
    }

    private function isInTheCurrencyOf(Cart $cart, CreditNote $creditNote): bool
    {
        return null === $cart->getCurrencyId() || (int) $cart->getCurrencyId() === (int) $creditNote->getCurrencyId();
    }

    private function paidAnOrderOf(int $cartId, int $creditNoteId, ?ConnectionInterface $connection): bool
    {
        return OrderCreditNoteQuery::create()
            ->filterByCreditNoteId($creditNoteId)
            ->useOrderQuery()
                ->filterByCartId($cartId)
            ->endUse()
            ->exists($connection);
    }

    /** The credit notes to spend whole already on the cart, in cents, one left out. */
    private function wholeAmountsOn(Cart $cart, int $exceptCreditNoteId, ?ConnectionInterface $connection): int
    {
        $amount = 0;

        foreach ($this->reservationsOf((int) $cart->getId(), $connection) as $reservation) {
            if ((int) $reservation->getCreditNoteId() !== $exceptCreditNoteId && !$reservation->getCreditNote()->getAllowPartialUse()) {
                $amount += Cents::of($reservation->getAmountPrice());
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
