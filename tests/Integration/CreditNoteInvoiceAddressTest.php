<?php

declare(strict_types=1);

namespace CreditNote\Tests\Integration;

use CreditNote\CreditNote;
use CreditNote\Model\CreditNoteAddressQuery;
use CreditNote\Model\CreditNoteQuery;
use CreditNote\Tests\Support\AdminSessionInjector;
use CreditNote\Tests\Support\CreditNoteFixture;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Thelia\Model\ModuleQuery;
use Thelia\Test\FixtureFactory;
use Thelia\Test\WebIntegrationTestCase;

/**
 * A credit note needs an invoice address: one created without (no customer picked, or "Other"
 * with empty fields) is refused with a message, and nothing is written, not even an empty
 * address.
 */
final class CreditNoteInvoiceAddressTest extends WebIntegrationTestCase
{
    private CreditNoteFixture $fixture;

    private AdminSessionInjector $injector;

    protected function setUp(): void
    {
        parent::setUp();

        if (!ModuleQuery::create()->findOneByCode(CreditNote::getModuleCode())?->getActivate()) {
            self::markTestSkipped('The CreditNote module is not active in the test database.');
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
    public function aCreditNoteWithoutInvoiceAddressIsRefusedAndNothingIsWritten(): void
    {
        $customer = $this->fixture->customer();
        $creditNotes = CreditNoteQuery::create()->count();
        $addresses = CreditNoteAddressQuery::create()->count();
        $this->loginAdmin();

        // The flash bag of the request, read before the session is saved.
        $errors = [];
        $readErrors = static function (ResponseEvent $event) use (&$errors): void {
            if ($event->getRequest()->hasSession()) {
                $errors = $event->getRequest()->getSession()->getFlashBag()->peek('danger');
            }
        };
        $dispatcher = $this->getService(EventDispatcherInterface::class);
        $dispatcher->addListener(KernelEvents::RESPONSE, $readErrors);

        $this->client->request('POST', '/admin/credit-note/create', [
            'success-url' => \sprintf('/admin/customer/update?customer_id=%d', $customer->getId()),
            'credit_note_create' => [
                'action' => 'create',
                'customer_id' => $customer->getId(),
                'ui_target' => 'customer',
                'ui_target_id' => $customer->getId(),
            ],
        ]);
        $response = $this->client->getResponse();
        $dispatcher->removeListener(KernelEvents::RESPONSE, $readErrors);

        self::assertTrue($response->isRedirection(), (string) $response->getContent());
        self::assertSame(\sprintf('/admin/customer/update?customer_id=%d', $customer->getId()), $response->headers->get('Location'));
        self::assertSame($creditNotes, CreditNoteQuery::create()->count(), 'no credit note written');
        self::assertSame($addresses, CreditNoteAddressQuery::create()->count(), 'no empty address written');
        self::assertNotEmpty($errors, 'the administrator is told why');
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
