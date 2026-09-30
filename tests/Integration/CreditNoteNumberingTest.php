<?php

declare(strict_types=1);

namespace CreditNote\Tests\Integration;

use CreditNote\CreditNote;
use CreditNote\Model\CreditNoteQuery;
use CreditNote\Model\CreditNoteStatusQuery;
use CreditNote\Tests\Support\CreditNoteFixture;
use PHPUnit\Framework\Attributes\Test;
use Thelia\Model\ModuleQuery;
use Thelia\Test\IntegrationTestCase;

/**
 * The numbering the module does when a credit note is saved: its reference on insert, its
 * accounting number when it is accepted, both from counters that never go back. The status
 * flow the module enforces on the way.
 */
final class CreditNoteNumberingTest extends IntegrationTestCase
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
    public function aNewCreditNoteTakesTheNextReferenceAndMovesTheCounter(): void
    {
        $customer = $this->fixture->customer();
        $order = $this->fixture->paidOrder($customer);
        $counterBefore = (int) CreditNote::getConfigValue(CreditNote::CONFIG_KEY_REF_INCREMENT, 1);

        $creditNote = $this->fixture->creditNote($customer, $order);
        $creditNote->save();

        self::assertSame($this->expectedReference('CN', $counterBefore + 1), $creditNote->getRef());
        self::assertSame($counterBefore + 1, (int) CreditNote::getConfigValue(CreditNote::CONFIG_KEY_REF_INCREMENT), 'the reference counter moved by one');
        self::assertNull($creditNote->getInvoiceRef(), 'a proposed credit note has no accounting number yet');
        self::assertInstanceOf(\DateTimeInterface::class, $creditNote->getInvoiceDate());
        self::assertSame('98.000000', $creditNote->getTotalPrice());
        self::assertSame('117.600000', $creditNote->getTotalPriceWithTax());

        $second = $this->fixture->creditNote($customer, $order);
        $second->save();

        self::assertSame($this->expectedReference('CN', $counterBefore + 2), $second->getRef());
        self::assertNotSame($creditNote->getRef(), $second->getRef());
    }

    #[Test]
    public function aDeletedCreditNoteNeverGivesItsNumberBack(): void
    {
        $customer = $this->fixture->customer();
        $order = $this->fixture->paidOrder($customer);

        $deleted = $this->fixture->creditNote($customer, $order);
        $deleted->save();
        $deletedReference = $deleted->getRef();
        $deleted->delete();

        self::assertNull(CreditNoteQuery::create()->findOneByRef($deletedReference));

        $next = $this->fixture->creditNote($customer, $order);
        $next->save();

        self::assertGreaterThan($deletedReference, $next->getRef(), 'the counter does not step back to the number of a deleted credit note');
    }

    #[Test]
    public function acceptingACreditNoteAssignsItsAccountingNumberOnce(): void
    {
        $customer = $this->fixture->customer();
        $order = $this->fixture->paidOrder($customer);
        $counterBefore = (int) CreditNote::getConfigValue(CreditNote::CONFIG_KEY_INVOICE_REF_INCREMENT, 1);

        $creditNote = $this->fixture->creditNote($customer, $order);
        $creditNote->save();
        self::assertNull($creditNote->getInvoiceRef());

        $creditNote->setCreditNoteStatus(CreditNoteStatusQuery::create()->findOneByCode('accepted'));
        $creditNote->save();

        $accountingNumber = $creditNote->getInvoiceRef();
        self::assertSame($this->expectedReference('FA', $counterBefore + 1), $accountingNumber);
        self::assertNotSame($creditNote->getRef(), $accountingNumber, 'the accounting number is distinct from the credit note reference');
        self::assertSame($counterBefore + 1, (int) CreditNote::getConfigValue(CreditNote::CONFIG_KEY_INVOICE_REF_INCREMENT));

        // Using the credit note keeps the number it was accepted under.
        $creditNote->setCreditNoteStatus(CreditNoteStatusQuery::create()->findOneByCode('used'));
        $creditNote->save();

        self::assertSame($accountingNumber, $creditNote->getInvoiceRef());
        self::assertSame($counterBefore + 1, (int) CreditNote::getConfigValue(CreditNote::CONFIG_KEY_INVOICE_REF_INCREMENT), 'no second number was drawn');
    }

    #[Test]
    public function aCreditNoteCreatedAcceptedIsNumberedInTheSameSave(): void
    {
        $customer = $this->fixture->customer();

        $creditNote = $this->fixture->creditNote($customer, null, 'accepted', 1, 'rebate');
        $creditNote->save();

        self::assertStringStartsWith('CN', (string) $creditNote->getRef());
        self::assertStringStartsWith('FA', (string) $creditNote->getInvoiceRef());
        self::assertNull($creditNote->getOrderId(), 'a rebate needs no order');
    }

    #[Test]
    public function theStatusFlowRefusesASkippedOrReversedTransition(): void
    {
        $customer = $this->fixture->customer();
        $order = $this->fixture->paidOrder($customer);

        $creditNote = $this->fixture->creditNote($customer, $order);
        $creditNote->save();

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('You do not respect the status flow');

        // proposed -> used skips the acceptance
        $creditNote->setStatusId(CreditNoteStatusQuery::create()->findOneByCode('used')->getId());
    }

    #[Test]
    public function anAcceptedCreditNoteCannotGoBackToProposed(): void
    {
        $customer = $this->fixture->customer();
        $order = $this->fixture->paidOrder($customer);

        $creditNote = $this->fixture->creditNote($customer, $order, 'accepted');
        $creditNote->save();
        self::assertNotNull($creditNote->getInvoiceRef());

        $this->expectException(\Exception::class);

        $creditNote->setStatusId(CreditNoteStatusQuery::create()->findOneByCode('proposed')->getId());
    }

    #[Test]
    public function aRefusedCreditNoteCanBeProposedAgain(): void
    {
        $customer = $this->fixture->customer();
        $order = $this->fixture->paidOrder($customer);

        $creditNote = $this->fixture->creditNote($customer, $order);
        $creditNote->save();

        $creditNote->setStatusId(CreditNoteStatusQuery::create()->findOneByCode('refused')->getId());
        $creditNote->save();
        $creditNote->setStatusId(CreditNoteStatusQuery::create()->findOneByCode('proposed')->getId());
        $creditNote->save();

        self::assertSame('proposed', $creditNote->getCreditNoteStatus()->getCode());
        self::assertNull($creditNote->getInvoiceRef(), 'a credit note that was never accepted has no accounting number');
    }

    #[Test]
    public function theDatabaseRefusesASecondCreditNoteUnderAnExistingReference(): void
    {
        $customer = $this->fixture->customer();
        $order = $this->fixture->paidOrder($customer);
        $first = $this->fixture->creditNote($customer, $order);
        $first->save();

        $duplicate = $this->fixture->creditNote($customer, $order);
        $duplicate->setRef($first->getRef());

        try {
            $duplicate->save();
            self::fail('two credit notes share a reference');
        } catch (\Propel\Runtime\Exception\PropelException $exception) {
            self::assertStringContainsString('ref_UNIQUE', $exception->getMessage() . ($exception->getPrevious()?->getMessage() ?? ''));
        }

        self::assertSame(1, CreditNoteQuery::create()->filterByRef($first->getRef())->count());
    }

    #[Test]
    public function theDatabaseRefusesASecondCreditNoteUnderAnExistingAccountingNumber(): void
    {
        $customer = $this->fixture->customer();
        $order = $this->fixture->paidOrder($customer);
        $first = $this->fixture->creditNote($customer, $order, 'accepted');
        $first->save();

        // Accepted as well: an accounting number on a credit note that is not accepted is
        // refused by the numbering itself, before the database gets a say.
        $duplicate = $this->fixture->creditNote($customer, $order, 'accepted');
        $duplicate->setInvoiceRef($first->getInvoiceRef());

        $this->expectException(\Propel\Runtime\Exception\PropelException::class);

        $duplicate->save();
    }

    private function expectedReference(string $prefix, int $number): string
    {
        return $prefix . str_pad((string) $number, (int) CreditNote::getConfigValue(CreditNote::CONFIG_KEY_REF_MIN_LENGTH, 8), '0', \STR_PAD_LEFT);
    }
}
