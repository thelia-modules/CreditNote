<?php

declare(strict_types=1);

namespace CreditNote\Service;

use InvoiceRef\Service\InvoiceRefSequence;
use Propel\Runtime\Connection\ConnectionInterface;
use Thelia\Core\Cache\ConfigCacheService;

/**
 * The accounting number of a credit note when the shop numbers its credit notes like its invoices
 * (`invoice_ref_with_thelia_order`): drawn from the series of the InvoiceRef module, under the same
 * lock as the orders. An invoice and a credit note accepted at the same time never read the same
 * counter value, and a number an order already carries is skipped.
 *
 * Must run inside the transaction that saves the credit note: the lock on the counter lasts until
 * that transaction ends, so no other number is drawn before the credit note carrying this one is
 * written.
 */
final readonly class SharedInvoiceNumber
{
    public function __construct(
        private ?ConfigCacheService $configCache = null,
    ) {
    }

    public function next(ConnectionInterface $connection): string
    {
        if (!class_exists(InvoiceRefSequence::class)) {
            throw new \LogicException('Credit notes numbered like the invoices need the InvoiceRef module 3.1 or later (InvoiceRef\Service\InvoiceRefSequence).');
        }

        $number = (new InvoiceRefSequence($this->configCache))->next($connection);

        // The counter was written in SQL: reload the configuration cache the back-office reads.
        $this->configCache?->initCacheConfigs(true);

        return $number;
    }
}
