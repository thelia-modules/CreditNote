<?php

declare(strict_types=1);

namespace CreditNote\Tests\Integration;

use CreditNote\CreditNote;
use CreditNote\Exception\CreditNoteExceedsOrderException;
use CreditNote\Exception\CreditNoteLineExceedsOrderException;
use CreditNote\Model\CreditNote as CreditNoteModel;
use CreditNote\Model\CreditNoteQuery;
use CreditNote\Model\CreditNoteStatusQuery;
use CreditNote\Service\CreditNoteOrderCeiling;
use CreditNote\Service\CreditNoteOrderLines;
use CreditNote\Tests\Support\CreditNoteFixture;
use CreditNote\Tests\Support\RecordsQueries;
use PHPUnit\Framework\Attributes\Test;
use Propel\Runtime\Connection\ConnectionWrapper;
use Propel\Runtime\Connection\PdoConnection;
use Thelia\Model\ModuleConfigQuery;
use Thelia\Model\ModuleQuery;
use Thelia\Model\OrderQuery;
use Thelia\Test\IntegrationTestCase;

/**
 * Two credit notes on the same order cannot exceed the amount of the order: the guard runs
 * when a credit note is saved, whatever wrote it. The order of the fixture is worth 235.20
 * with tax (two lines at 117.60), a credit note of one line 117.60.
 */
final class CreditNoteOrderCeilingTest extends IntegrationTestCase
{
    use RecordsQueries;

    private CreditNoteFixture $fixture;

    protected function setUp(): void
    {
        parent::setUp();

        if (!ModuleQuery::create()->findOneByCode(CreditNote::getModuleCode())?->getActivate()) {
            self::markTestSkipped('The CreditNote module is not active in the test database.');
        }

        $this->fixture = new CreditNoteFixture($this->getPropelConnection());
    }

    #[Test]
    public function creditNotesMayRefundTheOrderUpToItsTotal(): void
    {
        $customer = $this->fixture->customer();
        $order = $this->fixture->paidOrder($customer);

        $this->fixture->creditNote($customer, $order, 'accepted')->save();
        $this->fixture->creditNote($customer, $order, 'proposed')->save();

        self::assertSame(2, CreditNoteQuery::create()->filterByOrderId($order->getId())->count(), 'two credit notes of 117.60 fit in an order of 235.20');
    }

    #[Test]
    public function aCreditNoteBeyondWhatIsLeftOfTheOrderIsRefusedAndNotWritten(): void
    {
        $customer = $this->fixture->customer();
        $order = $this->fixture->paidOrder($customer);
        $this->fixture->creditNote($customer, $order, 'accepted', 2)->save();

        $countBefore = CreditNoteQuery::create()->count();
        $third = $this->fixture->creditNote($customer, $order);

        try {
            $third->save();
            self::fail('a credit note beyond the total of the order was written');
        } catch (CreditNoteExceedsOrderException $exception) {
            self::assertSame($order->getRef(), $exception->orderRef);
            self::assertSame(235.2, $exception->ceiling);
            self::assertSame(235.2, $exception->granted);
            self::assertSame(117.6, $exception->asked);
        }

        self::assertSame($countBefore, CreditNoteQuery::create()->count(), 'nothing was written');
        self::assertNull($third->getRef(), 'no reference was drawn for the refused credit note');
    }

    #[Test]
    public function aRefusedCreditNoteGrantsNothing(): void
    {
        $customer = $this->fixture->customer();
        $order = $this->fixture->paidOrder($customer);

        $refused = $this->fixture->creditNote($customer, $order, 'proposed', 2);
        $refused->save();
        $refused->setCreditNoteStatus(CreditNoteStatusQuery::create()->findOneByCode('refused'));
        $refused->save();

        // The whole order can be granted again.
        $this->fixture->creditNote($customer, $order, 'accepted', 2)->save();

        self::assertSame(2, CreditNoteQuery::create()->filterByOrderId($order->getId())->count());
    }

