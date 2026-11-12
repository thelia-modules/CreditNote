<?php

declare(strict_types=1);

namespace CreditNote\Tests\Support;

use CreditNote\CreditNote;
use CreditNote\Model\CreditNoteStatusQuery;
use CreditNote\Model\CreditNoteTypeQuery;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Thelia\Core\Security\AccessManager;
use Thelia\Model\Admin;
use Thelia\Model\Lang;
use Thelia\Model\ModuleQuery;
use Thelia\Model\Order;
use Thelia\Model\ProfileModule;
use Thelia\Test\FixtureFactory;
use Thelia\Test\WebIntegrationTestCase;

/**
 * The back-office routes of the module, called the way the modal calls them, by an
 * administrator put in the session: a super administrator, or one whose profile grants
 * exactly the accesses given on the resources and on the module.
 */
abstract class BackOfficeRouteTestCase extends WebIntegrationTestCase
{
    protected CreditNoteFixture $fixture;

    protected AdminSessionInjector $injector;

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

    /**
     * Built directly rather than through createFixtureFactory(): that helper pushes a
     * synthetic request on the stack, which would become the main request the security
     * context resolves the session from for every request the client sends afterwards.
     */
    protected function loginSuperAdmin(?Lang $lang = null): Admin
    {
        $admin = (new FixtureFactory($this->getPropelConnection()))->admin(['locale' => $lang?->getLocale() ?? 'en_US']);
        $admin->eraseCredentials();
        $this->injector->setAdmin($admin, $lang);

        return $admin;
    }

    /**
     * @param array<string, list<string>> $resources accesses by resource code
     * @param list<string>                $module    accesses on the CreditNote module, none when empty
     */
    protected function loginRestrictedAdmin(array $resources, array $module): Admin
    {
        $factory = new FixtureFactory($this->getPropelConnection());
        $admin = $factory->restrictedAdmin($resources);

        if ([] !== $module) {
            $accessManager = new AccessManager(0);
            $accessManager->build($module);

            (new ProfileModule())
                ->setProfileId($admin->getProfileId())
                ->setModuleId(CreditNote::getModuleId())
                ->setAccess($accessManager->getAccessValue())
                ->save($this->getPropelConnection());
        }

        $admin->eraseCredentials();
        $this->injector->setAdmin($admin);

        return $admin;
    }

    /**
     * What the modal posts to grant a credit note on one unit of the first line of the order
     * (117.60 with tax), with the invoice address typed in.
     *
     * @param array<string, mixed> $overrides
     *
     * @return array<string, mixed>
     */
    protected function creationOf(Order $order, array $overrides = []): array
    {
        return ['credit_note_create' => $overrides + [
            'action' => 'create',
            'order_id' => $order->getId(),
            'type_id' => CreditNoteTypeQuery::create()->findOneByCode('back_product')->getId(),
            'status_id' => CreditNoteStatusQuery::create()->findOneByCode('proposed')->getId(),
            'order_product_quantity' => [$order->getOrderProducts()->getFirst()->getId() => 1],
            'invoice_address_firstname' => 'Mohamed',
            'invoice_address_lastname' => 'Diallo',
            'invoice_address_address1' => '3 rue de Paris',
            'invoice_address_zipcode' => '93100',
            'invoice_address_city' => 'Montreuil',
            'invoice_address_country_id' => $order->getOrderAddressRelatedByInvoiceOrderAddressId()->getCountryId(),
            'ui_target' => 'order',
            'ui_target_id' => $order->getId(),
        ]];
    }

    /**
     * Sends the request and returns the error messages left for the next page: the flash bag
     * of the request, read before the session is saved.
     *
     * @param array<string, mixed> $parameters
     *
     * @return list<string>
     */
    protected function requestAndReadErrors(string $method, string $uri, array $parameters = []): array
    {
        $errors = [];
        $readErrors = static function (ResponseEvent $event) use (&$errors): void {
            if ($event->getRequest()->hasSession()) {
                $errors = $event->getRequest()->getSession()->getFlashBag()->peek('danger');
            }
        };
        $dispatcher = $this->getService(EventDispatcherInterface::class);
        $dispatcher->addListener(KernelEvents::RESPONSE, $readErrors);

        try {
            $this->client->request($method, $uri, $parameters);
        } finally {
            $dispatcher->removeListener(KernelEvents::RESPONSE, $readErrors);
        }

        return array_values($errors);
    }
}
