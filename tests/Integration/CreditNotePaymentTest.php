<?php

declare(strict_types=1);

namespace CreditNote\Tests\Integration;

use CreditNote\CreditNote;
use CreditNote\Exception\CreditNoteConsumptionException;
use CreditNote\Exception\CreditNoteReservationException;
use CreditNote\Exception\CreditNotesChangedException;
use CreditNote\Model\CartCreditNote;
use CreditNote\Model\CartCreditNoteQuery;
use CreditNote\Model\CreditNote as CreditNoteModel;
use CreditNote\Model\CreditNoteQuery;
use CreditNote\Model\CreditNoteStatusQuery;
use CreditNote\Model\OrderCreditNoteQuery;
use CreditNote\Service\CartCreditNoteReservation;
use CreditNote\Service\CreditNoteBalance;
use CreditNote\Service\CreditNoteConsumption;
use CreditNote\Tests\Support\CreditNoteFixture;
use CreditNote\Tests\Support\Payment\CreditNoteTestBalanceProvider;
use CreditNote\Tests\Support\Payment\CreditNoteTestInstantMeans;
use CreditNote\Tests\Support\Payment\CreditNoteTestPlainProvider;
use CreditNote\Tests\Support\Payment\PaysWithCreditNotes;
use CreditNote\Tests\Support\RecordsQueries;
use PHPUnit\Framework\Attributes\Test;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\Connection\ConnectionWrapper;
use Propel\Runtime\Connection\PdoConnection;
use Propel\Runtime\Propel;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Thelia\Domain\Payment\Enum\OrderPaymentPartState;
use Thelia\Domain\Payment\Exception\InstantPaymentRefusedException;
use Thelia\Domain\Payment\Exception\PaymentCompositionRefusedException;
use Thelia\Model\Cart;
use Thelia\Model\CurrencyQuery;
use Thelia\Model\Customer;
use Thelia\Model\Module;
use Thelia\Model\ModuleQuery;
use Thelia\Model\OrderQuery;
use Thelia\Test\FixtureFactory;
use Thelia\Test\IntegrationTestCase;
use Thelia\Test\Trait\ForgetsPooledModels;

/**
 * Paying an order with credit notes, through the checkout of the core: the credit notes the
 * buyer put on the cart settle their part when the order is paid, a provider charges the rest,
 * and a cancelled order gives them back. An accepted credit note of the fixture is worth 117.60
 * for one unit and 235.20 for two.
 */
final class CreditNotePaymentTest extends IntegrationTestCase
{
    use ForgetsPooledModels;
    use PaysWithCreditNotes;
    use RecordsQueries;

    private CreditNoteFixture $fixture;

    private Module $creditNoteModule;

    private Module $provider;

    private Customer $customer;

    protected function setUp(): void
    {
        parent::setUp();

        if (!ModuleQuery::create()->findOneByCode(CreditNote::getModuleCode())?->getActivate()) {
            self::markTestSkipped('The CreditNote module is not active in the test database.');
        }

        $this->fixture = new CreditNoteFixture($this->getPropelConnection());
        $this->creditNoteModule = $this->creditNoteAsPaymentModule();
        $this->provider = $this->installPaymentModule(CreditNoteTestBalanceProvider::class, 1001);
        $this->turnMandatoryConsentsOff();
        $this->customer = $this->fixture->customer();
    }

    protected function tearDown(): void
    {
        $this->uninstallThePaymentModules();
        self::forgetPooledModels();
        Propel::disableInstancePooling();

        parent::tearDown();
    }

