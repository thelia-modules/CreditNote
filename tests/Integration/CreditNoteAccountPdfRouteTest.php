<?php

declare(strict_types=1);

namespace CreditNote\Tests\Integration;

use CreditNote\CreditNote;
use CreditNote\Tests\Support\CreditNoteFixture;
use CreditNote\Tests\Support\CustomerSessionInjector;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Thelia\Core\Template\TemplateDefinition;
use Thelia\Model\ConfigQuery;
use Thelia\Model\ModuleQuery;
use Thelia\Test\WebIntegrationTestCase;

/**
 * The route that serves a credit note to its customer from the account: the signed-in owner
 * gets the PDF, anyone else gets a 404 (another customer, a draft, an unknown id), a visitor
 * is sent to the login page.
 *
 * Prerequisites: the module active, and a PDF theme that ships the credit note document.
 */
final class CreditNoteAccountPdfRouteTest extends WebIntegrationTestCase
{
    private CreditNoteFixture $fixture;

    private CustomerSessionInjector $injector;

    protected function setUp(): void
    {
        parent::setUp();

        if (!ModuleQuery::create()->findOneByCode(CreditNote::getModuleCode())?->getActivate()) {
            self::markTestSkipped('The CreditNote module is not active in the test database.');
        }

        $pdfTemplate = new TemplateDefinition(ConfigQuery::read(TemplateDefinition::PDF_CONFIG_NAME, 'default'), TemplateDefinition::PDF);

        if (!is_file($pdfTemplate->getAbsolutePath() . '/credit-note.html.twig')) {
            self::markTestSkipped(\sprintf('The active PDF template (%s) ships no credit note document.', $pdfTemplate->getName()));
        }

        $this->fixture = new CreditNoteFixture($this->getPropelConnection());
        $this->injector = new CustomerSessionInjector();
        $this->getService(EventDispatcherInterface::class)->addSubscriber($this->injector);
    }

    protected function tearDown(): void
    {
        if (isset($this->injector)) {
            $this->injector->clear();
        }

        parent::tearDown();
    }

    #[Test]
    public function theOwnerDownloadsTheAcceptedCreditNote(): void
    {
        $customer = $this->fixture->customer();
        $creditNote = $this->fixture->creditNote($customer, $this->fixture->paidOrder($customer), 'accepted', 2);
        $creditNote->save();
        $this->injector->setCustomer($customer);

        $this->client->request('GET', \sprintf('/account/credit-note/%d/pdf', $creditNote->getId()));
        $response = $this->client->getResponse();

        self::assertSame(200, $response->getStatusCode());
        self::assertSame('application/pdf', $response->headers->get('Content-Type'));
        self::assertSame(\sprintf('inline; filename=%s.pdf', $creditNote->getInvoiceRef()), $response->headers->get('Content-Disposition'));
        self::assertStringStartsWith('%PDF-', (string) $response->getContent());
    }

    #[Test]
    public function theCreditNoteOfAnotherCustomerDoesNotExistForTheSignedInOne(): void
    {
        $owner = $this->fixture->customer();
        $creditNote = $this->fixture->creditNote($owner, $this->fixture->paidOrder($owner), 'accepted');
        $creditNote->save();
        $this->injector->setCustomer($this->fixture->customer());

        $this->client->request('GET', \sprintf('/account/credit-note/%d/pdf', $creditNote->getId()));

        self::assertSame(404, $this->client->getResponse()->getStatusCode());
    }

    #[Test]
    public function aDraftAndAnUnknownCreditNoteAreNotServed(): void
    {
        $customer = $this->fixture->customer();
        $draft = $this->fixture->creditNote($customer, $this->fixture->paidOrder($customer));
        $draft->save();
        $this->injector->setCustomer($customer);

        $this->client->request('GET', \sprintf('/account/credit-note/%d/pdf', $draft->getId()));
        self::assertSame(404, $this->client->getResponse()->getStatusCode());

        $this->client->request('GET', '/account/credit-note/999999/pdf');
        self::assertSame(404, $this->client->getResponse()->getStatusCode());
    }

    #[Test]
    public function aGuestCheckingOutIsNotACustomerWhoSignedIn(): void
    {
        $owner = $this->fixture->customer();
        $creditNote = $this->fixture->creditNote($owner, $this->fixture->paidOrder($owner), 'accepted');
        $creditNote->save();
        $this->injector->setCustomer($this->fixture->factory()->guestCustomer($this->fixture->factory()->customerTitle()));

        $this->client->request('GET', \sprintf('/account/credit-note/%d/pdf', $creditNote->getId()));
        $response = $this->client->getResponse();

        self::assertTrue($response->isRedirection(), 'a guest is sent to sign in');
        self::assertStringNotContainsString('%PDF-', (string) $response->getContent());
    }

    #[Test]
    public function aVisitorIsSentToTheLoginPage(): void
    {
        $customer = $this->fixture->customer();
        $creditNote = $this->fixture->creditNote($customer, $this->fixture->paidOrder($customer), 'accepted');
        $creditNote->save();

        $this->client->request('GET', \sprintf('/account/credit-note/%d/pdf', $creditNote->getId()));
        $response = $this->client->getResponse();

        self::assertTrue($response->isRedirection(), 'a visitor is redirected');
        self::assertStringContainsString('login', (string) $response->headers->get('Location'));
        self::assertStringNotContainsString('%PDF-', (string) $response->getContent());
    }
}
