<?php
/*************************************************************************************/
/*      This file is part of the module CreditNote                                   */
/*                                                                                   */
/*      For the full copyright and license information, please view the LICENSE.txt  */
/*      file that was distributed with this source code.                             */
/*************************************************************************************/

namespace CreditNote;

use Propel\Runtime\Connection\ConnectionInterface;
use Symfony\Component\Finder\Finder;
use Thelia\Model\ModuleQuery;
use Thelia\Module\BaseModule;
use Thelia\Install\Database;

/**
 * @author Gilles Bourgeat >gilles.bourgeat@gmail.com>
 */
class CreditNote extends BaseModule
{
    const DOMAIN_MESSAGE = "creditnote";
    const PARSED_DATA = 'parsedData';

    const CONFIG_KEY_REF_PREFIX = 'ref_prefix';
    const CONFIG_KEY_REF_MIN_LENGTH = 'ref_min_length';
    const CONFIG_KEY_REF_INCREMENT = 'ref_increment';
    const CONFIG_KEY_INVOICE_REF_PREFIX = 'invoice_ref_prefix';
    const CONFIG_KEY_INVOICE_REF_MIN_LENGTH = 'invoice_ref_min_length';
    const CONFIG_KEY_INVOICE_REF_INCREMENT = 'invoice_ref_increment';
    const CONFIG_KEY_INVOICE_REF_WITH_THELIA_ORDER = 'invoice_ref_with_thelia_order';

    /**
     * @param ConnectionInterface $con
     */
    public function postActivation(ConnectionInterface $con = null)
    {
        if (!$this->getConfigValue('is_initialized', false)) {
            $database = new Database($con);
            $database->insertSql(null, [__DIR__ . "/Config/thelia.sql", __DIR__ . "/Config/insert.sql"]);
            $this->setConfigValue(self::CONFIG_KEY_REF_INCREMENT, 1);
            $this->setConfigValue(self::CONFIG_KEY_REF_PREFIX, 'CN');
            $this->setConfigValue(self::CONFIG_KEY_REF_MIN_LENGTH, 8);
            $this->setConfigValue(self::CONFIG_KEY_INVOICE_REF_INCREMENT, 1);
            $this->setConfigValue(self::CONFIG_KEY_INVOICE_REF_PREFIX, 'FA');
            $this->setConfigValue(self::CONFIG_KEY_INVOICE_REF_MIN_LENGTH, 8);
            $this->setConfigValue(self::CONFIG_KEY_INVOICE_REF_WITH_THELIA_ORDER, 0);
            $this->setConfigValue('is_initialized', true);
        }
    }

    public function update($currentVersion, $newVersion, ConnectionInterface $con = null)
    {
        if (null === self::getConfigValue(self::CONFIG_KEY_INVOICE_REF_WITH_THELIA_ORDER)) {
            self::setConfigValue(
                self::CONFIG_KEY_INVOICE_REF_WITH_THELIA_ORDER,
                0
            );
        }

        $sqlToExecute = [];
        $finder = new Finder();
        $sort = function (\SplFileInfo $a, \SplFileInfo $b) {
            $a = strtolower(substr($a->getRelativePathname(), 0, -4));
            $b = strtolower(substr($b->getRelativePathname(), 0, -4));
            return version_compare($a, $b);
        };

        $files = $finder->name('*.sql')
            ->in(__DIR__ ."/Config/Update/")
            ->sort($sort);

        foreach ($files as $file) {
            if (version_compare($file->getFilename(), $currentVersion, ">")) {
                $sqlToExecute[$file->getFilename()] = $file->getRealPath();
            }
        }

        $database = new Database($con);

        foreach ($sqlToExecute as $version => $sql) {
            $database->insertSql(null, [$sql]);
        }
    }
}