    #[Test]
    public function aCreditNoteThatCoversTheOrderPaysItWithoutAnyProvider(): void
    {
        $creditNote = $this->acceptedCreditNote(2);
        $cart = $this->cartOf($this->customer, $this->creditNoteModule);
        $this->reservation()->reserve($cart, $creditNote->getId());

        $order = $this->pay($cart);

        $order = $this->rereadOrder($order);
        self::assertTrue($order->isPaid(false), 'the credit note paid the whole order');
        self::assertSame([], CreditNoteTestBalanceProvider::$paidOrderIds, 'no provider was asked');

        $part = $this->partOf($order, $this->creditNoteModule);
        self::assertSame(OrderPaymentPartState::SETTLED->value, $part->getState());

        $uses = OrderCreditNoteQuery::create()->filterByOrderId($order->getId())->find();
        self::assertCount(1, $uses);
        self::assertSame($creditNote->getId(), $uses->getFirst()->getCreditNoteId());
        self::assertSame($part->getId(), $uses->getFirst()->getOrderPaymentPartId(), 'the use remembers the part it settled');
        self::assertSame(round($order->getTotalAmount(), 2), round((float) $uses->getFirst()->getAmountPrice(), 2));
        self::assertSame(round(235.2 - $order->getTotalAmount(), 2), $this->balance()->remaining($creditNote), 'what the order did not need is left');
        self::assertSame('accepted', $this->statusOf($creditNote));
    }

    #[Test]
    public function aCreditNoteSmallerThanTheOrderLeavesTheRestToAProviderThatChargesTheBalance(): void
    {
        $creditNote = $this->acceptedCreditNote();
        $cart = $this->cartOf($this->customer, $this->provider, '300.000000');
        $this->reservation()->reserve($cart, $creditNote->getId());

        $order = $this->pay($cart);

        self::assertSame(OrderPaymentPartState::SETTLED->value, $this->partOf($order, $this->creditNoteModule)->getState());
        self::assertSame('117.600000', $this->partOf($order, $this->creditNoteModule)->getAmount());
        self::assertSame([$order->getId()], CreditNoteTestBalanceProvider::$paidOrderIds, 'the provider was asked for the rest');
        self::assertSame(
            round($order->getTotalAmount() - 117.6, 2),
            round((float) $this->providerInstance()->getAmountToCharge($this->rereadOrder($order)), 2),
            'the provider charges the total minus the credit note',
        );
        self::assertFalse($this->rereadOrder($order)->isPaid(false), 'the provider has not confirmed yet');
        self::assertSame(0.0, $this->balance()->remaining($creditNote));
        self::assertSame('used', $this->statusOf($creditNote), 'a credit note with nothing left is used');

        $this->confirmThePayment($order);

        self::assertTrue($this->rereadOrder($order)->isPaid(false));
    }

    #[Test]
    public function aCreditNoteGrantedBeyondItsOrderStillPaysOnceTheCeilingIsBackOn(): void
    {
        // A shop that granted more than an order was worth while the ceiling was off, then
        // turned it back on: spending such a credit note grants nothing more.
        CreditNote::setConfigValue(CreditNote::CONFIG_KEY_ORDER_CEILING, 0);
        $origin = $this->fixture->paidOrder($this->customer);
        $creditNote = $this->fixture->creditNote($this->customer, $origin, 'accepted');
        $creditNote->save();
        $other = $this->fixture->gesture($this->customer, $origin, 235.2);
        $other->save();
        CreditNote::setConfigValue(CreditNote::CONFIG_KEY_ORDER_CEILING, 1);

        $cart = $this->cartOf($this->customer, $this->provider, '300.000000');
        $this->reservation()->reserve($cart, $creditNote->getId());

        $order = $this->pay($cart);

        self::assertSame(OrderPaymentPartState::SETTLED->value, $this->partOf($order, $this->creditNoteModule)->getState());
        self::assertSame('used', $this->statusOf($creditNote));
    }

    #[Test]
    public function whatIsLeftOfACreditNotePaysTheNextOrders(): void
    {
        $creditNote = $this->acceptedCreditNote(2);

        $first = $this->cartOf($this->customer, $this->creditNoteModule);
        $this->reservation()->reserve($first, $creditNote->getId());
        $firstOrder = $this->pay($first);
        $left = round(235.2 - $firstOrder->getTotalAmount(), 2);
        self::assertSame($left, $this->balance()->remaining($creditNote));

        $second = $this->cartOf($this->customer, $this->creditNoteModule);
        $this->reservation()->reserve($second, $creditNote->getId());
        self::assertSame(\sprintf('%.6F', $left), $this->module()->getAmountAppliedToCart($second), 'only what is left is put on the second cart');
        $secondOrder = $this->pay($second);

        self::assertTrue($this->rereadOrder($secondOrder)->isPaid(false));
        self::assertSame(round($left - $secondOrder->getTotalAmount(), 2), $this->balance()->remaining($creditNote));
        self::assertSame(2, OrderCreditNoteQuery::create()->filterByCreditNoteId($creditNote->getId())->count());
    }

