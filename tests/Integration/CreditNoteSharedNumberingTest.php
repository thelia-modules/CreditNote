<?php

declare(strict_types=1);

namespace CreditNote\Tests\Integration;

use CreditNote\CreditNote;
use CreditNote\Tests\Support\CreditNoteFixture;
use InvoiceRef\Service\InvoiceRefSequence;
use PHPUnit\Framework\Attributes\Test;
use Thelia\Model\ConfigQuery;
use Thelia\Model\ModuleQuery;
use Thelia\Test\IntegrationTestCase;

/**
 * A shop that numbers its credit notes like its invoices (`invoice_ref_with_thelia_order`) draws
 * them from the series of the InvoiceRef module (`InvoiceRefSequence::next()`, which reads the counter
 * under a lock and skips a number an order already carries). The lock itself is exercised by the
 * concurrency tests of InvoiceRef.
 */
final class CreditNoteSharedNumberingTest extends IntegrationTestCase
{
    private const FIRST_NUMBER = 900000001;

    private CreditNoteFixture $fixture;

    protected function setUp(): void
    {
        parent::setUp();

        if (!ModuleQuery::create()->findOneByCode(CreditNote::getModuleCode())?->getActivate()) {
            self::markTestSkipped('The CreditNote module is not active in the test database.');
        }

        if (!class_exists(InvoiceRefSequence::class)) {
            self::markTestSkipped('The InvoiceRef module 3.1 or later is not installed.');
        }

        $this->fixture = new CreditNoteFixture($this->getPropelConnection());
        CreditNote::setConfigValue(CreditNote::CONFIG_KEY_INVOICE_REF_WITH_THELIA_ORDER, 1);
        ConfigQuery::write(InvoiceRefSequence::CONFIG_NAME, (string) self::FIRST_NUMBER, true, true);
    }

    #[Test]
    public function anAcceptedCreditNoteTakesTheNextNumberOfTheInvoiceSeriesAndMovesTheCounter(): void
    {
        $customer = $this->fixture->customer();

        $creditNote = $this->fixture->creditNote($customer, $this->fixture->paidOrder($customer), 'accepted');
        $creditNote->save();

        self::assertSame((string) self::FIRST_NUMBER, $creditNote->getInvoiceRef());
        self::assertSame((string) (self::FIRST_NUMBER + 1), $this->counter());

        $second = $this->fixture->creditNote($customer, $this->fixture->paidOrder($customer), 'accepted');
        $second->save();

        self::assertSame((string) (self::FIRST_NUMBER + 1), $second->getInvoiceRef());
        self::assertSame((string) (self::FIRST_NUMBER + 2), $this->counter());
    }

    #[Test]
    public function aNumberAnOrderAlreadyCarriesIsSkipped(): void
    {
        $customer = $this->fixture->customer();
        $order = $this->fixture->paidOrder($customer);
        $order->setInvoiceRef((string) self::FIRST_NUMBER)->save($this->getPropelConnection());

        $creditNote = $this->fixture->creditNote($customer, $order, 'accepted');
        $creditNote->save();

        self::assertSame((string) (self::FIRST_NUMBER + 1), $creditNote->getInvoiceRef());
        self::assertSame((string) (self::FIRST_NUMBER + 2), $this->counter());
    }

    #[Test]
    public function aCreditNoteThatIsNotAcceptedDrawsNoNumber(): void
    {
        $customer = $this->fixture->customer();

        $this->fixture->creditNote($customer, $this->fixture->paidOrder($customer))->save();

        self::assertSame((string) self::FIRST_NUMBER, $this->counter());
    }

    private function counter(): string
    {
        $statement = $this->getPropelConnection()->prepare('SELECT `value` FROM `config` WHERE `name` = :name');
        $statement->execute([':name' => InvoiceRefSequence::CONFIG_NAME]);

        return (string) $statement->fetchColumn();
    }
}
