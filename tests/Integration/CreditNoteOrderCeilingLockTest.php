<?php

declare(strict_types=1);

namespace CreditNote\Tests\Integration;

use CreditNote\CreditNote;
use CreditNote\Model\CreditNote as CreditNoteModel;
use CreditNote\Service\CreditNoteOrderCeiling;
use CreditNote\Tests\Support\CreditNoteFixture;
use PHPUnit\Framework\Attributes\Test;
use Propel\Runtime\Connection\ConnectionWrapper;
use Propel\Runtime\Connection\PdoConnection;
use Thelia\Model\CartQuery;
use Thelia\Model\CustomerQuery;
use Thelia\Model\ModuleQuery;
use Thelia\Model\Order;
use Thelia\Model\OrderAddressQuery;
use Thelia\Model\OrderQuery;
use Thelia\Test\IntegrationTestCase;

/**
 * The ceiling of an order against a second request, on its own connection.
 *
 * The order is committed before the request under test opens its transaction, and taken away
 * afterwards: an order written inside the transaction of a test would prove nothing, a second
 * connection waits on a row not yet committed, locked or not. Hence no transaction around the
 * test.
 */
final class CreditNoteOrderCeilingLockTest extends IntegrationTestCase
{
    protected bool $useTransaction = false;

    private ?ConnectionWrapper $otherRequest = null;

    private ?Order $order = null;

    protected function setUp(): void
    {
        parent::setUp();

        if (!ModuleQuery::create()->findOneByCode(CreditNote::getModuleCode())?->getActivate()) {
            self::markTestSkipped('The CreditNote module is not active in the test database.');
        }

        $this->otherRequest = new ConnectionWrapper(new PdoConnection(
            \sprintf('mysql:host=%s;port=%s;dbname=%s', $_SERVER['DATABASE_HOST'], $_SERVER['DATABASE_PORT'] ?? '3306', $_SERVER['DATABASE_NAME']),
            $_SERVER['DATABASE_USER'],
            $_SERVER['DATABASE_PASSWORD'],
        ));

        $fixture = new CreditNoteFixture($this->otherRequest);
        $customer = $fixture->factory()->customer($fixture->factory()->customerTitle(), ['email' => uniqid('ceiling-lock-', true).'@test.com']);
        $this->order = $fixture->paidOrder($customer);
    }

    protected function tearDown(): void
    {
        if (null !== $this->order && null !== $this->otherRequest) {
            // The lines of the order, its taxes and its versions go with it.
            OrderQuery::create()->filterById($this->order->getId())->delete($this->otherRequest);
            OrderAddressQuery::create()->filterById([$this->order->getInvoiceOrderAddressId(), $this->order->getDeliveryOrderAddressId()])->delete($this->otherRequest);
            CartQuery::create()->filterById($this->order->getCartId())->delete($this->otherRequest);
            CustomerQuery::create()->filterById($this->order->getCustomerId())->delete($this->otherRequest);
        }

        $this->order = null;
        $this->otherRequest = null;

        parent::tearDown();
    }

    #[Test]
    public function theOrderIsLockedWhileItsCeilingIsChecked(): void
    {
        $request = $this->getPropelConnection();
        $request->beginTransaction();

        try {
            $creditNote = (new CreditNoteModel())->setOrder($this->order)->setTotalPriceWithTax('0.010000');
            $this->getService(CreditNoteOrderCeiling::class)->assertWithinOrder($creditNote);

            // Another request writing a credit note on the same order, at the same time.
            $this->otherRequest->prepare('SET SESSION innodb_lock_wait_timeout = 1')->execute();
            $this->otherRequest->beginTransaction();

            try {
                $this->otherRequest->prepare(\sprintf('SELECT `id` FROM `order` WHERE `id` = %d FOR UPDATE', $this->order->getId()))->execute();
                self::fail('a second credit note read the ceiling of the order while the first one was being written');
            } catch (\PDOException $exception) {
                self::assertStringContainsString('lock', strtolower($exception->getMessage()), 'it waits until the first one is written');
            } finally {
                $this->otherRequest->rollBack();
            }
        } finally {
            // Nothing was written: the row of the order is free again for tearDown().
            $request->rollBack();
        }
    }
}