    #[Test]
    public function aCreditNoteToSpendWholeIsRefusedOnACartWorthLessAndNothingIsWritten(): void
    {
        $creditNote = $this->acceptedCreditNote(1, false);
        $cart = $this->cartOf($this->customer, $this->creditNoteModule);

        try {
            $this->reservation()->reserve($cart, $creditNote->getId());
            self::fail('the buyer would have lost what the cart does not need');
        } catch (CreditNoteReservationException $exception) {
            self::assertSame(CreditNoteReservationException::WHOLE_BALANCE_EXCEEDS_CART, $exception->reason);
        }

        self::assertSame(0, CartCreditNoteQuery::create()->filterByCartId($cart->getId())->count());
        self::assertSame('0.000000', $this->module()->getAmountAppliedToCart($cart));

        $larger = $this->cartOf($this->customer, $this->provider, '300.000000');
        $this->reservation()->reserve($larger, $creditNote->getId());
        self::assertSame('117.600000', $this->module()->getAmountAppliedToCart($larger), 'a cart worth more takes it whole');
    }

    #[Test]
    public function cancellingTheOrderGivesTheCreditNoteBackOnce(): void
    {
        $creditNote = $this->acceptedCreditNote();
        $cart = $this->cartOf($this->customer, $this->provider, '300.000000');
        $this->reservation()->reserve($cart, $creditNote->getId());
        $order = $this->pay($cart);
        self::assertSame('used', $this->statusOf($creditNote));

        $this->cancel($order);

        $part = $this->partOf($order, $this->creditNoteModule);
        self::assertSame(OrderPaymentPartState::RELEASED->value, $part->getState());
        self::assertSame(0, OrderCreditNoteQuery::create()->filterByOrderId($order->getId())->count(), 'the use is removed');
        self::assertSame(117.6, $this->balance()->remaining($creditNote));
        self::assertSame('accepted', $this->statusOf($creditNote), 'the credit note may be used again');

        // The core may ask again for a part it already got back (a cancellation replayed).
        $elsewhere = $this->fixture->paidOrder($this->customer);
        $this->consumption()->consume($creditNote->getId(), $elsewhere, 17.6);
        $this->module()->releasePart($part);

        self::assertSame(100.0, $this->balance()->remaining($creditNote), 'nothing is credited twice, and the use of another order is left alone');
    }

    #[Test]
    public function theAmountAppliedDoesNotChangeOnceThePartIsSettledAndTheUnpaidOrderIsPresentedAgain(): void
    {
        $creditNote = $this->acceptedCreditNote();
        $cart = $this->cartOf($this->customer, $this->provider, '300.000000');
        $this->reservation()->reserve($cart, $creditNote->getId());
        $before = $this->module()->getAmountAppliedToCart($cart);

        $order = $this->pay($cart);

        self::assertSame(0.0, $this->balance()->remaining($creditNote), 'the part is settled, the credit note is empty');
        self::assertSame('117.600000', $before);
        self::assertSame($before, $this->module()->getAmountAppliedToCart($cart));

        $again = $this->pay($cart);

        self::assertSame($order->getId(), $again->getId(), 'the same unpaid order is presented to the provider again');
        self::assertSame(1, OrderCreditNoteQuery::create()->filterByCreditNoteId($creditNote->getId())->count(), 'the credit note is consumed once');
    }

