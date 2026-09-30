<?php

declare(strict_types=1);

namespace CreditNote\Tests\Integration;

use CreditNote\CreditNote;
use CreditNote\Exception\CreditNoteExceedsOrderException;
use CreditNote\Model\CreditNoteQuery;
use CreditNote\Model\CreditNoteStatusQuery;
use CreditNote\Tests\Support\CreditNoteFixture;
use PHPUnit\Framework\Attributes\Test;
use Thelia\Model\ModuleQuery;
use Thelia\Test\IntegrationTestCase;

/**
 * Two credit notes on the same order cannot exceed the amount of the order: the guard runs
 * when a credit note is saved, whatever wrote it. The order of the fixture is worth 235.20
 * with tax (two lines at 117.60), a credit note of one line 117.60.
 */
final class CreditNoteOrderCeilingTest extends IntegrationTestCase
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
    public function aCreditNoteWithoutOrderHasNoCeiling(): void
    {
        $customer = $this->fixture->customer();

        $rebate = $this->fixture->creditNote($customer, null, 'accepted', 1, 'rebate');
        $rebate->setTotalPrice('10000.000000')->setTotalPriceWithTax('12000.000000');
        $rebate->save();

        self::assertNotNull($rebate->getId());
    }
}
