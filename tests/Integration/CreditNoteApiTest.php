<?php

declare(strict_types=1);

namespace CreditNote\Tests\Integration;

use CreditNote\CreditNote;
use CreditNote\Model\CreditNote as CreditNoteModel;
use CreditNote\Model\CreditNoteQuery;
use CreditNote\Model\OrderCreditNote;
use CreditNote\Tests\Support\CreditNoteFixture;
use PHPUnit\Framework\Attributes\Test;
use Propel\Runtime\Propel;
use Thelia\Core\Security\AccessManager;
use Thelia\Core\Security\Resource\AdminResources;
use Thelia\Model\Admin;
use Thelia\Model\AdminLogQuery;
use Thelia\Model\Customer;
use Thelia\Model\ModuleQuery;
use Thelia\Model\ModuleConfigQuery;
use Thelia\Model\Order;
use Thelia\Model\OrderStatus;
use Thelia\Model\OrderStatusQuery;
use Thelia\Model\ProfileModule;
use Thelia\Test\ApiTestCase;
use Thelia\Test\FixtureFactory;

/**
 * The credit notes over the API. The customer account reads the accepted credit notes of the
 * signed-in customer, with their balance, and never those of someone else. The administration
 * reads them all, filtered, and creates one from an order with its lines and quantities, under
 * the same rights and the same ceiling as the back-office.
 */