    #[Test]
    public function raisingAnExistingCreditNoteBeyondTheOrderIsRefused(): void
    {
        $customer = $this->fixture->customer();
        $order = $this->fixture->paidOrder($customer);
        $this->fixture->creditNote($customer, $order, 'accepted')->save();
        $second = $this->fixture->creditNote($customer, $order);
        $second->save();

        $second->setTotalPrice('196.000000')->setTotalPriceWithTax('235.200000');

        $this->expectException(CreditNoteExceedsOrderException::class);

        $second->save();
    }

    #[Test]
    public function theCeilingIsEnforcedWhileTheSettingIsAbsent(): void
    {
        $this->forgetSetting();

        $customer = $this->fixture->customer();
        $order = $this->fixture->paidOrder($customer);
        $this->fixture->creditNote($customer, $order, 'accepted', 2)->save();

        $this->expectException(CreditNoteExceedsOrderException::class);

        $this->fixture->creditNote($customer, $order)->save();
    }

    #[Test]
    public function aShopThatTurnedTheCeilingOffGrantsBeyondTheOrderAndStillUsesTheCreditNote(): void
    {
        CreditNote::setConfigValue(CreditNote::CONFIG_KEY_ORDER_CEILING, 0);
        ModuleConfigQuery::resetConfigCache();

        $customer = $this->fixture->customer();
        $order = $this->fixture->paidOrder($customer);
        $this->fixture->creditNote($customer, $order, 'accepted', 2)->save();

        $beyond = $this->fixture->gesture($customer, $order, 117.6);
        $beyond->save();
        $beyond->setCreditNoteStatus(CreditNoteStatusQuery::create()->findOneByCode('used'));
        $beyond->save();

        self::assertSame(2, CreditNoteQuery::create()->filterByOrderId($order->getId())->count());
        self::assertSame('used', $beyond->getCreditNoteStatus()->getCode());
        self::assertNotNull($beyond->getInvoiceRef(), 'the credit note granted beyond the order is numbered like any other');
    }

    #[Test]
    public function theSettingDefaultsToOnAndTheUpdateAddsItToAnOlderInstallation(): void
    {
        self::assertTrue(CreditNote::isOrderCeilingEnforced());

        $this->forgetSetting();
        (new CreditNote())->update('3.0.2', '4.1.0', $this->getPropelConnection());

        self::assertSame('1', CreditNote::getConfigValue(CreditNote::CONFIG_KEY_ORDER_CEILING));
    }

    private function forgetSetting(): void
    {
        ModuleConfigQuery::create()
            ->filterByModuleId(CreditNote::getModuleId())
            ->filterByName(CreditNote::CONFIG_KEY_ORDER_CEILING)
            ->delete($this->getPropelConnection());
        ModuleConfigQuery::resetConfigCache();
    }

    #[Test]
    public function aLineOfTheOrderIsNotRefundedTwiceEvenWithTheCeilingOff(): void
    {
        CreditNote::setConfigValue(CreditNote::CONFIG_KEY_ORDER_CEILING, 0);
        ModuleConfigQuery::resetConfigCache();

        $customer = $this->fixture->customer();
        $order = $this->fixture->paidOrder($customer);
        $this->fixture->creditNote($customer, $order, 'accepted')->save();
        $this->fixture->creditNote($customer, $order, 'refused', 2)->save();
        $second = $this->fixture->creditNote($customer, $order, 'proposed');
        $second->save();

        // The line holds two units: one refunded, one proposed, the refused ones do not count.
        try {
            $this->fixture->creditNote($customer, $order, 'proposed')->save();
            self::fail('a third unit of a line of two was refunded');
        } catch (CreditNoteLineExceedsOrderException $exception) {
            self::assertSame([2, 2, 1], [$exception->held, $exception->refunded, $exception->asked]);
        }

        // Nor by changing a credit note that exists.
        $second->getCreditNoteDetails()->getFirst()->setQuantity(2);
        $second->setTotalPriceWithTax('235.200000');

        try {
            $second->save();
            self::fail('a credit note was changed to refund a unit the order no longer has');
        } catch (CreditNoteLineExceedsOrderException $exception) {
            self::assertSame([2, 1, 2], [$exception->held, $exception->refunded, $exception->asked]);
        }

        self::assertSame(3, CreditNoteQuery::create()->filterByOrderId($order->getId())->count());
        CreditNote::setConfigValue(CreditNote::CONFIG_KEY_ORDER_CEILING, 1);
    }

