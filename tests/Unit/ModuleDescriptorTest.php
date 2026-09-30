<?php

declare(strict_types=1);

namespace CreditNote\Tests\Unit;

use CreditNote\CreditNote;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Thelia\Core\Template\Element\BaseLoop;

/**
 * The descriptors of the module agree with its code: the declared class, the loops, the
 * schema constraints the Thelia module conventions require.
 */
final class ModuleDescriptorTest extends TestCase
{
    #[Test]
    public function theModuleDescriptorNamesTheModuleClass(): void
    {
        $descriptor = simplexml_load_file(self::root() . '/Config/module.xml');
        self::assertNotFalse($descriptor);

        self::assertSame(CreditNote::class, (string) $descriptor->fullnamespace);
        self::assertMatchesRegularExpression('/^\d+\.\d+\.\d+$/', (string) $descriptor->version);
        self::assertContains((string) $descriptor->stability, ['alpha', 'beta', 'rc', 'prod', 'other']);

        $languages = array_map('strval', iterator_to_array($descriptor->languages->language, false));
        foreach ($languages as $locale) {
            self::assertFileExists(self::root() . '/I18n/' . $locale . '.php', \sprintf('the %s language declared by module.xml has a catalog', $locale));
        }
    }

    #[Test]
    public function everyDeclaredLoopIsALoopClassOfTheModule(): void
    {
        $config = simplexml_load_file(self::root() . '/Config/config.xml');
        self::assertNotFalse($config);

        $loops = [];
        foreach ($config->loops->loop as $loop) {
            $class = (string) $loop['class'];
            $loops[(string) $loop['name']] = $class;

            self::assertTrue(class_exists($class), \sprintf('the loop class %s exists', $class));
            self::assertTrue(is_subclass_of($class, BaseLoop::class), \sprintf('%s extends BaseLoop', $class));
        }

        self::assertSame(
            ['credit-note', 'credit-note-address', 'credit-note-comment', 'credit-note-detail', 'credit-note-status', 'credit-note-type', 'credit-note-version', 'order-credit-note'],
            array_keys(array_change_key_case($this->sorted($loops))),
        );
    }

    #[Test]
    public function everyForeignKeyOfTheSchemaSaysWhatADeletionDoes(): void
    {
        $schema = simplexml_load_file(self::root() . '/Config/schema.xml');
        self::assertNotFalse($schema);

        $withoutRule = [];
        foreach ($schema->table as $table) {
            foreach ($table->{'foreign-key'} as $foreignKey) {
                if ('' === (string) $foreignKey['onDelete']) {
                    $withoutRule[] = \sprintf('%s -> %s', (string) $table['name'], (string) $foreignKey['foreignTable']);
                }
            }
        }

        self::assertSame([], $withoutRule, 'foreign keys without an onDelete rule');
    }

    #[Test]
    public function theModuleTablesAreTheOnesTheSchemaDescribes(): void
    {
        $schema = simplexml_load_file(self::root() . '/Config/schema.xml');
        self::assertNotFalse($schema);

        $tables = array_map(static fn (\SimpleXMLElement $table): string => (string) $table['name'], iterator_to_array($schema->table, false));
        sort($tables);

        self::assertSame(
            ['cart_credit_note', 'credit_note', 'credit_note_address', 'credit_note_comment', 'credit_note_detail', 'credit_note_status', 'credit_note_status_flow', 'credit_note_type', 'order_credit_note'],
            $tables,
        );

        $sql = (string) file_get_contents(self::root() . '/Config/thelia.sql');
        foreach ($tables as $table) {
            self::assertStringContainsString(\sprintf('CREATE TABLE `%s`', $table), $sql, \sprintf('thelia.sql creates %s', $table));
        }
    }

    /**
     * @param array<string, string> $loops
     *
     * @return array<string, string>
     */
    private function sorted(array $loops): array
    {
        ksort($loops);

        return $loops;
    }

    private static function root(): string
    {
        return \dirname(__DIR__, 2);
    }
}
