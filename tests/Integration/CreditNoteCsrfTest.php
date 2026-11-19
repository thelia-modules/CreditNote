<?php

declare(strict_types=1);

namespace CreditNote\Tests\Integration;

use CreditNote\CreditNote;
use CreditNote\Model\CreditNoteQuery;
use CreditNote\Service\CreditNoteCounter;
use CreditNote\Tests\Support\BackOfficeRouteTestCase;
use PHPUnit\Framework\Attributes\Test;
use Thelia\Model\ModuleConfigQuery;

/**
 * The routes that write a credit note take the token the pages of the module carry: a page of
 * another site cannot make the browser of a signed-in administrator create, change or delete a
 * credit note, which a customer could then pay an order with.
 */
final class CreditNoteCsrfTest extends BackOfficeRouteTestCase
{
    #[Test]
    public function aCreditNoteIsNotCreatedChangedOrDeletedWithoutTheTokenOfThePage(): void
    {
        $customer = $this->fixture->customer();
        $order = $this->fixture->paidOrder($customer);
        $existing = $this->fixture->creditNote($customer, $this->fixture->paidOrder($customer));
        $existing->save();
        $this->loginSuperAdmin();
        $withToken = $this->creationOf($order);
        $fields = $withToken['credit_note_create'];

        foreach ([[], ['_token' => 'forged']] as $forged) {
            $this->client->request('POST', '/admin/credit-note/create', $forged + ['credit_note_create' => $fields]);
            self::assertSame(403, $this->client->getResponse()->getStatusCode());

            $this->client->request('POST', \sprintf('/admin/credit-note/%d/_update', $existing->getId()), $forged + ['credit_note_create' => ['action' => 'update'] + $fields]);
            self::assertSame(403, $this->client->getResponse()->getStatusCode());

            $this->client->request('POST', \sprintf('/admin/credit-note/%d/_delete', $existing->getId()), $forged);
            self::assertSame(403, $this->client->getResponse()->getStatusCode());
        }

        self::assertSame(0, CreditNoteQuery::create()->filterByOrderId($order->getId())->count(), 'nothing is created');
        self::assertNotNull(CreditNoteQuery::create()->findPk($existing->getId()), 'nothing is deleted');

        $this->client->request('POST', '/admin/credit-note/create', $withToken);
        self::assertTrue($this->client->getResponse()->isRedirection(), 'the form of the module still creates');
        self::assertSame(1, CreditNoteQuery::create()->filterByOrderId($order->getId())->count());
    }

    #[Test]
    public function theModalAndTheDeleteFormCarryTheToken(): void
    {
        $order = $this->fixture->paidOrder($this->fixture->customer());
        $this->loginSuperAdmin();

        $this->client->request('POST', '/admin/credit-note/ajax/modal/create', ['credit_note_create' => ['action' => 'view', 'order_id' => $order->getId()]]);
        self::assertMatchesRegularExpression('/name="_token" value="[^"]{20,}"/', (string) $this->client->getResponse()->getContent(), 'the modal opens without a token and carries one');

        $this->client->request('GET', '/admin/credit-note');
        self::assertMatchesRegularExpression('/id="credit-note-delete-form">\s*<input type="hidden" name="_token" value="[^"]{20,}"/', (string) $this->client->getResponse()->getContent());
    }

    #[Test]
    public function theConfigurationScreenMovesACounterForwardAndNeverBack(): void
    {
        $counter = $this->getService(CreditNoteCounter::class);
        $this->loginSuperAdmin();
        $submit = function (int $ref) {
            $crawler = $this->client->request('GET', '/admin/module/CreditNote');
            $form = $crawler->filterXPath('//input[contains(@name, "[ref_increment]")]/ancestor::form')->form();
            $field = $crawler->filterXPath('//input[contains(@name, "[ref_increment]")]')->attr('name');
            $form[$field] = (string) $ref;

            return $this->requestAndReadMessages($form->getMethod(), $form->getUri(), $form->getPhpValues());
        };

        // The page was opened with the counter at its value; ten credit notes are numbered since.
        $shown = $counter->current(CreditNote::CONFIG_KEY_REF_INCREMENT);
        $counter->moveTo(CreditNote::CONFIG_KEY_REF_INCREMENT, $shown + 10);

        $warnings = $submit($shown);

        self::assertSame($shown + 10, $counter->current(CreditNote::CONFIG_KEY_REF_INCREMENT), 'the numbers drawn meanwhile are not given again');
        self::assertCount(1, $warnings);
        self::assertStringContainsString('A counter was left as it is', $warnings[0]);

        self::assertSame([], $submit($shown + 50));
        self::assertSame($shown + 50, $counter->current(CreditNote::CONFIG_KEY_REF_INCREMENT), 'the merchant may still jump ahead');
    }

    #[Test]
    public function theBackOfficeSaysWhichLineOfTheOrderIsAlreadyRefunded(): void
    {
        $customer = $this->fixture->customer();
        $order = $this->fixture->paidOrder($customer);
        $this->fixture->creditNote($customer, $order, 'accepted', 2)->save();
        $this->loginSuperAdmin();
        CreditNote::setConfigValue(CreditNote::CONFIG_KEY_ORDER_CEILING, 0);
        ModuleConfigQuery::resetConfigCache();

        try {
            $errors = $this->requestAndReadErrors('POST', '/admin/credit-note/create', $this->creationOf($order));
        } finally {
            CreditNote::setConfigValue(CreditNote::CONFIG_KEY_ORDER_CEILING, 1);
        }

        self::assertCount(1, $errors);
        self::assertStringContainsString('The line "Violet" of order '.$order->getRef().' holds 2: 2 already refunded by its credit notes, 1 asked.', $errors[0]);
        self::assertSame(1, CreditNoteQuery::create()->filterByOrderId($order->getId())->count());
    }
}