    #[Test]
    public function theCreditNoteOfAnotherCustomerOrOneNotAcceptedIsRefusedOnTheCartAndAtTheSettlement(): void
    {
        $stranger = $this->fixture->customer();
        $theirs = $this->fixture->creditNote($stranger, $this->fixture->paidOrder($stranger), 'accepted');
        $theirs->save();
        $draft = $this->fixture->creditNote($this->customer, $this->fixture->paidOrder($this->customer));
        $draft->save();
        $cart = $this->cartOf($this->customer, $this->provider, '300.000000');

        foreach ([$theirs->getId(), $draft->getId(), 999999] as $creditNoteId) {
            try {
                $this->reservation()->reserve($cart, $creditNoteId);
                self::fail('a credit note the buyer may not use was put on the cart');
            } catch (CreditNoteReservationException $exception) {
                self::assertSame(CreditNoteReservationException::NOT_USABLE, $exception->reason);
            }
        }

        self::assertSame(0, CartCreditNoteQuery::create()->filterByCartId($cart->getId())->count());

        // A reservation written behind the back of the module pays nothing: off the cart.
        (new CartCreditNote())->setCartId($cart->getId())->setCreditNoteId($theirs->getId())->setAmountPrice('117.600000')->save();

        self::assertSame('0.000000', $this->module()->getAmountAppliedToCart($cart));
        self::assertSame(['gone' => [$theirs->getId()], 'tooBig' => [], 'reduced' => []], $this->thePaymentStepIsShown($cart));
        self::assertSame(0, CartCreditNoteQuery::create()->filterByCartId($cart->getId())->count());
    }

    #[Test]
    public function aCreditNoteInAnotherCurrencyThanTheCartPaysNothing(): void
    {
        $creditNote = $this->acceptedCreditNote();
        $cart = $this->cartOf($this->customer, $this->provider, '300.000000');
        $cart->setCurrencyId($creditNote->getCurrencyId())->save();
        $this->reservation()->reserve($cart, $creditNote->getId());
        self::assertSame('117.600000', $this->module()->getAmountAppliedToCart($cart));

        // The buyer switches the shop to another currency: 117.60 of one is not 117.60 of the other.
        $otherCurrency = CurrencyQuery::create()->filterById($creditNote->getCurrencyId(), Criteria::NOT_EQUAL)->findOne();
        self::assertNotNull($otherCurrency, 'the shop has a second currency');
        $cart->setCurrencyId($otherCurrency->getId())->save();

        self::assertSame('0.000000', $this->module()->getAmountAppliedToCart($cart));
        self::assertSame([], $this->reservation()->choicesFor($cart), 'it is not offered on this cart');
        self::assertSame([$creditNote->getId()], $this->thePaymentStepIsShown($cart)['gone']);

        try {
            $this->reservation()->reserve($cart, $creditNote->getId());
            self::fail('a credit note paid a cart in another currency');
        } catch (CreditNoteReservationException $exception) {
            self::assertSame(CreditNoteReservationException::NOT_USABLE, $exception->reason);
        }

        // And under the lock, whatever put it there: an order in another currency is refused.
        $order = $this->fixture->paidOrder($this->customer);
        $order->setCurrencyId($otherCurrency->getId())->save();

        $this->expectException(CreditNoteConsumptionException::class);
        $this->expectExceptionMessage('is not in the currency of order');

        $this->consumption()->consume($creditNote->getId(), $order, 117.6);
    }

    #[Test]
    public function aCreditNoteThatChangesHandsWhileTheOrderIsBeingPaidIsRefusedAtTheSettlement(): void
    {
        $this->installPaymentModule(CreditNoteTestInstantMeans::class, 999);
        CreditNoteTestInstantMeans::$appliedAmount = '20';
        $creditNote = $this->acceptedCreditNote();
        $cart = $this->cartOf($this->customer, $this->provider, '300.000000');
        $this->reservation()->reserve($cart, $creditNote->getId());

        // After the payment was composed, before the credit notes settle their part.
        $stranger = $this->fixture->customer();
        CreditNoteTestInstantMeans::$onceSettled = static fn () => CreditNoteQuery::create()->filterById($creditNote->getId())->update(['CustomerId' => $stranger->getId()]);

        try {
            $this->pay($cart);
            self::fail('the credit note of another customer paid the order');
        } catch (InstantPaymentRefusedException) {
            self::assertSame(0, OrderCreditNoteQuery::create()->filterByCreditNoteId($creditNote->getId())->count());
            self::assertSame(117.6, $this->balance()->remaining($creditNote));
        }
    }