    #[Test]
    public function whatTheOtherCreditNotesGrantIsReadAsItIsNowNotAsTheTransactionFirstSawIt(): void
    {
        $customer = $this->fixture->customer();
        $order = $this->fixture->paidOrder($customer);
        $this->fixture->creditNote($customer, $order, 'accepted')->save();
        $second = $this->fixture->creditNote($customer, $order, 'proposed');

        $statements = $this->queriesOf(function () use ($second): void {
            $this->getService(CreditNoteOrderCeiling::class)->assertWithinOrder($second);
            $this->getService(CreditNoteOrderLines::class)->assertWithinOrder($second);
        });

        // A sum read without lock is the one of the first read of the transaction: it would
        // miss a credit note another request wrote while this one waited on the order.
        $sums = array_values(array_filter($statements, static fn (string $sql): bool => str_contains($sql, 'SUM(')));
        self::assertCount(2, $sums, implode("\n", $statements));

        foreach ($sums as $sum) {
            self::assertStringEndsWith('FOR UPDATE', trim($sum));
        }

        $lock = array_values(array_filter(array_keys($statements), static fn (int $i): bool => str_contains($statements[$i], 'FROM `order` WHERE `id`')));
        self::assertNotSame([], $lock, 'the order is locked');
        self::assertLessThan(array_search($sums[0], $statements, true), $lock[0], 'before the sums are read');
    }

    #[Test]
    public function theOrderIsLockedWhileItsCeilingIsChecked(): void
    {
        // An order the database already holds: a second connection only waits on a row it sees.
        $order = OrderQuery::create()->orderById()->findOne();
        self::assertNotNull($order, 'the test database has an order');
        $creditNote = (new CreditNoteModel())->setOrder($order)->setTotalPriceWithTax('0.010000');

        $this->getService(CreditNoteOrderCeiling::class)->assertWithinOrder($creditNote);

        // Another request writing a credit note on the same order, at the same time.
        $contender = new ConnectionWrapper(new PdoConnection(
            \sprintf('mysql:host=%s;port=%s;dbname=%s', $_SERVER['DATABASE_HOST'], $_SERVER['DATABASE_PORT'] ?? '3306', $_SERVER['DATABASE_NAME']),
            $_SERVER['DATABASE_USER'],
            $_SERVER['DATABASE_PASSWORD'],
        ));
        $contender->prepare('SET SESSION innodb_lock_wait_timeout = 1')->execute();
        $contender->beginTransaction();

        try {
            $contender->prepare(\sprintf('SELECT `id` FROM `order` WHERE `id` = %d FOR UPDATE', $order->getId()))->execute();
            self::fail('a second credit note read the ceiling of the order while the first one was being written');
        } catch (\Throwable $exception) {
            self::assertStringContainsString('lock', strtolower($exception->getMessage()), 'it waits until the first one is written');
        } finally {
            $contender->rollBack();
        }
    }

    #[Test]
    public function aCreditNoteWithoutOrderHasNoCeiling(): void
    {
        $customer = $this->fixture->customer();

        $rebate = $this->fixture->creditNote($customer, null, 'accepted', 1, 'rebate');
        $rebate->setTotalPrice('10000.000000')->setTotalPriceWithTax('12000.000000');
        $rebate->save();

        self::assertNotNull($rebate->getId());
    }
}
