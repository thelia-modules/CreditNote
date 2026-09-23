<?php

declare(strict_types=1);

namespace CreditNote\Tests\Integration;

use CreditNote\CreditNote;
use CreditNote\Exception\CreditNoteConsumptionException;
use CreditNote\Model\CreditNoteQuery;
use CreditNote\Model\OrderCreditNoteQuery;
use CreditNote\Service\CreditNoteBalance;
use CreditNote\Service\CreditNoteConsumption;
use CreditNote\Tests\Support\CreditNoteFixture;
use PHPUnit\Framework\Attributes\Test;
use Propel\Runtime\Connection\ConnectionWrapper;
use Propel\Runtime\Connection\PdoConnection;
use Thelia\Model\ModuleQuery;
use Thelia\Test\IntegrationTestCase;

/**
 * Using a credit note on an order: the balance goes down, never below zero, the credit note is
 * marked used at zero, and two orders paid at the same time with the same credit note consume
 * its balance once, the second one waiting on the row lock the first one holds.
 */
final class CreditNoteConsumptionTest extends IntegrationTestCase
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
    public function aCreditNoteIsUsedInSeveralPurchasesUntilNothingIsLeft(): void
    {
        $customer = $this->fixture->customer();
        $creditNote = $this->fixture->creditNote($customer, $this->fixture->paidOrder($customer), 'accepted', 2);
        $creditNote->save();

        $first = $this->consumption()->consume($creditNote->getId(), $this->fixture->paidOrder($customer), 50.0);
        self::assertSame('50.000000', $first->getAmountPrice());
        self::assertSame(185.2, $this->balance()->remaining($creditNote));
        self::assertSame('accepted', $this->reload($creditNote)->getCreditNoteStatus()->getCode());

        $this->consumption()->consume($creditNote->getId(), $this->fixture->paidOrder($customer), 185.2);
        self::assertSame(0.0, $this->balance()->remaining($creditNote));
        self::assertSame('used', $this->reload($creditNote)->getCreditNoteStatus()->getCode(), 'a credit note with nothing left is used');

        $this->expectException(CreditNoteConsumptionException::class);
        $this->expectExceptionMessage('already used');

        $this->consumption()->consume($creditNote->getId(), $this->fixture->paidOrder($customer), 1.0);
    }

    #[Test]
    public function moreThanTheBalanceIsRefusedWithoutWritingAnything(): void
    {
        $customer = $this->fixture->customer();
        $creditNote = $this->fixture->creditNote($customer, $this->fixture->paidOrder($customer), 'accepted');
        $creditNote->save();
        $order = $this->fixture->paidOrder($customer);

        try {
            $this->consumption()->consume($creditNote->getId(), $order, 117.61);
            self::fail('more than the balance was consumed');
        } catch (CreditNoteConsumptionException $exception) {
            self::assertStringContainsString('117.60 left, 117.61 asked', $exception->getMessage());
        }

        self::assertSame(0, OrderCreditNoteQuery::create()->filterByOrderId($order->getId())->count());
        self::assertSame(117.6, $this->balance()->remaining($creditNote));
    }

    #[Test]
    public function aCreditNoteForbiddingPartialUseIsUsedForItsWholeBalanceOrNotAtAll(): void
    {
        $customer = $this->fixture->customer();
        $creditNote = $this->fixture->creditNote($customer, $this->fixture->paidOrder($customer), 'accepted');
        $creditNote->setAllowPartialUse(false);
        $creditNote->save();

        try {
            $this->consumption()->consume($creditNote->getId(), $this->fixture->paidOrder($customer), 100.0);
            self::fail('a partial use of a single-use credit note was accepted');
        } catch (CreditNoteConsumptionException $exception) {
            self::assertStringContainsString('must be used at once', $exception->getMessage());
        }

        $this->consumption()->consume($creditNote->getId(), $this->fixture->paidOrder($customer), 117.6);

        self::assertSame(0.0, $this->balance()->remaining($creditNote));
    }

    #[Test]
    public function theCreditNoteOfAnotherCustomerADraftOrTheSameOrderTwiceAreRefused(): void
    {
        $customer = $this->fixture->customer();
        $accepted = $this->fixture->creditNote($customer, $this->fixture->paidOrder($customer), 'accepted', 2);
        $accepted->save();
        $draft = $this->fixture->creditNote($customer, $this->fixture->paidOrder($customer));
        $draft->save();
        $order = $this->fixture->paidOrder($customer);

        $this->assertRefused($accepted->getId(), $this->fixture->paidOrder($this->fixture->customer()), 10.0, 'does not belong to the customer');
        $this->assertRefused($draft->getId(), $order, 10.0, 'is not accepted');
        $this->assertRefused($accepted->getId(), $order, 0.0, 'must be positive');
        $this->assertRefused(999999, $order, 10.0, 'does not exist');

        $this->consumption()->consume($accepted->getId(), $order, 10.0);
        $this->assertRefused($accepted->getId(), $order, 10.0, 'already used on order');

        self::assertSame(225.2, $this->balance()->remaining($accepted));
    }

    #[Test]
    public function aSecondOrderPaidAtTheSameTimeWaitsForTheFirstAndCannotReadTheBalanceItIsHolding(): void
    {
        $customer = $this->fixture->customer();
        $creditNote = $this->fixture->creditNote($customer, $this->fixture->paidOrder($customer), 'accepted', 2);
        $creditNote->save();
        $secondOrder = $this->fixture->paidOrder($customer);

        // The first order: the use is written and the credit note row stays locked until the
        // surrounding transaction (the one of this test) ends.
        $this->consumption()->consume($creditNote->getId(), $this->fixture->paidOrder($customer), 235.2);

        // The second order, from another connection, as another request would be.
        $contender = $this->dedicatedConnection();
        $contender->prepare('SET SESSION innodb_lock_wait_timeout = 1')->execute();

        try {
            $this->consumption()->consume($creditNote->getId(), $secondOrder, 235.2, $contender);
            self::fail('the second order consumed the balance the first one was holding');
        } catch (\Throwable $exception) {
            self::assertStringContainsString('lock', strtolower($exception->getMessage()), 'the second order waited on the row lock and gave up');
        }

        self::assertSame(1, OrderCreditNoteQuery::create()->filterByCreditNoteId($creditNote->getId())->count(), 'the balance was consumed once');
        self::assertSame(0.0, $this->balance()->remaining($creditNote));
    }

    private function assertRefused(int $creditNoteId, \Thelia\Model\Order $order, float $amount, string $reason): void
    {
        try {
            $this->consumption()->consume($creditNoteId, $order, $amount);
            self::fail(\sprintf('the use was accepted although the credit note %s', $reason));
        } catch (CreditNoteConsumptionException $exception) {
            self::assertStringContainsString($reason, $exception->getMessage());
        }
    }

    private function consumption(): CreditNoteConsumption
    {
        return $this->getService(CreditNoteConsumption::class);
    }

    private function balance(): CreditNoteBalance
    {
        return $this->getService(CreditNoteBalance::class);
    }

    private function reload(\CreditNote\Model\CreditNote $creditNote): \CreditNote\Model\CreditNote
    {
        return CreditNoteQuery::create()->findPk($creditNote->getId());
    }

    private function dedicatedConnection(): ConnectionWrapper
    {
        return new ConnectionWrapper(new PdoConnection(
            \sprintf('mysql:host=%s;port=%s;dbname=%s', $_SERVER['DATABASE_HOST'], $_SERVER['DATABASE_PORT'] ?? '3306', $_SERVER['DATABASE_NAME']),
            $_SERVER['DATABASE_USER'],
            $_SERVER['DATABASE_PASSWORD'],
        ));
    }
}
