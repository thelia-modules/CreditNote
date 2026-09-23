<?php

declare(strict_types=1);

namespace CreditNote\Tests\Integration;

use CreditNote\CreditNote;
use CreditNote\Model\CreditNoteStatusQuery;
use CreditNote\Model\CreditNoteTypeQuery;
use PHPUnit\Framework\Attributes\Test;
use Thelia\Model\ModuleQuery;
use Thelia\Module\BaseModule;
use Thelia\Test\IntegrationTestCase;

/**
 * The module activates and deactivates on a shop without error, seeds its reference data
 * once, and a second activation leaves the data alone (postActivation is idempotent).
 *
 * Prerequisites: a shop test database where the module is registered.
 */
final class ModuleActivationTest extends IntegrationTestCase
{
    private const TABLES = ['credit_note', 'credit_note_address', 'credit_note_detail', 'credit_note_comment', 'credit_note_status', 'credit_note_status_flow', 'credit_note_type', 'order_credit_note', 'cart_credit_note'];

    // The activation commits its own transaction (DDL): no surrounding transaction.
    protected bool $useTransaction = false;

    #[Test]
    public function theModuleActivatesAndDeactivatesAndSeedsItsReferenceDataOnce(): void
    {
        $module = ModuleQuery::create()->findOneByCode(CreditNote::getModuleCode());

        if (null === $module) {
            self::markTestSkipped('The CreditNote module is not registered in the test database.');
        }

        // createInstance() hands back a bare module: activate() reaches for the cache
        // directory, the dispatcher and the kernel through the container, the way
        // Thelia\Action\Module wires it before toggling a module.
        $instance = $module->createInstance();
        $instance->setContainer(static::getContainer());

        if (BaseModule::IS_ACTIVATED === (int) $module->getActivate()) {
            $instance->deActivate($module);
        }

        $instance->activate($module);
        self::assertSame(BaseModule::IS_ACTIVATED, (int) $this->reloadModule()->getActivate());
        $this->assertTablesExist();

        self::assertSame(['proposed', 'refused', 'accepted', 'used'], array_map(static fn ($status): string => $status->getCode(), CreditNoteStatusQuery::create()->orderByPosition()->find()->getData()));
        self::assertSame(['order_full_refund', 'back_product', 'billing_error', 'rebate', 'discount', 'difference_refund'], array_map(static fn ($type): string => $type->getCode(), CreditNoteTypeQuery::create()->orderById()->find()->getData()));
        self::assertSame('CN', CreditNote::getConfigValue(CreditNote::CONFIG_KEY_REF_PREFIX));
        self::assertSame('FA', CreditNote::getConfigValue(CreditNote::CONFIG_KEY_INVOICE_REF_PREFIX));
        self::assertSame('8', CreditNote::getConfigValue(CreditNote::CONFIG_KEY_REF_MIN_LENGTH));

        $statusesAfterFirstActivation = CreditNoteStatusQuery::create()->count();
        $typesAfterFirstActivation = CreditNoteTypeQuery::create()->count();

        $instance->deActivate($this->reloadModule());
        self::assertSame(BaseModule::IS_NOT_ACTIVATED, (int) $this->reloadModule()->getActivate());
        $this->assertTablesExist('the credit note tables survive a deactivation');

        // Second activation: the SQL is not replayed on an initialized shop.
        $instance->activate($this->reloadModule());
        self::assertSame(BaseModule::IS_ACTIVATED, (int) $this->reloadModule()->getActivate());
        self::assertSame($statusesAfterFirstActivation, CreditNoteStatusQuery::create()->count(), 'the second activation seeds no status again');
        self::assertSame($typesAfterFirstActivation, CreditNoteTypeQuery::create()->count(), 'the second activation seeds no type again');
    }

    private function assertTablesExist(string $message = ''): void
    {
        $connection = $this->getPropelConnection();

        foreach (self::TABLES as $table) {
            $statement = $connection->query(\sprintf("SHOW TABLES LIKE '%s'", $table));

            self::assertNotFalse($statement->fetch(), '' !== $message ? $message : \sprintf('table %s is missing', $table));
        }
    }

    private function reloadModule(): \Thelia\Model\Module
    {
        return ModuleQuery::create()->findOneByCode(CreditNote::getModuleCode())
            ?? throw new \RuntimeException('The CreditNote module disappeared from the module table.');
    }
}
