<?php

declare(strict_types=1);

namespace CreditNote\Tests\Integration;

use CreditNote\Model\CreditNote as CreditNoteModel;
use CreditNote\Model\CreditNoteQuery;
use CreditNote\Tests\Support\BackOfficeRouteTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Thelia\Core\Security\AccessManager;
use Thelia\Core\Security\Resource\AdminResources;

/**
 * A credit note is an amount granted to a customer: every route that reads or writes one
 * takes the right on the orders and on the module, both. Reading takes VIEW, creating and
 * updating UPDATE, deleting DELETE. The configuration of the module keeps the module resource.
 */
final class CreditNoteAccessRightsTest extends BackOfficeRouteTestCase
{
    private const ALL = [AccessManager::VIEW, AccessManager::CREATE, AccessManager::UPDATE, AccessManager::DELETE];

    #[Test]
    #[DataProvider('routes')]
    public function everyRouteIsRefusedWithoutTheRightOnTheOrders(string $method, string $uri): void
    {
        $creditNote = $this->creditNote('accepted');
        $this->loginRestrictedAdmin([AdminResources::MODULE => self::ALL], self::ALL);

        $this->client->request($method, \sprintf($uri, $creditNote->getId()), $this->creationOf($creditNote->getOrder()));

        self::assertSame(403, $this->client->getResponse()->getStatusCode());
    }

    #[Test]
    #[DataProvider('routes')]
    public function everyRouteIsRefusedWithoutTheRightOnTheModule(string $method, string $uri): void
    {
        $creditNote = $this->creditNote('accepted');
        $this->loginRestrictedAdmin([AdminResources::ORDER => self::ALL], []);

        $this->client->request($method, \sprintf($uri, $creditNote->getId()), $this->creationOf($creditNote->getOrder()));

        self::assertSame(403, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @return iterable<string, array{string, string}>
     */
    public static function routes(): iterable
    {
        yield 'list' => ['GET', '/admin/credit-note'];
        yield 'create' => ['POST', '/admin/credit-note/create'];
        yield 'modal of a credit note' => ['POST', '/admin/credit-note/%d'];
        yield 'update' => ['POST', '/admin/credit-note/%d/_update'];
        yield 'delete' => ['POST', '/admin/credit-note/%d/_delete'];
        yield 'modal of a new credit note' => ['POST', '/admin/credit-note/ajax/modal/create'];
        yield 'document' => ['GET', '/admin/credit-note/pdf/invoice/%d/2'];
        yield 'customer search' => ['GET', '/admin/credit-note/ajax/search/customer?q=diallo'];
        yield 'order search' => ['GET', '/admin/credit-note/ajax/search/order?q=diallo'];
        yield 'search form' => ['POST', '/admin/module/credit-note/search'];
    }

    #[Test]
    public function anAdministratorWhoOnlyViewsReadsTheListAndWritesNothing(): void
    {
        $creditNote = $this->creditNote('proposed');
        $order = $creditNote->getOrder();
        $count = CreditNoteQuery::create()->count();
        $this->loginRestrictedAdmin([AdminResources::ORDER => [AccessManager::VIEW]], [AccessManager::VIEW]);

        $this->client->request('GET', '/admin/credit-note');
        self::assertSame(200, $this->client->getResponse()->getStatusCode());

        $this->client->request('POST', '/admin/credit-note/create', $this->creationOf($order));
        self::assertSame(403, $this->client->getResponse()->getStatusCode());

        $this->client->request('POST', \sprintf('/admin/credit-note/%d/_update', $creditNote->getId()), $this->creationOf($order, ['action' => 'update']));
        self::assertSame(403, $this->client->getResponse()->getStatusCode());

        $this->client->request('POST', \sprintf('/admin/credit-note/%d/_delete', $creditNote->getId()));
        self::assertSame(403, $this->client->getResponse()->getStatusCode());

        self::assertSame($count, CreditNoteQuery::create()->count(), 'nothing was written nor deleted');
    }

    #[Test]
    public function anAdministratorWhoUpdatesCreatesButDoesNotDelete(): void
    {
        $creditNote = $this->creditNote('proposed');
        $order = $creditNote->getOrder();
        $count = CreditNoteQuery::create()->count();
        $rights = [AccessManager::VIEW, AccessManager::UPDATE];
        $this->loginRestrictedAdmin([AdminResources::ORDER => $rights], $rights);

        $this->client->request('POST', '/admin/credit-note/create', $this->creationOf($order));
        self::assertTrue($this->client->getResponse()->isRedirection(), (string) $this->client->getResponse()->getContent());
        self::assertSame($count + 1, CreditNoteQuery::create()->count(), 'the credit note is written');

        $this->client->request('POST', \sprintf('/admin/credit-note/%d/_delete', $creditNote->getId()));
        self::assertSame(403, $this->client->getResponse()->getStatusCode());
        self::assertNotNull(CreditNoteQuery::create()->findPk($creditNote->getId()), 'the credit note is still there');
    }

    #[Test]
    public function theConfigurationOfTheModuleKeepsTheModuleResource(): void
    {
        $this->loginRestrictedAdmin([AdminResources::ORDER => self::ALL], self::ALL);
        $this->client->request('POST', '/admin/module/credit-note/config');
        self::assertSame(403, $this->client->getResponse()->getStatusCode(), 'the right on the orders does not open the configuration');

        $this->loginRestrictedAdmin([AdminResources::MODULE => self::ALL], self::ALL);
        $this->client->request('POST', '/admin/module/credit-note/config');
        self::assertTrue($this->client->getResponse()->isRedirection(), 'the right on the modules does');
    }

    private function creditNote(string $statusCode): CreditNoteModel
    {
        $customer = $this->fixture->customer();
        $creditNote = $this->fixture->creditNote($customer, $this->fixture->paidOrder($customer), $statusCode);
        $creditNote->save();

        return $creditNote;
    }
}
