<?php

declare(strict_types=1);

namespace CreditNote\Tests\Support;

use CreditNote\Model\Map\CreditNoteTableMap;
use Propel\Runtime\Propel;
use Psr\Log\AbstractLogger;

/**
 * For a test that must see the SQL a piece of code sends, in the order it sends it: which rows
 * are locked first, whether a sum is a locking read.
 */
trait RecordsQueries
{
    /**
     * @return list<string> the statements executed on the connection of the test while $do ran
     */
    private function queriesOf(callable $do): array
    {
        $container = Propel::getServiceContainer();
        $previous = $container->getLogger(CreditNoteTableMap::DATABASE_NAME);
        $spy = new class extends AbstractLogger {
            /** @var list<string> */
            public array $statements = [];

            public function log($level, string|\Stringable $message, array $context = []): void
            {
                $this->statements[] = (string) $message;
            }
        };

        $container->setLogger(CreditNoteTableMap::DATABASE_NAME, $spy);
        $this->getPropelConnection()->useDebug(true);

        try {
            $do();
        } finally {
            $container->setLogger(CreditNoteTableMap::DATABASE_NAME, $previous);
        }

        return $spy->statements;
    }
}