    #[Test]
    public function aProviderThatCannotChargeABalanceIsRefusedWhenTheCreditNoteLeavesSomethingToPay(): void
    {
        $plain = $this->installPaymentModule(CreditNoteTestPlainProvider::class, 1002);
        $creditNote = $this->acceptedCreditNote();
        $cart = $this->cartOf($this->customer, $plain, '300.000000');
        $this->reservation()->reserve($cart, $creditNote->getId());

        try {
            $this->pay($cart);
            self::fail('a provider charging the whole total was combined with a credit note');
        } catch (PaymentCompositionRefusedException $exception) {
            self::assertSame(PaymentCompositionRefusedException::PROVIDER_CANNOT_CHARGE_BALANCE, $exception->reason());
        }

        self::assertSame(117.6, $this->balance()->remaining($creditNote));
        self::assertSame(0, OrderCreditNoteQuery::create()->filterByCreditNoteId($creditNote->getId())->count());
    }

    #[Test]
    public function severalCreditNotesPayOneOrderAndComeBackTogether(): void
    {
        $whole = $this->acceptedCreditNote(1, false);
        $partial = $this->acceptedCreditNote(2);
        $cart = $this->cartOf($this->customer, $this->provider, '200.000000');
        $this->reservation()->reserve($cart, $partial->getId());
        $this->reservation()->reserve($cart, $whole->getId());
        self::assertSame('352.800000', $this->module()->getAmountAppliedToCart($cart));

        $order = $this->pay($cart);

        $total = round($this->rereadOrder($order)->getTotalAmount(), 2);
        self::assertSame(0.0, $this->balance()->remaining($whole), 'the credit note to spend whole goes first');
        self::assertSame('used', $this->statusOf($whole));
        self::assertSame(round(235.2 - ($total - 117.6), 2), $this->balance()->remaining($partial), 'the other one pays what is left of the order');
        self::assertSame(2, OrderCreditNoteQuery::create()->filterByOrderPaymentPartId($this->partOf($order, $this->creditNoteModule)->getId())->count());

        $this->cancel($order);

        self::assertSame(117.6, $this->balance()->remaining($whole));
        self::assertSame('accepted', $this->statusOf($whole));
        self::assertSame(235.2, $this->balance()->remaining($partial));
    }

    #[Test]
    public function theCreditNotesAreLockedInTheSameOrderWhenTheyPayAndWhenTheyComeBack(): void
    {
        // The older one may be spent in parts, the newer one whole: spent, so locked, newer first.
        $partial = $this->acceptedCreditNote();
        $whole = $this->acceptedCreditNote(1, false);
        self::assertLessThan($whole->getId(), $partial->getId());
        $cart = $this->cartOf($this->customer, $this->provider, '300.000000');
        $this->reservation()->reserve($cart, $partial->getId());
        $this->reservation()->reserve($cart, $whole->getId());
        // The lock binds its id, which the log does not show; the statement that follows it
        // names the credit note: its use on the order, looked for when paying, deleted when
        // coming back.
        $locked = static function (array $statements): array {
            $ids = [];
            $awaited = false;

            foreach ($statements as $sql) {
                if (str_contains($sql, 'FROM `credit_note` WHERE `id` = :id FOR UPDATE')) {
                    $awaited = true;
                } elseif ($awaited && 1 === preg_match('/`order_credit_note`\.`credit_note_id`\s*=\s*(\d+)/', $sql, $found)) {
                    $ids[] = (int) $found[1];
                    $awaited = false;
                }
            }

            return $ids;
        };

        $order = null;
        $paying = $this->queriesOf(function () use ($cart, &$order): void { $order = $this->pay($cart); });
        $whenPaying = $locked($paying);
        $whenComingBack = $locked($this->queriesOf(fn () => $this->cancel($order)));

        self::assertSame([$whole->getId(), $partial->getId()], $whenPaying, implode("\n", array_filter($paying, static fn (string $sql): bool => str_contains($sql, 'FOR UPDATE'))));
        self::assertSame($whenPaying, $whenComingBack, 'a payment and a cancellation at the same time never wait on each other');
    }

