<?php

declare(strict_types=1);

namespace CreditNote\Tests\Integration;

use CreditNote\CreditNote;
use CreditNote\Model\CreditNote as CreditNoteModel;
use CreditNote\Model\CreditNoteQuery;
use CreditNote\Model\OrderCreditNote;
use CreditNote\Tests\Support\AdminSessionInjector;
use CreditNote\Tests\Support\ReadsCsrfToken;
use CreditNote\Tests\Support\CreditNoteFixture;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Thelia\Model\ModuleQuery;
use Thelia\Test\FixtureFactory;
use Thelia\Test\WebIntegrationTestCase;

/**
 * What the merchant sees and sets about the use of a credit note in the back-office modal: what
 * is left of it, the orders it paid with their amount, and whether the customer may spend it
 * over several purchases, a choice that is closed once an order has consumed part of it.
 */
final class CreditNoteUseRouteTest extends WebIntegrationTestCase
{
    use ReadsCsrfToken;

    private CreditNoteFixture $fixture;

    private AdminSessionInjector $injector;

    protected function setUp(): void
    {
        parent::setUp();

        if (!ModuleQuery::create()->findOneByCode(CreditNote::getModuleCode())?->getActivate()) {
            self::markTestSkipped('The CreditNote module is not active in the test database.');
        }

        $this->fixture = new CreditNoteFixture($this->getPropelConnection());
        $this->injector = new AdminSessionInjector();
        $this->getService(EventDispatcherInterface::class)->addSubscriber($this->injector);

        $admin = (new FixtureFactory($this->getPropelConnection()))->admin();
        $admin->eraseCredentials();
        $this->injector->setAdmin($admin);
    }

    protected function tearDown(): void
    {
        if (isset($this->injector)) {
            $this->injector->clear();
        }

        parent::tearDown();
    }

    #[Test]
    public function theModalShowsWhatIsLeftAndWhatEachOrderPaidWithTheCreditNote(): void
    {
        $creditNote = $this->acceptedCreditNote(2);
        $paid = $this->fixture->paidOrder($creditNote->getCustomer());
        (new OrderCreditNote())->setOrderId($paid->getId())->setCreditNoteId($creditNote->getId())->setAmountPrice('35.200000')->save($this->getPropelConnection());

        $crawler = $this->client->request('POST', \sprintf('/admin/credit-note/%d', $creditNote->getId()), ['credit_note_create' => ['action' => 'view']]);

        self::assertSame(200, $this->client->getResponse()->getStatusCode());
        self::assertStringContainsString('200', $crawler->filterXPath('//*[@data-testid="credit-note-modal-balance"]')->text(), 'the total of 235.20 minus the 35.20 an order paid');
        $text = $crawler->text();
        self::assertStringContainsString((string) $paid->getRef(), $text);
        self::assertStringContainsString('35', $text);
        self::assertStringContainsString('paid with this credit note', $text);

        $select = $crawler->filterXPath('//*[@data-testid="credit-note-allow-partial-use"]');
        self::assertNull($select->attr('name'), 'the use is no longer sent once an order was paid with the credit note');
        self::assertNotNull($select->attr('disabled'));
    }

    #[Test]
    public function thePageOfAnOrderShowsTheCreditNotesThatPaidItAndWhatTheyLeftToPay(): void
    {
        $creditNote = $this->acceptedCreditNote(2);
        $other = $this->acceptedCreditNote();
        $paid = $this->fixture->paidOrder($creditNote->getCustomer());
        (new OrderCreditNote())->setOrderId($paid->getId())->setCreditNoteId($creditNote->getId())->setAmountPrice('35.200000')->save($this->getPropelConnection());
        (new OrderCreditNote())->setOrderId($paid->getId())->setCreditNoteId($other->getId())->setAmountPrice('10.000000')->save($this->getPropelConnection());

        $crawler = $this->client->request('GET', \sprintf('/admin/order/update/%d', $paid->getId()));

        self::assertSame(200, $this->client->getResponse()->getStatusCode());
        $block = $crawler->filterXPath('//*[@data-testid="order-credit-notes-used"]');
        self::assertCount(1, $block);
        $text = $block->text();
        self::assertStringContainsString((string) $creditNote->getRef(), $text);
        self::assertStringContainsString('35', $text);
        self::assertStringContainsString((string) $other->getRef(), $text);
        self::assertStringContainsString(
            number_format(round($paid->getTotalAmount() - 45.2, 2), 2, '.', ''),
            str_replace([',', ' '], ['.', ''], $text),
            'what the two credit notes left to pay, once',
        );

        $crawler = $this->client->request('GET', \sprintf('/admin/order/update/%d', $this->fixture->paidOrder($creditNote->getCustomer())->getId()));
        self::assertCount(0, $crawler->filterXPath('//*[@data-testid="order-credit-notes-used"]'), 'nothing for an order paid without credit note');
    }

    #[Test]
    public function theMerchantChoosesWhetherTheCreditNoteIsSpentAtOnceUntilAnOrderIsPaidWithIt(): void
    {
        $creditNote = $this->acceptedCreditNote();

        $crawler = $this->client->request('POST', \sprintf('/admin/credit-note/%d', $creditNote->getId()), ['credit_note_create' => ['action' => 'view']]);
        $select = $crawler->filterXPath('//*[@data-testid="credit-note-allow-partial-use"]');
        self::assertSame('credit_note_create[allow_partial_use]', $select->attr('name'));
        self::assertSame('1', $select->filterXPath('.//option[@selected]')->attr('value'), 'several purchases unless told otherwise');

        $this->update($creditNote, ['allow_partial_use' => 0]);
        self::assertFalse($this->reread($creditNote)->getAllowPartialUse());

        $this->update($creditNote, []);
        self::assertFalse($this->reread($creditNote)->getAllowPartialUse(), 'a form without the field changes nothing');

        $this->update($creditNote, ['allow_partial_use' => 1]);
        self::assertTrue($this->reread($creditNote)->getAllowPartialUse());

        (new OrderCreditNote())->setOrderId($this->fixture->paidOrder($creditNote->getCustomer())->getId())->setCreditNoteId($creditNote->getId())->setAmountPrice('10.000000')->save($this->getPropelConnection());

        $this->update($creditNote, ['allow_partial_use' => 0]);
        self::assertTrue($this->reread($creditNote)->getAllowPartialUse(), 'a credit note partly spent cannot be turned into one to spend at once');
    }

    private function acceptedCreditNote(int $quantity = 1): CreditNoteModel
    {
        $customer = $this->fixture->customer();
        $creditNote = $this->fixture->creditNote($customer, $this->fixture->paidOrder($customer), 'accepted', $quantity);
        $creditNote->save();

        return $creditNote;
    }

    /**
     * @param array<string, mixed> $fields
     */
    private function update(CreditNoteModel $creditNote, array $fields): void
    {
        $this->client->request('POST', \sprintf('/admin/credit-note/%d/_update', $creditNote->getId()), [
            '_token' => $this->csrfToken(),
            'credit_note_create' => $fields + [
                'action' => 'update',
                'ui_target' => 'order',
                'ui_target_id' => $creditNote->getOrderId(),
            ],
        ]);

        self::assertTrue($this->client->getResponse()->isRedirection(), (string) $this->client->getResponse()->getContent());
    }

    private function reread(CreditNoteModel $creditNote): CreditNoteModel
    {
        $reread = CreditNoteQuery::create()->findPk($creditNote->getId());
        $reread->reload();

        return $reread;
    }
}
