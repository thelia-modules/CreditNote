<?php

declare(strict_types=1);

namespace CreditNote\Tests\Integration;

use CreditNote\CreditNote;
use CreditNote\Model\OrderCreditNote;
use CreditNote\Service\CreditNoteBalance;
use CreditNote\Tests\Support\CreditNoteFixture;
use PHPUnit\Framework\Attributes\Test;
use Thelia\Model\ModuleQuery;
use Thelia\Test\IntegrationTestCase;

/**
 * The balance of a credit note is what the orders it was used on left of it, read from the
 * order_credit_note rows: never stored, never negative by construction of the module rules.
 */
final class CreditNoteBalanceTest extends IntegrationTestCase
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
    public function anUnusedCreditNoteIsWorthItsTotalWithTax(): void
    {
        $customer = $this->fixture->customer();
        $creditNote = $this->fixture->creditNote($customer, $this->fixture->paidOrder($customer), 'accepted', 2);
        $creditNote->save();

        $balance = $this->getService(CreditNoteBalance::class);

        self::assertSame(0.0, $balance->used($creditNote));
        self::assertSame(235.2, $balance->remaining($creditNote));
    }

    #[Test]
    public function everyOrderTheCreditNoteWasUsedOnLowersItsBalance(): void
    {
        $customer = $this->fixture->customer();
        $creditNote = $this->fixture->creditNote($customer, $this->fixture->paidOrder($customer), 'accepted', 2);
        $creditNote->save();

        $this->useOn($this->fixture->paidOrder($customer)->getId(), $creditNote->getId(), '50.000000');
        $this->useOn($this->fixture->paidOrder($customer)->getId(), $creditNote->getId(), '100.200000');

        $balance = $this->getService(CreditNoteBalance::class);

        self::assertSame(150.2, $balance->used($creditNote));
        self::assertSame(85.0, $balance->remaining($creditNote));
    }

    #[Test]
    public function theUseOfAnotherCreditNoteDoesNotCount(): void
    {
        $customer = $this->fixture->customer();
        $order = $this->fixture->paidOrder($customer);
        $creditNote = $this->fixture->creditNote($customer, $order, 'accepted');
        $creditNote->save();
        $other = $this->fixture->creditNote($customer, $order, 'accepted');
        $other->save();

        $this->useOn($this->fixture->paidOrder($customer)->getId(), $other->getId(), '117.600000');

        $balance = $this->getService(CreditNoteBalance::class);

        self::assertSame(117.6, $balance->remaining($creditNote));
        self::assertSame(0.0, $balance->remaining($other));
    }

    private function useOn(int $orderId, int $creditNoteId, string $amount): void
    {
        (new OrderCreditNote())
            ->setOrderId($orderId)
            ->setCreditNoteId($creditNoteId)
            ->setAmountPrice($amount)
            ->save($this->getPropelConnection());
    }
}
