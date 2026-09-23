<?php

declare(strict_types=1);

namespace CreditNote\Tests\Integration;

use CreditNote\CreditNote;
use CreditNote\Tests\Support\AdminSessionInjector;
use CreditNote\Tests\Support\CreditNoteFixture;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Thelia\Core\Template\TemplateDefinition;
use Thelia\Model\ConfigQuery;
use Thelia\Model\ModuleQuery;
use Thelia\Test\FixtureFactory;
use Thelia\Test\WebIntegrationTestCase;

/**
 * The route that serves the printable credit note: an administrator gets the document of an
 * accepted credit note, as HTML or as PDF; a credit note that is not accepted yet is not
 * served; a visitor without an administrator session gets nothing.
 *
 * Prerequisites: the module active, and a PDF theme that ships the credit note document
 * (thelia/pdf-default-template 1.2 or later).
 */
final class CreditNotePdfRouteTest extends WebIntegrationTestCase
{
    private CreditNoteFixture $fixture;

    private AdminSessionInjector $injector;

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
        $this->injector = new AdminSessionInjector();
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
    public function anAdministratorGetsTheDocumentOfAnAcceptedCreditNote(): void
    {
        $customer = $this->fixture->customer();
        $order = $this->fixture->paidOrder($customer);
        $creditNote = $this->fixture->creditNote($customer, $order, 'accepted', 2);
        $creditNote->save();
        $this->loginAdmin();

        $this->client->request('GET', \sprintf('/admin/credit-note/pdf/invoice/%d/2', $creditNote->getId()));
        $html = (string) $this->client->getResponse()->getContent();

        self::assertSame(200, $this->client->getResponse()->getStatusCode());
        self::assertStringContainsString($creditNote->getRef(), $html);
        self::assertStringContainsString($creditNote->getInvoiceRef(), $html);
        self::assertStringContainsString($order->getRef(), $html);
        self::assertStringContainsString('Violet', $html);
        self::assertStringContainsString('Diallo', $html);

        $this->client->request('GET', \sprintf('/admin/credit-note/pdf/invoice/%d/1', $creditNote->getId()));
        $response = $this->client->getResponse();

        self::assertSame(200, $response->getStatusCode());
        self::assertSame('application/pdf', $response->headers->get('Content-Type'));
        self::assertSame(\sprintf('inline; filename=%s.pdf', $creditNote->getInvoiceRef()), $response->headers->get('Content-Disposition'));
        self::assertStringStartsWith('%PDF-', (string) $response->getContent());
    }

    #[Test]
    public function aCreditNoteNotAcceptedYetIsNotServed(): void
    {
        $customer = $this->fixture->customer();
        $creditNote = $this->fixture->creditNote($customer, $this->fixture->paidOrder($customer));
        $creditNote->save();
        $this->loginAdmin();

        $this->client->request('GET', \sprintf('/admin/credit-note/pdf/invoice/%d/2', $creditNote->getId()));

        self::assertSame(404, $this->client->getResponse()->getStatusCode());
    }

    #[Test]
    public function aVisitorWithoutAdministratorSessionGetsNoDocument(): void
    {
        $customer = $this->fixture->customer();
        $creditNote = $this->fixture->creditNote($customer, $this->fixture->paidOrder($customer), 'accepted');
        $creditNote->save();

        $this->client->request('GET', \sprintf('/admin/credit-note/pdf/invoice/%d/2', $creditNote->getId()));
        $response = $this->client->getResponse();

        self::assertNotSame(200, $response->getStatusCode());
        self::assertStringNotContainsString($creditNote->getRef(), (string) $response->getContent());
    }

    private function loginAdmin(): void
    {
        // Built directly rather than through createFixtureFactory(): that helper pushes a
        // synthetic request on the stack, which would become the main request the security
        // context resolves the session from for every request the client sends afterwards.
        $admin = (new FixtureFactory($this->getPropelConnection()))->admin();
        $admin->eraseCredentials();
        $this->injector->setAdmin($admin);
    }
}
