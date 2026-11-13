<?php

declare(strict_types=1);

namespace CreditNote\Tests\Integration;

use CreditNote\CreditNote;
use CreditNote\Hook\Front\CheckoutPaymentPartsHook;
use CreditNote\LiveComponent\CheckoutCreditNotes;
use CreditNote\Model\CartCreditNoteQuery;
use CreditNote\Model\CreditNote as CreditNoteModel;
use CreditNote\Service\CreditNoteConsumption;
use CreditNote\Tests\Support\CreditNoteFixture;
use CreditNote\Tests\Support\Payment\CreditNoteTestBalanceProvider;
use CreditNote\Tests\Support\Payment\PaysWithCreditNotes;
use PHPUnit\Framework\Attributes\Test;
use Propel\Runtime\Connection\ConnectionInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Thelia\Core\HttpFoundation\Session\Session;
use Thelia\Model\Cart;
use Thelia\Model\Customer;
use Thelia\Model\Module;
use Thelia\Model\ModuleQuery;
use Thelia\Test\FixtureFactory;
use Thelia\Test\IntegrationTestCase;

/**
 * The credit notes on the payment step of the checkout: the line the theme draws for the module
 * lists the credit notes of the signed-in buyer, and ticking one puts it on the cart of the
 * session. Nothing is shown to a visitor, in the line of another means, or about the credit
 * notes of someone else.
 */
final class CheckoutCreditNotesTest extends IntegrationTestCase
{
    use PaysWithCreditNotes;

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
        $this->customer = $this->fixture->customer();
    }

    protected function tearDown(): void
    {
        $this->session()?->clearCustomerUser();
        $this->uninstallThePaymentModules();

        parent::tearDown();
    }

    #[Test]
    public function theLineOfTheModuleListsTheCreditNotesTheBuyerMayPayWith(): void
    {
        $usable = $this->creditNote($this->customer, 'accepted', 2);
        $wholeOnly = $this->creditNote($this->customer, 'accepted', 1, false);
        $draft = $this->creditNote($this->customer, 'proposed');
        $theirs = $this->creditNote($this->fixture->customer(), 'accepted');
        $this->signInWithCart('300.000000');

        $html = $this->hook()->render('checkout.payment.parts', ['module' => $this->creditNoteModule->getId(), 'amount' => '0']);

        self::assertStringContainsString('Credit note '.$usable->getRef(), $html);
        self::assertStringContainsString('€235.20 left', html_entity_decode($html), 'with what is left of it, written as the theme writes its amounts');
        self::assertStringContainsString('Credit note '.$wholeOnly->getRef(), $html);
        self::assertStringContainsString('Usable in a single purchase', $html);
        self::assertStringContainsString('data-live-action-param="toggle"', $html);
        self::assertStringNotContainsString((string) $draft->getRef(), $html, 'a proposed credit note pays nothing');
        self::assertStringNotContainsString((string) $theirs->getRef(), $html, 'the credit notes of another customer are never shown');
    }

    #[Test]
    public function nothingIsDrawnInTheLineOfAnotherMeansNorForAVisitor(): void
    {
        $this->creditNote($this->customer, 'accepted');

        self::assertTrue($this->hook()->supports('checkout.payment.parts'));
        self::assertFalse($this->hook()->supports('checkout.payment.extra'));
        self::assertSame('', $this->hook()->render('checkout.payment.parts', ['module' => $this->creditNoteModule->getId(), 'amount' => '0']), 'a visitor has no credit note');

        $this->signInWithCart();

        self::assertSame('', $this->hook()->render('checkout.payment.parts', ['module' => $this->provider->getId(), 'amount' => '0']), 'the line of another means stays as it is');
    }

    #[Test]
    public function tickingACreditNotePutsItOnTheCartAndUntickingTakesItOff(): void
    {
        $creditNote = $this->creditNote($this->customer, 'accepted');
        $cart = $this->signInWithCart('300.000000');
        $component = $this->component();

        $component->toggle($creditNote->getId());

        self::assertNull($component->error);
        self::assertSame('117.600000', CartCreditNoteQuery::create()->filterByCartId($cart->getId())->findOne()?->getAmountPrice());
        self::assertTrue($component->getChoices()[0]['reserved']);

        $component->toggle($creditNote->getId());

        self::assertSame(0, CartCreditNoteQuery::create()->filterByCartId($cart->getId())->count());
    }

    #[Test]
    public function aCreditNoteTheBuyerMayNotUseIsRefusedWithAMessageAndNothingIsWritten(): void
    {
        $theirs = $this->creditNote($this->fixture->customer(), 'accepted');
        $wholeOnly = $this->creditNote($this->customer, 'accepted', 1, false);
        $cart = $this->signInWithCart('50.000000');
        $component = $this->component();

        $component->toggle($theirs->getId());
        self::assertSame('This credit note cannot be used.', $component->error);

        $component->toggle($wholeOnly->getId());
        self::assertStringContainsString('your cart is worth less than it', (string) $component->error);

        self::assertSame(0, CartCreditNoteQuery::create()->filterByCartId($cart->getId())->count());
    }

    #[Test]
    public function theBuyerIsToldWhenACreditNoteOfTheCartWasSpentElsewhere(): void
    {
        $spent = $this->creditNote($this->customer, 'accepted');
        $kept = $this->creditNote($this->customer, 'accepted');
        $cart = $this->signInWithCart('300.000000');
        $component = $this->component();
        $component->toggle($spent->getId());
        $component->toggle($kept->getId());

        $this->getService(CreditNoteConsumption::class)->consume($spent->getId(), $this->fixture->paidOrder($this->customer), 117.6);

        $choices = $component->getChoices();

        self::assertSame('A credit note was taken off your cart: it is no longer available.', $component->error);
        self::assertSame([$kept->getId()], array_column($choices, 'id'), 'the spent credit note is no longer offered');
        self::assertSame([$kept->getId()], CartCreditNoteQuery::create()->filterByCartId($cart->getId())->select(['CreditNoteId'])->find()->getData());
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

    private function creditNote(Customer $customer, string $status, int $quantity = 1, bool $allowPartialUse = true): CreditNoteModel
    {
        $creditNote = $this->fixture->creditNote($customer, $this->fixture->paidOrder($customer), $status, $quantity);
        $creditNote->setAllowPartialUse($allowPartialUse);
        $creditNote->save();

        return $creditNote;
    }

    private function signInWithCart(string $price = '100.000000'): Cart
    {
        $cart = $this->cartOf($this->customer, $this->provider, $price);
        $this->session()->setCustomerUser($this->customer);
        $this->session()->setSessionCart($cart);

        return $cart;
    }

    private function hook(): CheckoutPaymentPartsHook
    {
        return $this->getService(CheckoutPaymentPartsHook::class);
    }

    private function component(): CheckoutCreditNotes
    {
        return $this->getService(CheckoutCreditNotes::class);
    }

    private function session(): ?Session
    {
        $request = $this->getService(RequestStack::class)->getMainRequest();
        $session = $request?->hasSession() ? $request->getSession() : null;

        return $session instanceof Session ? $session : null;
    }
}
