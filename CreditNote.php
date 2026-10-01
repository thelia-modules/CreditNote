<?php
/*************************************************************************************/
/*      This file is part of the module CreditNote                                   */
/*                                                                                   */
/*      For the full copyright and license information, please view the LICENSE.txt  */
/*      file that was distributed with this source code.                             */
/*************************************************************************************/

namespace CreditNote;

use Propel\Runtime\Connection\ConnectionInterface;
use Symfony\Component\DependencyInjection\Loader\Configurator\ServicesConfigurator;
use Symfony\Component\Finder\Finder;
use Thelia\Model\ModuleQuery;
use Thelia\Module\BaseModule;
use Thelia\Core\Install\Database;

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
    const CONFIG_KEY_ORDER_CEILING = 'order_ceiling';

    /**
     * @param ConnectionInterface $con
     */
    public function postActivation(?ConnectionInterface $con = null): void
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
            $this->setConfigValue(self::CONFIG_KEY_ORDER_CEILING, 1);
            $this->setConfigValue('is_initialized', true);
        }
    }

    public function update($currentVersion, $newVersion, ?ConnectionInterface $con = null): void
    {
        if (null === self::getConfigValue(self::CONFIG_KEY_INVOICE_REF_WITH_THELIA_ORDER)) {
            self::setConfigValue(
                self::CONFIG_KEY_INVOICE_REF_WITH_THELIA_ORDER,
                0
            );
        }

        if (null === self::getConfigValue(self::CONFIG_KEY_ORDER_CEILING)) {
            self::setConfigValue(self::CONFIG_KEY_ORDER_CEILING, 1);
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

    /**
     * Whether the credit notes of an order are held to the total of the order. On unless the
     * shop turned it off: a shop that grants commercial gestures beyond the order, or refunds
     * the return postage, turns it off.
     */
    public static function isOrderCeilingEnforced(): bool
    {
        return (bool) (int) self::getConfigValue(self::CONFIG_KEY_ORDER_CEILING, 1);
    }

    public static function configureServices(ServicesConfigurator $servicesConfigurator): void
    {
        $servicesConfigurator->load(self::getModuleCode().'\\', __DIR__)
            ->exclude([
                __DIR__ . '/I18n/*',
                __DIR__ . '/Config/**/*.php',
                __DIR__ . '/CreditNote.php',
                // Neither the test suite nor a vendor directory left by a composer install at the
                // module root are services: registering them breaks the boot of a linked checkout.
                __DIR__ . '/tests/*',
                __DIR__ . '/vendor/*',
            ])
            ->autowire(true)
            ->autoconfigure(true);
    }
}
