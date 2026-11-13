<?php

declare(strict_types=1);

namespace CreditNote\Tests\Integration;

use CreditNote\Model\CreditNoteCommentQuery;
use CreditNote\Model\CreditNoteQuery;
use CreditNote\Tests\Support\BackOfficeRouteTestCase;
use PHPUnit\Framework\Attributes\Test;

/**
 * The comment of the modal is optional: left empty, nothing is written and nothing is
 * deprecated; typed in, it is kept without the spaces around it.
 */
final class CreditNoteCommentTest extends BackOfficeRouteTestCase
{
    #[Test]
    public function anEmptyCommentWritesNoComment(): void
    {
        $customer = $this->fixture->customer();
        $order = $this->fixture->paidOrder($customer);
        $this->loginSuperAdmin();

        // PHP 8.5 deprecates a null given to trim(): the deprecations of the controller are
        // collected while the request runs.
        $deprecations = [];
        set_error_handler(static function (int $level, string $message, string $file) use (&$deprecations): bool {
            if (str_ends_with($file, 'CreditNoteController.php')) {
                $deprecations[] = $message;
            }

            return true;
        }, \E_DEPRECATED);

        try {
            $this->client->request('POST', '/admin/credit-note/create', $this->creationOf($order));
        } finally {
            restore_error_handler();
        }

        $creditNote = CreditNoteQuery::create()->filterByOrderId($order->getId())->findOne();
        self::assertNotNull($creditNote);
        self::assertSame([], $deprecations);
        self::assertSame(0, CreditNoteCommentQuery::create()->filterByCreditNoteId($creditNote->getId())->count());

        $this->client->request('POST', \sprintf('/admin/credit-note/%d/_update', $creditNote->getId()), $this->creationOf($order, [
            'action' => 'update',
            'comment' => '  Returned damaged  ',
        ]));
        $comment = CreditNoteCommentQuery::create()->filterByCreditNoteId($creditNote->getId())->findOne();
        self::assertSame('Returned damaged', $comment?->getComment());
    }
}