    #[Test]
    public function aCreditNoteToSpendWholeIsRefusedWhenTheOnesAlreadyOnTheCartLeaveTooLittle(): void
    {
        $first = $this->acceptedCreditNote(1, false);
        $second = $this->acceptedCreditNote(1, false);
        $cart = $this->cartOf($this->customer, $this->provider, '200.000000');
        $this->reservation()->reserve($cart, $first->getId());

        $this->expectException(CreditNoteReservationException::class);

        $this->reservation()->reserve($cart, $second->getId());
    }

    #[Test]
    public function aCreditNoteToSpendWholeIsTakenOffACartThatShrankBeforeItsAmountIsTold(): void
    {
        $whole = $this->acceptedCreditNote(1, false);
        $partial = $this->acceptedCreditNote(2);
        $cart = $this->cartOf($this->customer, $this->provider, '400.000000');
        $this->reservation()->reserve($cart, $whole->getId());
        $this->reservation()->reserve($cart, $partial->getId());
        self::assertSame('352.800000', $this->module()->getAmountAppliedToCart($cart));

        foreach ($cart->getCartItems() as $item) {
            $item->setPrice('50.000000')->setPromoPrice('50.000000')->save();
        }

        self::assertSame('235.200000', $this->module()->getAmountAppliedToCart($cart), 'the payment step is told what can still pay the cart');
        self::assertSame(2, CartCreditNoteQuery::create()->filterByCartId($cart->getId())->count(), 'telling the amount writes nothing');
        self::assertSame(['gone' => [], 'tooBig' => [$whole->getId()], 'reduced' => []], $this->thePaymentStepIsShown($cart), 'the buyer is told what was taken off');
        self::assertSame([$partial->getId()], CartCreditNoteQuery::create()->filterByCartId($cart->getId())->select(['CreditNoteId'])->find()->getData());

        $order = $this->pay($cart);

        self::assertSame(117.6, $this->balance()->remaining($whole), 'the credit note to spend whole is left whole');
        self::assertSame(round(235.2 - $this->rereadOrder($order)->getTotalAmount(), 2), $this->balance()->remaining($partial));
    }

    #[Test]
    public function aCreditNoteSpentElsewhereIsTakenOffTheCartThatStillHeldIt(): void
    {
        $creditNote = $this->acceptedCreditNote();
        $cart = $this->cartOf($this->customer, $this->provider, '300.000000');
        $this->reservation()->reserve($cart, $creditNote->getId());

        // The same credit note pays another order of the customer, placed from another cart.
        $this->consumption()->consume($creditNote->getId(), $this->fixture->paidOrder($this->customer), 117.6);

        self::assertSame('0.000000', $this->module()->getAmountAppliedToCart($cart), 'an empty credit note pays nothing');
        self::assertSame(1, CartCreditNoteQuery::create()->filterByCartId($cart->getId())->count(), 'telling the amount writes nothing');
        self::assertSame(['gone' => [$creditNote->getId()], 'tooBig' => [], 'reduced' => []], $this->thePaymentStepIsShown($cart), 'the buyer is told it is gone');
        self::assertSame(0, CartCreditNoteQuery::create()->filterByCartId($cart->getId())->count());

        $order = $this->pay($cart);

        self::assertSame([], OrderCreditNoteQuery::create()->filterByOrderId($order->getId())->find()->getData(), 'the order is placed without it');
        self::assertSame(
            round($order->getTotalAmount(), 2),
            round((float) $this->providerInstance()->getAmountToCharge($this->rereadOrder($order)), 2),
            'and the provider charges it whole',
        );
    }

