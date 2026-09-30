<?php

declare(strict_types=1);

namespace CreditNote\Tests\Integration;

use CreditNote\CreditNote;
use CreditNote\Hook\Front\AccountThemeHook;
use CreditNote\Model\OrderCreditNote;
use CreditNote\Tests\Support\CreditNoteFixture;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\HttpFoundation\RequestStack;
use Thelia\Model\Customer;
use Thelia\Model\ModuleQuery;
use Thelia\Test\IntegrationTestCase;

/**
 * What the customer account shows about credit notes, through the theme hook points of the
 * account and order pages: the accepted credit notes of the signed-in customer with their
 * balance, nothing for a draft, nothing for another customer, nothing for a visitor.
 */
final class AccountThemeHookTest extends IntegrationTestCase
{
    private CreditNoteFixture $fixture;

    protected function setUp(): void
    {
        parent::setUp();

        if (!ModuleQuery::create()->findOneByCode(CreditNote::getModuleCode())?->getActivate()) {
            self::markTestSkipped('The CreditNote module is not active in the test database.');
        }

        $this->fixture = new CreditNoteFixture($this->getPropelConnection());
    }

    protected function tearDown(): void
    {
        $this->session()?->clearCustomerUser();

        parent::tearDown();
    }

    #[Test]
    public function theHookAnswersTheAccountAndOrderPagesOnly(): void
    {
        $hook = $this->hook();

        self::assertTrue($hook->supports('account.bottom'));
        self::assertTrue($hook->supports('account-order.bottom'));
        self::assertFalse($hook->supports('account.top'));
        self::assertFalse($hook->supports('product.bottom'));
    }

    #[Test]
    public function theAccountPageListsTheAcceptedCreditNotesOfTheCustomerWithTheirBalance(): void
    {
        $customer = $this->fixture->customer();
        $order = $this->fixture->paidOrder($customer);
        $accepted = $this->fixture->creditNote($customer, $order, 'accepted', 2);
        $accepted->save();
        $draft = $this->fixture->creditNote($customer, $this->fixture->paidOrder($customer));
        $draft->save();
        $someoneElse = $this->fixture->creditNote($this->fixture->customer(), null, 'accepted', 1, 'rebate');
        $someoneElse->save();

        (new OrderCreditNote())->setOrderId($this->fixture->paidOrder($customer)->getId())->setCreditNoteId($accepted->getId())->setAmountPrice('35.200000')->save($this->getPropelConnection());

        $this->signIn($customer);
        $html = $this->hook()->render('account.bottom', ['customer' => ['id' => $customer->getId()]]);
        $text = $this->text($html);

        self::assertStringContainsString('My credit notes', $text);
        self::assertStringContainsString('Credit note ' . $accepted->getRef(), $text);
        self::assertStringContainsString('Order ' . $order->getRef(), $text);
        self::assertStringContainsString('Amount : 235', $text);
        self::assertStringContainsString('Remaining balance : 200', $text, 'the 35.20 used on another order are deducted');
        self::assertStringContainsString('Accepted', $text);
        self::assertStringContainsString('Usable in several purchases', $text);
        self::assertStringContainsString(\sprintf('/account/credit-note/%d/pdf', $accepted->getId()), $html, 'the document is downloaded through the account route');
        self::assertMatchesRegularExpression(\sprintf('#<a[^>]*href="[^"]*/account/credit-note/%d/pdf"[^>]*target="_blank"#s', $accepted->getId()), $html, 'the document opens in its own tab');

        self::assertStringNotContainsString((string) $draft->getRef(), $text, 'a proposed credit note is not shown to the customer');
        self::assertStringNotContainsString((string) $someoneElse->getRef(), $text, 'the credit notes of another customer are never shown');
    }

    #[Test]
    public function theOrderPageListsTheCreditNotesOfThatOrderOnly(): void
    {
        $customer = $this->fixture->customer();
        $order = $this->fixture->paidOrder($customer);
        $otherOrder = $this->fixture->paidOrder($customer);
        $onOrder = $this->fixture->creditNote($customer, $order, 'accepted');
        $onOrder->save();
        $onOtherOrder = $this->fixture->creditNote($customer, $otherOrder, 'accepted');
        $onOtherOrder->save();

        $this->signIn($customer);
        $text = $this->text($this->hook()->render('account-order.bottom', ['order' => ['id' => $order->getId(), 'ref' => $order->getRef()]]));

        self::assertStringContainsString('Credit notes on this order', $text);
        self::assertStringContainsString((string) $onOrder->getRef(), $text);
        self::assertStringNotContainsString((string) $onOtherOrder->getRef(), $text);
    }

    #[Test]
    public function theOrderOfAnotherCustomerShowsNothingEvenWhenTheThemeNamesIt(): void
    {
        $customer = $this->fixture->customer();
        $otherCustomer = $this->fixture->customer();
        $otherOrder = $this->fixture->paidOrder($otherCustomer);
        $this->fixture->creditNote($otherCustomer, $otherOrder, 'accepted')->save();

        $this->signIn($customer);

        self::assertSame('', $this->hook()->render('account-order.bottom', ['order' => ['id' => $otherOrder->getId()]]));
    }

    #[Test]
    public function nothingIsRenderedForAVisitorOrACustomerWithoutCreditNote(): void
    {
        self::assertSame('', $this->hook()->render('account.bottom', []), 'a visitor sees nothing');

        $this->signIn($this->fixture->customer());

        self::assertSame('', $this->hook()->render('account.bottom', []), 'a customer without credit note gets no empty block');
    }

    private function hook(): AccountThemeHook
    {
        return $this->getService(AccountThemeHook::class);
    }

    private function signIn(Customer $customer): void
    {
        $this->session()->setCustomerUser($customer);
    }

    private function session(): ?\Thelia\Core\HttpFoundation\Session\Session
    {
        $request = $this->getService(RequestStack::class)->getMainRequest();
        $session = $request?->hasSession() ? $request->getSession() : null;

        return $session instanceof \Thelia\Core\HttpFoundation\Session\Session ? $session : null;
    }

    private function text(string $html): string
    {
        return trim((string) preg_replace('/\s+/u', ' ', html_entity_decode(strip_tags(str_replace('>', '> ', $html)), \ENT_QUOTES | \ENT_HTML5, 'UTF-8')));
    }
}
