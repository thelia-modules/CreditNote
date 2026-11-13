<?php

declare(strict_types=1);

namespace CreditNote\Service;

use CreditNote\CreditNote;
use CreditNote\Model\Map\CreditNoteTableMap;
use Propel\Runtime\Propel;

/**
 * The counters the references and the accounting numbers are drawn from, read under a lock.
 *
 * The value comes from the database, not from the configuration the request already read, and
 * its row stays locked until the transaction that saves the credit note ends: two credit notes
 * written at the same time draw two numbers, the second one after the first is written. Without
 * the lock both read the same value and the database refused the second credit note for its
 * duplicate reference.
 */
final readonly class CreditNoteCounter
{
    public function current(string $counter): int
    {
        $statement = Propel::getWriteConnection(CreditNoteTableMap::DATABASE_NAME)->prepare(
            'SELECT i.`value` FROM `module_config` c'
            .' INNER JOIN `module_config_i18n` i ON i.`id` = c.`id`'
            .' WHERE c.`module_id` = :module AND c.`name` = :name'
            // The row Thelia reads and writes a setting without locale in.
            ." ORDER BY (i.`locale` = 'en_US') DESC, i.`locale` LIMIT 1 FOR UPDATE"
        );
        $statement->bindValue(':module', CreditNote::getModuleId(), \PDO::PARAM_INT);
        $statement->bindValue(':name', $counter);
        $statement->execute();
        $value = $statement->fetchColumn();

        return false === $value || null === $value ? 1 : (int) $value;
    }

    /**
     * Written in SQL first: the configuration object the request holds may already say this
     * value while the database does not, and would then write nothing. Then through the
     * configuration of the module, so what the request reads next is the new value.
     */
    public function moveTo(string $counter, int $value): void
    {
        $statement = Propel::getWriteConnection(CreditNoteTableMap::DATABASE_NAME)->prepare(
            'UPDATE `module_config_i18n` i'
            .' INNER JOIN `module_config` c ON c.`id` = i.`id`'
            .' SET i.`value` = :value'
            .' WHERE c.`module_id` = :module AND c.`name` = :name'
        );
        $statement->bindValue(':value', (string) $value);
        $statement->bindValue(':module', CreditNote::getModuleId(), \PDO::PARAM_INT);
        $statement->bindValue(':name', $counter);
        $statement->execute();

        CreditNote::setConfigValue($counter, $value);
    }
}