    #[Test]
    public function anOrderIsRefusedOnceWhenItsCreditNotesChangedSinceThePaymentStepWasShown(): void
    {
        $spent = $this->acceptedCreditNote();
        $entered = $this->acceptedCreditNote(2);
        $cart = $this->cartOf($this->customer, $this->provider, '500.000000');
        $this->reservation()->reserve($cart, $spent->getId());
        $this->thePaymentStepIsShown($cart);

        // The buyer has the payment step on screen; another cart of theirs spends the credit note.
        $this->consumption()->consume($spent->getId(), $this->fixture->paidOrder($this->customer), 117.6);

        try {
            $this->pay($cart);
            self::fail('the order was placed for more than the payment step showed');
        } catch (CreditNotesChangedException $refusal) {
            self::assertStringContainsString('The credit notes of your cart have changed', $refusal->getMessage());
        }

        self::assertSame(0, OrderQuery::create()->filterByCartId($cart->getId())->count(), 'no order is written');
        self::assertSame([], CreditNoteTestBalanceProvider::$paidOrderIds, 'and the provider is not asked for anything');
        self::assertSame(0, CartCreditNoteQuery::create()->filterByCartId($cart->getId())->count(), 'the cart is put right, so the same request goes through next');

        // Sent back to the payment step, the buyer adds another credit note, partly spent while
        // they read: refused once more, for the same reason.
        $this->reservation()->reserve($cart, $entered->getId());
        $this->consumption()->consume($entered->getId(), $this->fixture->paidOrder($this->customer), 100.0);

        try {
            $this->pay($cart);
            self::fail('the order was placed with less credit note than the payment step showed');
        } catch (CreditNotesChangedException) {
        }

        $order = $this->pay($cart);

        self::assertSame('135.200000', $this->partOf($order, $this->creditNoteModule)->getAmount(), 'what the buyer has now seen pays the order');
    }

    #[Test]
    public function aCreditNotePartlySpentElsewherePaysWithWhatIsLeftOfIt(): void
    {
        $creditNote = $this->acceptedCreditNote(2);
        $cart = $this->cartOf($this->customer, $this->provider, '300.000000');
        $this->reservation()->reserve($cart, $creditNote->getId());

        $this->consumption()->consume($creditNote->getId(), $this->fixture->paidOrder($this->customer), 100.0);

        self::assertSame('135.200000', $this->module()->getAmountAppliedToCart($cart));
        self::assertSame(['gone' => [], 'tooBig' => [], 'reduced' => [$creditNote->getId()]], $this->thePaymentStepIsShown($cart));
        self::assertSame('135.200000', CartCreditNoteQuery::create()->filterByCartId($cart->getId())->findOne()?->getAmountPrice());

        $this->pay($cart);

        self::assertSame(0.0, $this->balance()->remaining($creditNote));
        self::assertSame('used', $this->statusOf($creditNote));
    }

    #[Test]
    public function aCreditNoteNoLongerAcceptedOrOfAnotherCustomerIsTakenOffTheCart(): void
    {
        $refused = $this->acceptedCreditNote();
        $theirs = $this->acceptedCreditNote();
        $cart = $this->cartOf($this->customer, $this->provider, '300.000000');
        $this->reservation()->reserve($cart, $refused->getId());
        $this->reservation()->reserve($cart, $theirs->getId());

        CreditNoteQuery::create()->filterById($refused->getId())->update(['StatusId' => CreditNoteStatusQuery::create()->findOneByCode('proposed')->getId()]);
        CreditNoteQuery::create()->filterById($theirs->getId())->update(['CustomerId' => $this->fixture->customer()->getId()]);

        self::assertSame('0.000000', $this->module()->getAmountAppliedToCart($cart));
        self::assertEqualsCanonicalizing([$refused->getId(), $theirs->getId()], $this->thePaymentStepIsShown($cart)['gone']);
    }

    #[Test]
    public function aCreditNoteEmptiedSinceItWasChosenRefusesTheOrderAndTheOtherMeansGetsItsPartBack(): void
    {
        $giftCard = $this->installPaymentModule(CreditNoteTestInstantMeans::class, 999);
        CreditNoteTestInstantMeans::$appliedAmount = '20';
        $creditNote = $this->acceptedCreditNote();
        $cart = $this->cartOf($this->customer, $this->provider, '300.000000');
        $this->reservation()->reserve($cart, $creditNote->getId());

        // Another order of the customer consumes the credit note while this one is being paid:
        // after the payment was composed, before the credit notes settle their part.
        $elsewhere = $this->fixture->paidOrder($this->customer);
        CreditNoteTestInstantMeans::$onceSettled = fn () => $this->consumption()->consume($creditNote->getId(), $elsewhere, 117.6);

        try {
            $this->pay($cart);
            self::fail('an empty credit note paid the order');
        } catch (InstantPaymentRefusedException) {
        }

        self::assertCount(1, CreditNoteTestInstantMeans::$settledPartIds, 'the other means was settled first');
        self::assertSame(CreditNoteTestInstantMeans::$settledPartIds, CreditNoteTestInstantMeans::$releasedPartIds, 'and got its part back');
        self::assertSame(1, OrderCreditNoteQuery::create()->filterByCreditNoteId($creditNote->getId())->count(), 'the credit note was consumed once, by the other order');
        self::assertNotNull($giftCard->getId());
    }

