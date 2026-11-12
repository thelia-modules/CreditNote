<?php

declare(strict_types=1);

namespace CreditNote\Service;

use CreditNote\Exception\CreditNoteConsumptionException;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\Propel;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use Thelia\Model\Cart;
use Thelia\Model\Map\OrderPaymentPartTableMap;
use Thelia\Model\OrderPaymentPart;

/**
 * The credit notes as a payment means settling part of an order inside the shop: what the module
 * class answers the core with (Thelia\Module\InstantPaymentModuleInterface).
 *
 * Everything is written on the connection of the core, inside the transaction it holds while it
 * settles or releases the parts of an order: nothing is begun or rolled back here, a refusal is
 * thrown.
 */
#[Autoconfigure(public: true)]
final readonly class CreditNotePaymentPart
{
    private const TOLERANCE = 0.005;

    public function __construct(
        private CartCreditNoteReservation $reservation,
        private CreditNoteConsumption $consumption,
    ) {
    }

    /**
     * Read from the reservations of the cart, never from the balances: the answer does not
     * change once the part is settled and the credit notes are empty.
     */
    public function amountAppliedTo(Cart $cart): string
    {
        return $this->reservation->amountReservedFor($cart);
    }

    /**
     * Spends the amount of the part on the credit notes of the cart the order was placed from:
     * the ones to spend whole first, then the others until the part is covered.
     *
     * @throws CreditNoteConsumptionException when the credit notes cannot pay the part
     */
    public function settle(OrderPaymentPart $part): void
    {
        $connection = $this->connection();
        $order = $part->getOrder($connection)
            ?? throw new CreditNoteConsumptionException('The payment part has no order.');
        $left = round((float) $part->getAmount(), 2);

        foreach ($this->reservation->reservationsOf((int) $order->getCartId(), $connection) as $reservation) {
            // Never more than the part still owes. A credit note to spend whole that the part
            // cannot take whole (the cart changed since it was chosen) is refused by the
            // consumption, under the lock, like one whose balance changed.
            $amount = min(round((float) $reservation->getAmountPrice(), 2), $left);

            if ($amount <= 0) {
                continue;
            }

            $this->consumption->consumeWithin($connection, (int) $reservation->getCreditNoteId(), $order, $amount, (int) $part->getId());
            $left = round($left - $amount, 2);
        }

        if ($left > self::TOLERANCE) {
            throw new CreditNoteConsumptionException(\sprintf('The credit notes of the cart leave %.2f of their part of order %s unpaid.', $left, $order->getRef()));
        }
    }

    /**
     * Gives the part back. The uses are found by the id of the part: a part already given back
     * has none left, and nothing is credited twice.
     */
    public function release(OrderPaymentPart $part): void
    {
        $this->consumption->giveBackWithin($this->connection(), (int) $part->getId());
    }

    private function connection(): ConnectionInterface
    {
        return Propel::getWriteConnection(OrderPaymentPartTableMap::DATABASE_NAME);
    }
}
