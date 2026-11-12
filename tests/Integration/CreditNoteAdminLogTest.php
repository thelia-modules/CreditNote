<?php

declare(strict_types=1);

namespace CreditNote\Tests\Integration;

use CreditNote\Model\CreditNoteQuery;
use CreditNote\Model\CreditNoteStatusQuery;
use CreditNote\Tests\Support\BackOfficeRouteTestCase;
use PHPUnit\Framework\Attributes\Test;
use Thelia\Core\Security\AccessManager;
use Thelia\Core\Security\Resource\AdminResources;
use Thelia\Model\AdminLog;
use Thelia\Model\AdminLogQuery;
use Thelia\Model\Order;

/**
 * Whoever writes a credit note from the back-office leaves a line in the administration log,
 * on the order the credit note refunds. A credit note the order ceiling refuses leaves none.
 */
final class CreditNoteAdminLogTest extends BackOfficeRouteTestCase
{
    #[Test]
    public function theCreationOfACreditNoteIsLoggedOnItsOrder(): void
    {
        $customer = $this->fixture->customer();
        $order = $this->fixture->paidOrder($customer);
        $admin = $this->loginSuperAdmin();

        $this->client->request('POST', '/admin/credit-note/create', $this->creationOf($order));
        self::assertTrue($this->client->getResponse()->isRedirection(), (string) $this->client->getResponse()->getContent());

        $creditNote = CreditNoteQuery::create()->filterByOrderId($order->getId())->findOne();
        self::assertNotNull($creditNote, 'the credit note is written');

        $log = $this->lastLog($order, AccessManager::CREATE);
        self::assertNotNull($log, 'the creation is logged');
        self::assertSame($admin->getLogin(), $log->getAdminLogin());
        self::assertStringContainsString($creditNote->getRef(), $log->getMessage());
        self::assertStringContainsString($order->getRef(), $log->getMessage());
        self::assertStringContainsString($customer->getRef(), $log->getMessage());
        self::assertStringContainsString('117.60', $log->getMessage());
        self::assertStringContainsString('proposed', $log->getMessage());
    }

    #[Test]
    public function theUpdateAndTheDeletionOfACreditNoteAreLogged(): void
    {
        $customer = $this->fixture->customer();
        $order = $this->fixture->paidOrder($customer);
        $creditNote = $this->fixture->creditNote($customer, $order);
        $creditNote->save();
        $ref = $creditNote->getRef();
        $this->loginSuperAdmin();

        $this->client->request('POST', \sprintf('/admin/credit-note/%d/_update', $creditNote->getId()), $this->creationOf($order, [
            'action' => 'update',
            'status_id' => CreditNoteStatusQuery::create()->findOneByCode('refused')->getId(),
        ]));
        self::assertTrue($this->client->getResponse()->isRedirection(), (string) $this->client->getResponse()->getContent());

        $update = $this->lastLog($order, AccessManager::UPDATE);
        self::assertNotNull($update, 'the update is logged');
        self::assertStringContainsString($ref, $update->getMessage());
        self::assertStringContainsString('refused', $update->getMessage());

        $this->client->request('POST', \sprintf('/admin/credit-note/%d/_delete', $creditNote->getId()), ['_token' => $this->csrfToken()]);
        self::assertNull(CreditNoteQuery::create()->findPk($creditNote->getId()), 'the credit note is deleted');

        $deletion = $this->lastLog($order, AccessManager::DELETE);
        self::assertNotNull($deletion, 'the deletion is logged');
        self::assertStringContainsString($ref, $deletion->getMessage());
    }

    #[Test]
    public function aCreditNoteTheCeilingRefusesIsNotLogged(): void
    {
        $customer = $this->fixture->customer();
        $order = $this->fixture->paidOrder($customer);
        $this->fixture->creditNote($customer, $order, 'accepted', 2)->save();
        $this->loginSuperAdmin();

        $errors = $this->requestAndReadErrors('POST', '/admin/credit-note/create', $this->creationOf($order));

        self::assertNotEmpty($errors, 'the administrator is told why');
        self::assertSame(1, CreditNoteQuery::create()->filterByOrderId($order->getId())->count(), 'nothing was written');
        self::assertNull($this->lastLog($order, AccessManager::CREATE));
    }

    #[Test]
    public function aNumberedCreditNoteIsNeitherDeletedNorLoggedAsDeleted(): void
    {
        $customer = $this->fixture->customer();
        $order = $this->fixture->paidOrder($customer);
        $creditNote = $this->fixture->creditNote($customer, $order, 'accepted');
        $creditNote->save();
        $this->loginSuperAdmin();

        $this->client->request('POST', \sprintf('/admin/credit-note/%d/_delete', $creditNote->getId()), ['_token' => $this->csrfToken()]);

        self::assertNotNull(CreditNoteQuery::create()->findPk($creditNote->getId()));
        self::assertNull($this->lastLog($order, AccessManager::DELETE));
    }

    private function lastLog(Order $order, string $action): ?AdminLog
    {
        return AdminLogQuery::create()
            ->filterByResource(AdminResources::ORDER)
            ->filterByAction($action)
            ->filterByResourceId($order->getId())
            ->orderById('desc')
            ->findOne();
    }
}
