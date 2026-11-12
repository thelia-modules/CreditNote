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
use Symfony\Contracts\Service\ResetInterface;
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
final class CartCreditNoteReservation implements ResetInterface
{
    private const TOLERANCE = 0.005;

    /**
     * The credit notes taken off a cart while this request was served, by cart id: what the
     * payment step tells the buyer, whoever read the cart first.
     *
     * @var array<int, list<int>>
     */
    private array $dropped = [];

    public function __construct(
        private readonly CreditNoteBalance $balance,
        #[Autowire(service: 'service_container')]
        private readonly ContainerInterface $container,
    ) {
    }

    public function reset(): void
    {
        $this->dropped = [];
    }

    /**
     * @return list<int> the credit notes taken off the cart since the request began
     */
    public function droppedFrom(Cart $cart): array
    {
        return $this->dropped[(int) $cart->getId()] ?? [];
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
                $this->dropped[(int) $cart->getId()][] = (int) $reservation->getCreditNoteId();
                $reservation->delete($connection);

                continue;
            }

            $due -= $amount;
        }

        return $dropped;
    }

    /**
     * What the credit notes on the cart are meant to pay, as a decimal string with six decimals.
     * The core caps it to what the order still owes.
     *
     * A credit note to spend whole that the cart is no longer worth is taken off here, before
     * the amount is told: the payment step would otherwise show a part the order cannot be
     * paid with. One query on the reservations for a cart without such a credit note, and the
     * total of the cart is only computed when there is one.
     */
    public function amountReservedFor(Cart $cart, ?ConnectionInterface $connection = null): string
    {
        $this->dropWhatNoLongerFits($cart, $connection);
        $reserved = 0.0;

        foreach ($this->reservationsOf((int) $cart->getId(), $connection) as $reservation) {
            $reserved += (float) $reservation->getAmountPrice();
        }

        return \sprintf('%.6F', $reserved);
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
