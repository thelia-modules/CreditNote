<?php

declare(strict_types=1);

namespace CreditNote\Tests\Integration;

use CreditNote\Model\CreditNoteQuery;
use CreditNote\Tests\Support\BackOfficeRouteTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

/**
 * A route that names a credit note answers 404 when it does not exist. Updating an unknown
 * credit note used to write a new one, and opening it used to serve the creation modal.
 */
final class CreditNoteUnknownIdTest extends BackOfficeRouteTestCase
{
    #[Test]
    #[DataProvider('routes')]
    public function anUnknownCreditNoteAnswersNotFoundAndWritesNothing(string $uri, string $action): void
    {
        $customer = $this->fixture->customer();
        $order = $this->fixture->paidOrder($customer);
        $count = CreditNoteQuery::create()->count();
        $unknownId = (int) CreditNoteQuery::create()->orderById('desc')->findOne()?->getId() + 1000;
        $this->loginSuperAdmin();

        $this->client->request('POST', \sprintf($uri, $unknownId), $this->creationOf($order, ['action' => $action]));

        self::assertSame(404, $this->client->getResponse()->getStatusCode());
        self::assertSame($count, CreditNoteQuery::create()->count(), 'no credit note was written');
    }

    /**
     * @return iterable<string, array{string, string}>
     */
    public static function routes(): iterable
    {
        yield 'modal' => ['/admin/credit-note/%d', 'view'];
        yield 'update' => ['/admin/credit-note/%d/_update', 'update'];
        yield 'delete' => ['/admin/credit-note/%d/_delete', 'delete'];
    }
}
