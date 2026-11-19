<?php

declare(strict_types=1);

namespace CreditNote\Service;

use CreditNote\Model\Map\CreditNoteTableMap;
use Propel\Runtime\Propel;

/**
 * Locks the row of an order until the surrounding transaction ends: what is read of its credit
 * notes afterwards is what a credit note written at the same time will see committed.
 */
final class OrderRowLock
{
    public static function take(int $orderId): void
    {
        $statement = Propel::getWriteConnection(CreditNoteTableMap::DATABASE_NAME)
            ->prepare('SELECT `id` FROM `order` WHERE `id` = :id FOR UPDATE');
        $statement->bindValue(':id', $orderId, \PDO::PARAM_INT);
        $statement->execute();
        $statement->fetchColumn();
    }
}
