<?php

declare(strict_types=1);

namespace CreditNote\Tests\Integration;

use CreditNote\CreditNote;
use CreditNote\Model\CreditNote as CreditNoteModel;
use CreditNote\Tests\Support\AdminSessionInjector;
use CreditNote\Tests\Support\CreditNoteFixture;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Thelia\Model\ModuleQuery;
use Thelia\Test\FixtureFactory;
use Thelia\Test\WebIntegrationTestCase;

/**
 * After a save, the administrator goes back to the page the modal was opened from, on the tab
 * that held it (`success-url`, sent by the modal script). Only a path of the shop is followed:
 * any other address sends back to the credit notes of the order.
 */
final class CreditNoteSaveRedirectTest extends WebIntegrationTestCase
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
    public function theAdministratorGoesBackToThePageAndTabTheModalWasOpenedFrom(): void
    {
        $creditNote = $this->creditNote();
        $this->loginAdmin();

        $this->update($creditNote, '/admin/order/update/42?foo=bar#credit-note');

        self::assertSame('/admin/order/update/42?foo=bar#credit-note', $this->client->getResponse()->headers->get('Location'));
    }

    #[Test]
    #[DataProvider('foreignAddresses')]
    public function anAddressOutsideTheShopSendsBackToTheCreditNotesOfTheOrder(?string $successUrl): void
    {
        $creditNote = $this->creditNote();
        $this->loginAdmin();

        $this->update($creditNote, $successUrl);

        self::assertSame(
            \sprintf('/admin/order/update/%d#credit-note', $creditNote->getOrderId()),
            $this->client->getResponse()->headers->get('Location'),
        );
    }

    /**
     * @return iterable<string, array{?string}>
     */
    public static function foreignAddresses(): iterable
    {
        yield 'none' => [null];
        yield 'absolute' => ['https://example.com/admin'];
        yield 'protocol-relative' => ['//example.com/admin'];
        yield 'backslash' => ['/\\example.com'];
        yield 'tab' => ["/\t/example.com"];
        yield 'line break' => ["/\n/example.com"];
        yield 'space' => ['/ /example.com'];
        yield 'trailing line break' => ["/admin/order/update/42\n"];
    }

    private function creditNote(): CreditNoteModel
    {
        $customer = $this->fixture->customer();
        $creditNote = $this->fixture->creditNote($customer, $this->fixture->paidOrder($customer), 'accepted');
        $creditNote->save();

        return $creditNote;
    }

    private function update(CreditNoteModel $creditNote, ?string $successUrl): void
    {
        $parameters = [
            'credit_note_create' => [
                'action' => 'update',
                'ui_target' => 'order',
                'ui_target_id' => $creditNote->getOrderId(),
            ],
        ];

        if (null !== $successUrl) {
            $parameters['success-url'] = $successUrl;
        }

        $this->client->request('POST', \sprintf('/admin/credit-note/%d/_update', $creditNote->getId()), $parameters);

        self::assertTrue($this->client->getResponse()->isRedirection(), (string) $this->client->getResponse()->getContent());
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