final class CreditNoteApiTest extends ApiTestCase
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
    public function aCustomerReadsTheirAcceptedCreditNotesWithWhatIsLeftOfThem(): void
    {
        $customer = $this->fixture->customer();
        $partlyUsed = $this->creditNote($customer, 'accepted', 2);
        $untouched = $this->creditNote($customer, 'accepted');
        $draft = $this->creditNote($customer, 'proposed');
        $this->creditNote($this->fixture->customer(), 'accepted');
        (new OrderCreditNote())->setOrderId($this->fixture->paidOrder($customer)->getId())->setCreditNoteId($partlyUsed->getId())->setAmountPrice('35.200000')->save($this->getPropelConnection());
        $token = $this->authenticateAsCustomer($customer);

        $response = $this->jsonRequest('GET', '/api/front/account/credit-notes', token: $token, format: 'json');

        self::assertJsonResponseSuccessful($response);
        $rows = array_column(self::decodeJson($response), null, 'id');
        self::assertSame([$partlyUsed->getId(), $untouched->getId()], $this->sorted(array_keys($rows)), 'the accepted credit notes of the customer, and nothing else');
        self::assertArrayNotHasKey($draft->getId(), $rows, 'a proposed credit note is a draft of the merchant');
        self::assertSame('200.000000', $rows[$partlyUsed->getId()]['balance'], 'the 35.20 an order was paid with are deducted');
        self::assertSame('235.200000', $rows[$partlyUsed->getId()]['totalPriceWithTax']);
        self::assertSame('117.600000', $rows[$untouched->getId()]['balance']);
        self::assertSame($partlyUsed->getRef(), $rows[$partlyUsed->getId()]['ref']);
        self::assertSame('accepted', $rows[$partlyUsed->getId()]['status']);
        self::assertTrue($rows[$partlyUsed->getId()]['allowPartialUse']);
        self::assertArrayNotHasKey('customerId', $rows[$partlyUsed->getId()], 'the account API hands over what the account page shows');

        $item = self::decodeJson($this->jsonRequest('GET', '/api/front/account/credit-notes/'.$partlyUsed->getId(), token: $token, format: 'json'));
        self::assertSame('200.000000', $item['balance']);
    }

    #[Test]
    public function aCustomerNeverReadsTheCreditNoteOfAnotherCustomerNorADraft(): void
    {
        $customer = $this->fixture->customer();
        $stranger = $this->fixture->customer();
        $theirs = $this->creditNote($stranger, 'accepted');
        $draft = $this->creditNote($customer, 'proposed');
        $token = $this->authenticateAsCustomer($customer);

        self::assertSame([], self::decodeJson($this->jsonRequest('GET', '/api/front/account/credit-notes', token: $token, format: 'json')), 'the collection of a customer without accepted credit note is empty');
        self::assertSame(404, $this->jsonRequest('GET', '/api/front/account/credit-notes/'.$theirs->getId(), token: $token)->getStatusCode(), 'the credit note of someone else is not found');
        self::assertSame(404, $this->jsonRequest('GET', '/api/front/account/credit-notes/'.$draft->getId(), token: $token)->getStatusCode(), 'nor is a draft');
        self::assertSame(404, $this->jsonRequest('GET', '/api/front/account/credit-notes/999999', token: $token)->getStatusCode());

        $strangerToken = $this->authenticateAsCustomer($stranger);
        self::assertSame(200, $this->jsonRequest('GET', '/api/front/account/credit-notes/'.$theirs->getId(), token: $strangerToken)->getStatusCode(), 'its owner reads it');
    }

    #[Test]
    public function theCreditNotesAreClosedToAVisitorAndTheAdministrationToACustomer(): void
    {
        $customer = $this->fixture->customer();
        $creditNote = $this->creditNote($customer, 'accepted');

        self::assertSame(401, $this->jsonRequest('GET', '/api/front/account/credit-notes')->getStatusCode());
        self::assertSame(401, $this->jsonRequest('GET', '/api/admin/credit_notes')->getStatusCode());

        $token = $this->authenticateAsCustomer($customer);

        self::assertContains($this->jsonRequest('GET', '/api/admin/credit_notes', token: $token)->getStatusCode(), [401, 403]);
        self::assertContains($this->jsonRequest('GET', '/api/admin/credit_notes/'.$creditNote->getId(), token: $token)->getStatusCode(), [401, 403]);
        self::assertContains(
            $this->jsonRequest('POST', '/api/admin/credit_notes', ['orderId' => $creditNote->getOrderId(), 'type' => 'order_full_refund'], $token)->getStatusCode(),
            [401, 403],
        );
    }

    #[Test]
    public function anAdministratorReadsTheCreditNotesFilteredAndOneWithItsLines(): void
    {
        $customer = $this->fixture->customer();
        $order = $this->fixture->paidOrder($customer);
        $accepted = $this->creditNote($customer, 'accepted', 2, $order);
        $draft = $this->creditNote($customer, 'proposed');
        $elsewhere = $this->creditNote($this->fixture->customer(), 'accepted');
        $token = $this->authenticateAsAdmin($this->administrator());

        $ofCustomer = array_column(self::decodeJson($this->jsonRequest('GET', '/api/admin/credit_notes?customerId='.$customer->getId(), token: $token, format: 'json')), 'id');
        self::assertSame([$accepted->getId(), $draft->getId()], $this->sorted($ofCustomer));

        $acceptedOnes = array_column(self::decodeJson($this->jsonRequest('GET', \sprintf('/api/admin/credit_notes?customerId=%d&status=accepted', $customer->getId()), token: $token, format: 'json')), 'id');
        self::assertSame([$accepted->getId()], $acceptedOnes);

        $byRef = array_column(self::decodeJson($this->jsonRequest('GET', '/api/admin/credit_notes?ref='.substr((string) $elsewhere->getRef(), 2), token: $token, format: 'json')), 'id');
        self::assertSame([$elsewhere->getId()], $byRef);

        $ofOrder = array_column(self::decodeJson($this->jsonRequest('GET', '/api/admin/credit_notes?orderId='.$order->getId(), token: $token, format: 'json')), 'id');
        self::assertSame([$accepted->getId()], $ofOrder);

        $paged = $this->jsonRequest('GET', \sprintf('/api/admin/credit_notes?customerId=%d&itemsPerPage=1', $customer->getId()), token: $token);
        self::assertJsonCollectionHasCount(1, $paged);
        self::assertHydraTotalItems(2, $paged);

        $item = self::decodeJson($this->jsonRequest('GET', '/api/admin/credit_notes/'.$accepted->getId(), token: $token, format: 'json'));
        self::assertSame($customer->getId(), $item['customerId']);
        self::assertSame($order->getId(), $item['orderId']);
        self::assertSame('back_product', $item['type']);
        self::assertSame('235.200000', $item['balance']);
        self::assertCount(1, $item['lines']);
        self::assertSame(2, $item['lines'][0]['quantity']);
        self::assertSame('117.600000', $item['lines'][0]['priceWithTax']);

        self::assertSame(404, $this->jsonRequest('GET', '/api/admin/credit_notes/999999', token: $token)->getStatusCode());

        $secondPage = $this->jsonRequest('GET', \sprintf('/api/admin/credit_notes?customerId=%d&itemsPerPage=1&page=2', $customer->getId()), token: $token, format: 'json');
        self::assertSame([$accepted->getId()], array_column(self::decodeJson($secondPage), 'id'), 'newest first: the older one is on the second page');

        $wildcard = self::decodeJson($this->jsonRequest('GET', '/api/admin/credit_notes?ref=%25', token: $token, format: 'json'));
        self::assertSame([], $wildcard, 'a wildcard in the reference searched is a character, not a pattern');
    }

    #[Test]
    public function aCreditNoteThatIsNotAcceptedHasNoBalance(): void
    {
        $customer = $this->fixture->customer();
        $draft = $this->creditNote($customer, 'proposed');
        $refused = $this->creditNote($customer, 'refused');
        $accepted = $this->creditNote($customer, 'accepted');
        $token = $this->authenticateAsAdmin($this->administrator());

        $balances = [];

        foreach (self::decodeJson($this->jsonRequest('GET', '/api/admin/credit_notes?customerId='.$customer->getId(), token: $token, format: 'json')) as $row) {
            $balances[$row['id']] = $row['balance'] ?? null;
        }

        self::assertEquals([$draft->getId() => null, $refused->getId() => null, $accepted->getId() => '117.600000'], $balances, 'a draft or a refused credit note pays nothing');
        self::assertNull(self::decodeJson($this->jsonRequest('GET', '/api/admin/credit_notes/'.$draft->getId(), token: $token, format: 'json'))['balance'] ?? null);
    }

    #[Test]
    public function aPageOfCreditNotesCostsTheSameQueriesWhateverItsLength(): void
    {
        $customer = $this->fixture->customer();
        $token = $this->authenticateAsAdmin($this->administrator());
        $connection = $this->getPropelConnection();
        $connection->useDebug(true);
        $queriesOf = function () use ($customer, $token, $connection): int {
            // As a request of its own: nothing of what this test wrote is kept in memory.
            Propel::disableInstancePooling();
            $before = $connection->getQueryCount();

            try {
                self::assertSame(200, $this->jsonRequest('GET', '/api/admin/credit_notes?customerId='.$customer->getId(), token: $token, format: 'json')->getStatusCode());
            } finally {
                Propel::enableInstancePooling();
            }

            return $connection->getQueryCount() - $before;
        };

        $this->creditNote($customer, 'accepted');
        $this->creditNote($customer, 'accepted');
        // Once for what the first request of an administrator reads and keeps.
        $queriesOf();
        $forTwo = $queriesOf();

        foreach (range(1, 4) as $ignored) {
            $this->creditNote($customer, 'accepted');
        }

        self::assertSame($forTwo, $queriesOf(), 'the status, the type, the order, the currency and the balance come with the page');
    }

    #[Test]
    public function anAdministratorCreatesACreditNoteFromAnOrderWithItsLinesAndQuantities(): void
    {
        $customer = $this->fixture->customer();
        $order = $this->fixture->paidOrder($customer);
        $line = $order->getOrderProducts()->getFirst();
        $token = $this->authenticateAsAdmin($this->administrator());

        $response = $this->jsonRequest('POST', '/api/admin/credit_notes', [
            'orderId' => $order->getId(),
            'type' => 'back_product',
            'status' => 'accepted',
            'lines' => [['orderProductId' => $line->getId(), 'quantity' => 1]],
            'allowPartialUse' => false,
            'comment' => 'Returned by post',
        ], $token, 'json');

        self::assertSame(201, $response->getStatusCode(), (string) $response->getContent());
        $created = self::decodeJson($response);
        self::assertSame('117.600000', $created['totalPriceWithTax'], 'one unit of the line, at the price the order was invoiced');
        self::assertSame('98.000000', $created['totalPrice']);
        self::assertSame('117.600000', $created['balance']);
        self::assertSame('accepted', $created['status']);
        self::assertFalse($created['allowPartialUse']);
        self::assertNotEmpty($created['ref']);
        self::assertNotEmpty($created['invoiceRef'], 'an accepted credit note is numbered');

        $saved = CreditNoteQuery::create()->findPk($created['id']);
        self::assertSame($customer->getId(), $saved->getCustomerId(), 'the customer is the one of the order');
        self::assertSame($order->getId(), $saved->getOrderId());
        self::assertNotNull($saved->getCreditNoteAddress(), 'with its own copy of the invoice address of the order');
        self::assertSame(1, $saved->getCreditNoteDetails()->count());
        self::assertSame($line->getId(), $saved->getCreditNoteDetails()->getFirst()->getOrderProductId());
        self::assertSame('Returned by post', $saved->getCreditNoteComments()->getFirst()->getComment());

        $log = AdminLogQuery::create()->filterByResource(AdminResources::ORDER)->filterByResourceId($order->getId())->orderById('desc')->findOne();
        self::assertNotNull($log, 'the creation is written in the administration log');
        self::assertStringContainsString((string) $saved->getRef(), (string) $log->getMessage());
    }

    #[Test]
    public function aFullRefundTakesEveryLineOfTheOrderWithoutBeingToldWhich(): void
    {
        $order = $this->fixture->paidOrder($this->fixture->customer());
        $token = $this->authenticateAsAdmin($this->administrator());

        $response = $this->jsonRequest('POST', '/api/admin/credit_notes', ['orderId' => $order->getId(), 'type' => 'order_full_refund'], $token, 'json');

        self::assertSame(201, $response->getStatusCode(), (string) $response->getContent());
        $created = self::decodeJson($response);
        self::assertSame('proposed', $created['status'], 'a draft unless told otherwise');
        self::assertSame(2, $created['lines'][0]['quantity'], 'the whole quantity of the line');
        self::assertSame(\sprintf('%.6F', 235.2 + (float) $order->getPostage() - (float) $order->getDiscount()), $created['totalPriceWithTax']);
    }

    #[Test]
    public function aCreditNoteThatCannotBeMadeOrGoesBeyondItsOrderIsRefusedAndNothingIsWritten(): void
    {
        $customer = $this->fixture->customer();
        $order = $this->fixture->paidOrder($customer);
        $line = $order->getOrderProducts()->getFirst();
        $otherLine = $this->fixture->paidOrder($customer)->getOrderProducts()->getFirst();
        $token = $this->authenticateAsAdmin($this->administrator());
        $before = CreditNoteQuery::create()->count();
        $post = fn (array $payload): int => $this->jsonRequest('POST', '/api/admin/credit_notes', $payload, $token, 'json')->getStatusCode();

        self::assertSame(422, $post(['orderId' => 999999, 'type' => 'back_product', 'lines' => [['orderProductId' => $line->getId(), 'quantity' => 1]]]), 'an order that does not exist');
        self::assertSame(422, $post(['orderId' => $order->getId(), 'type' => 'no_such_type', 'lines' => [['orderProductId' => $line->getId(), 'quantity' => 1]]]), 'a type that does not exist');
        $notItsLine = $this->jsonRequest('POST', '/api/admin/credit_notes', ['orderId' => $order->getId(), 'type' => 'back_product', 'lines' => [['orderProductId' => $otherLine->getId(), 'quantity' => 1]]], $token, 'json');
        self::assertSame(422, $notItsLine->getStatusCode(), 'a line of another order');
        self::assertStringContainsString('is not a line of order', (string) $notItsLine->getContent());

        $tooMany = $this->jsonRequest('POST', '/api/admin/credit_notes', ['orderId' => $order->getId(), 'type' => 'back_product', 'lines' => [['orderProductId' => $line->getId(), 'quantity' => 3]]], $token, 'json');
        self::assertSame(422, $tooMany->getStatusCode(), 'more than the line holds');
        self::assertStringContainsString('holds 2, 3 asked', (string) $tooMany->getContent(), 'refused for the quantity, before the ceiling of the order is even asked');
        self::assertSame(422, $post(['orderId' => $order->getId(), 'type' => 'back_product', 'lines' => []]), 'nothing to refund');
        self::assertSame(422, $post(['orderId' => $order->getId(), 'type' => 'back_product', 'lines' => [['orderProductId' => $line->getId(), 'quantity' => 0]]]), 'a quantity of nothing');
        self::assertSame(422, $post(['orderId' => $order->getId(), 'type' => 'back_product', 'lines' => [['orderProductId' => $line->getId(), 'quantity' => -1]]]), 'a negative quantity');
        self::assertSame(422, $post(['orderId' => $order->getId(), 'type' => 'back_product', 'lines' => [['orderProductId' => $line->getId(), 'quantity' => 1, 'price' => '1.00']]]), 'a field a line does not have');
        $twice = $this->jsonRequest('POST', '/api/admin/credit_notes', ['orderId' => $order->getId(), 'type' => 'back_product', 'lines' => [['orderProductId' => $line->getId(), 'quantity' => 1], ['orderProductId' => $line->getId(), 'quantity' => 1]]], $token, 'json');
        self::assertSame(422, $twice->getStatusCode(), 'the same line twice');
        self::assertStringContainsString('is given twice', (string) $twice->getContent());

        $unpaid = $this->fixture->paidOrder($customer);
        $unpaid->setStatusId(OrderStatusQuery::create()->findOneByCode(OrderStatus::CODE_NOT_PAID)->getId())->save();
        $neverInvoiced = $this->jsonRequest('POST', '/api/admin/credit_notes', ['orderId' => $unpaid->getId(), 'type' => 'order_full_refund'], $token, 'json');
        self::assertSame(422, $neverInvoiced->getStatusCode(), 'an order that was never invoiced');
        self::assertStringContainsString('is not invoiced', (string) $neverInvoiced->getContent());
        self::assertSame(422, $post(['orderId' => $order->getId(), 'type' => 'back_product', 'status' => 'used', 'lines' => [['orderProductId' => $line->getId(), 'quantity' => 1]]]), 'a status a credit note is not created in');

        // The order is worth what its two units cost: a second credit note for both goes beyond it.
        self::assertSame(201, $post(['orderId' => $order->getId(), 'type' => 'back_product', 'lines' => [['orderProductId' => $line->getId(), 'quantity' => 2]]]));
        $response = $this->jsonRequest('POST', '/api/admin/credit_notes', ['orderId' => $order->getId(), 'type' => 'back_product', 'lines' => [['orderProductId' => $line->getId(), 'quantity' => 2]]], $token, 'json');
        self::assertSame(422, $response->getStatusCode(), 'beyond what the order was worth');
        self::assertStringContainsString('already granted', (string) $response->getContent());

        self::assertSame($before + 1, CreditNoteQuery::create()->count(), 'only the one accepted request wrote a credit note');

        // With the ceiling off the amounts are free, the lines are not: the two units are refunded.
        CreditNote::setConfigValue(CreditNote::CONFIG_KEY_ORDER_CEILING, 0);
        ModuleConfigQuery::resetConfigCache();

        try {
            $again = $this->jsonRequest('POST', '/api/admin/credit_notes', ['orderId' => $order->getId(), 'type' => 'back_product', 'lines' => [['orderProductId' => $line->getId(), 'quantity' => 1]]], $token, 'json');
        } finally {
            CreditNote::setConfigValue(CreditNote::CONFIG_KEY_ORDER_CEILING, 1);
        }

        self::assertSame(422, $again->getStatusCode(), 'a line already refunded whole');
        self::assertStringContainsString('holds 2: 2 already refunded, 1 asked', (string) $again->getContent());
    }

    #[Test]
    public function anAdministratorWithoutTheRightOnTheOrdersOrOnTheModuleIsRefused(): void
    {
        $order = $this->fixture->paidOrder($this->fixture->customer());
        $payload = ['orderId' => $order->getId(), 'type' => 'order_full_refund'];

        $noModule = $this->authenticateAsAdmin($this->restrictedAdministrator([AdminResources::ORDER => [AccessManager::VIEW, AccessManager::UPDATE]], []));
        self::assertSame(403, $this->jsonRequest('GET', '/api/admin/credit_notes', token: $noModule)->getStatusCode(), 'the right on the orders without the right on the module');
        self::assertSame(403, $this->jsonRequest('POST', '/api/admin/credit_notes', $payload, $noModule)->getStatusCode());

        $noOrder = $this->authenticateAsAdmin($this->restrictedAdministrator([AdminResources::CUSTOMER => [AccessManager::VIEW]], [AccessManager::VIEW, AccessManager::UPDATE]));
        self::assertSame(403, $this->jsonRequest('GET', '/api/admin/credit_notes', token: $noOrder)->getStatusCode(), 'the right on the module without the right on the orders');

        $readOnly = $this->authenticateAsAdmin($this->restrictedAdministrator([AdminResources::ORDER => [AccessManager::VIEW]], [AccessManager::VIEW]));
        self::assertSame(200, $this->jsonRequest('GET', '/api/admin/credit_notes', token: $readOnly)->getStatusCode(), 'reading needs the two rights to view');
        self::assertSame(403, $this->jsonRequest('POST', '/api/admin/credit_notes', $payload, $readOnly)->getStatusCode(), 'creating needs the two rights to update');

        $writer = $this->authenticateAsAdmin($this->restrictedAdministrator([AdminResources::ORDER => [AccessManager::VIEW, AccessManager::UPDATE]], [AccessManager::VIEW, AccessManager::UPDATE]));
        self::assertSame(201, $this->jsonRequest('POST', '/api/admin/credit_notes', $payload, $writer)->getStatusCode());
    }

    private function creditNote(Customer $customer, string $status, int $quantity = 1, ?Order $order = null): CreditNoteModel
    {
        $creditNote = $this->fixture->creditNote($customer, $order ?? $this->fixture->paidOrder($customer), $status, $quantity);
        $creditNote->save();

        return $creditNote;
    }

    private function administrator(): Admin
    {
        return (new FixtureFactory($this->getPropelConnection()))->admin(['password' => 'password']);
    }

    /**
     * @param array<string, list<string>> $resources accesses by resource code
     * @param list<string>                $module    accesses on the CreditNote module, none when empty
     */
    private function restrictedAdministrator(array $resources, array $module): Admin
    {
        $admin = (new FixtureFactory($this->getPropelConnection()))->restrictedAdmin($resources, ['password' => 'password']);

        if ([] !== $module) {
            $accessManager = new AccessManager(0);
            $accessManager->build($module);

            (new ProfileModule())
                ->setProfileId($admin->getProfileId())
                ->setModuleId(CreditNote::getModuleId())
                ->setAccess($accessManager->getAccessValue())
                ->save($this->getPropelConnection());
        }

        return $admin;
    }

    /**
     * @param list<int> $ids
     *
     * @return list<int>
     */
    private function sorted(array $ids): array
    {
        sort($ids);

        return $ids;
    }
}