    #[Test]
    public function aSecondOrderPaidAtTheSameTimeWaitsOnTheCreditNoteTheFirstOneIsSettlingAndGivesUp(): void
    {
        $creditNote = $this->acceptedCreditNote();
        $cart = $this->cartOf($this->customer, $this->provider, '300.000000');
        $this->reservation()->reserve($cart, $creditNote->getId());

        // The first order, settled by the core: the credit note row stays locked until the
        // surrounding transaction (the one of this test) ends.
        $order = $this->pay($cart);

        // The second order, from another connection, as another request would be: the same
        // write the settlement of its part does.
        $contender = new ConnectionWrapper(new PdoConnection(
            \sprintf('mysql:host=%s;port=%s;dbname=%s', $_SERVER['DATABASE_HOST'], $_SERVER['DATABASE_PORT'] ?? '3306', $_SERVER['DATABASE_NAME']),
            $_SERVER['DATABASE_USER'],
            $_SERVER['DATABASE_PASSWORD'],
        ));
        $contender->prepare('SET SESSION innodb_lock_wait_timeout = 1')->execute();
        $contender->beginTransaction();

        try {
            $this->consumption()->consumeWithin($contender, $creditNote->getId(), $this->fixture->paidOrder($this->customer), 117.6, 999999);
            self::fail('the second order consumed the credit note the first one was holding');
        } catch (\Throwable $exception) {
            self::assertStringContainsString('lock', strtolower($exception->getMessage()), 'the second order waited on the row lock and gave up');
        } finally {
            $contender->rollBack();
        }

        self::assertSame(1, OrderCreditNoteQuery::create()->filterByCreditNoteId($creditNote->getId())->count(), 'the balance was consumed once');
        self::assertSame($order->getId(), OrderCreditNoteQuery::create()->filterByCreditNoteId($creditNote->getId())->findOne()->getOrderId());
    }

    protected function paymentFactory(): FixtureFactory
    {
        return $this->fixture->factory();
    }

    protected function paymentConnection(): ConnectionInterface
    {
        return $this->getPropelConnection();
    }

    protected function paymentContainer(): ContainerInterface
    {
        return static::getContainer();
    }

    /**
     * What the payment step does when it draws the credit notes: the cart is put right, and
     * the buyer is told what changed.
     *
     * @return array{gone: list<int>, tooBig: list<int>, reduced: list<int>}
     */
    private function thePaymentStepIsShown(Cart $cart): array
    {
        return $this->reservation()->putRight($cart);
    }

    private function acceptedCreditNote(int $quantity = 1, bool $allowPartialUse = true): CreditNoteModel
    {
        $creditNote = $this->fixture->creditNote($this->customer, $this->fixture->paidOrder($this->customer), 'accepted', $quantity);
        $creditNote->setAllowPartialUse($allowPartialUse);
        $creditNote->save();

        return $creditNote;
    }

    private function statusOf(CreditNoteModel $creditNote): string
    {
        $reread = CreditNoteQuery::create()->findPk($creditNote->getId());
        $reread->reload(true);

        return $reread->getCreditNoteStatus()->getCode();
    }

    private function module(): CreditNote
    {
        return $this->creditNoteModule->getPaymentModuleInstance(static::getContainer());
    }

    private function providerInstance(): CreditNoteTestBalanceProvider
    {
        return $this->provider->getPaymentModuleInstance(static::getContainer());
    }

    private function reservation(): CartCreditNoteReservation
    {
        return $this->getService(CartCreditNoteReservation::class);
    }

    private function consumption(): CreditNoteConsumption
    {
        return $this->getService(CreditNoteConsumption::class);
    }

    private function balance(): CreditNoteBalance
    {
        return $this->getService(CreditNoteBalance::class);
    }
}
