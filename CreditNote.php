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
use CreditNote\Service\CreditNotePaymentPart;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\Response;
use Thelia\Core\Install\Database;
use Thelia\Model\Cart;
use Thelia\Model\Order;
use Thelia\Model\OrderPaymentPart;
use Thelia\Module\AbstractPaymentModule;
use Thelia\Module\InstantPaymentModuleInterface;

/**
 * A payment module that settles part of an order inside the shop: the credit notes the buyer
 * put on the cart pay their share when the order is paid, the provider chosen charges the rest
 * (Thelia\Module\InstantPaymentModuleInterface, Thelia 3.3).
 *
 * @author Gilles Bourgeat >gilles.bourgeat@gmail.com>
 */
class CreditNote extends AbstractPaymentModule implements InstantPaymentModuleInterface
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
    const CONFIG_KEY_DOCUMENT_LOCALES = 'document_locales';

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

        // The scripts of the versions this update crosses only: above the version installed, up
        // to the one installed now. A script above the target describes a schema the shop does
        // not have yet, or already has when it was installed at that version.
        foreach ($files as $file) {
            $version = substr($file->getFilename(), 0, -4);

            if (version_compare($version, (string) $currentVersion, '>') && version_compare($version, (string) $newVersion, '<=')) {
                $sqlToExecute[$file->getFilename()] = $file->getRealPath();
            }
        }

        $database = new Database($con);

        foreach ($sqlToExecute as $version => $sql) {
            $database->insertSql(null, [$sql]);
        }
    }

    public function getAmountAppliedToCart(Cart $cart): string
    {
        return $this->paymentPart()->amountAppliedTo($cart);
    }

    public function settlePart(OrderPaymentPart $part): void
    {
        $this->paymentPart()->settle($part);
    }

    public function releasePart(OrderPaymentPart $part): void
    {
        $this->paymentPart()->release($part);
    }

    /**
     * Nothing to ask a provider: the core settles the part before it gets here, and does not
     * call this at all for an order the credit notes cover whole.
     */
    public function pay(Order $order): ?Response
    {
        return null;
    }

    public function isValidPayment(): bool
    {
        return true;
    }

    /**
     * An unpaid order may be presented to the payment again: the amount applied is read from
     * the reservations of the cart, which the settlement leaves as they are.
     */
    public function supportsPaymentRetry(): bool
    {
        return true;
    }

    private function paymentPart(): CreditNotePaymentPart
    {
        return $this->getContainer()->get(CreditNotePaymentPart::class);
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

    /**
     * The languages a credit note may be printed in, as the shop maintains its PDF template: the
     * `document_locales` setting (locales separated by commas), English and French when empty.
     *
     * @return list<string>
     */
    public static function documentLocales(): array
    {
        $locales = array_values(array_filter(array_map('trim', explode(',', (string) self::getConfigValue(self::CONFIG_KEY_DOCUMENT_LOCALES, '')))));

        return [] === $locales ? \CreditNote\Service\CreditNoteDocumentLocale::PREFERRED : $locales;
    }

    public static function getCompilers(): array
    {
        return [new \CreditNote\DependencyInjection\AdminApiResourcePass()];
    }

    public static function configureServices(ServicesConfigurator $servicesConfigurator): void
    {
        $excluded = [
            __DIR__ . '/I18n/*',
            __DIR__ . '/Config/**/*.php',
            __DIR__ . '/CreditNote.php',
            // Neither the test suite nor a vendor directory left by a composer install at the
            // module root are services: registering them breaks the boot of a linked checkout.
            __DIR__ . '/tests/*',
            __DIR__ . '/vendor/*',
            // Neither services: the models, the API resource and its input, the compiler pass.
            __DIR__ . '/Model/*',
            __DIR__ . '/Api/Resource/*',
            __DIR__ . '/Api/Dto/*',
            __DIR__ . '/DependencyInjection/*',
            __DIR__ . '/Exception/*',
        ];

        // The choice of the credit notes on the payment step is a live component, as the theme
        // Flexy expects it. A shop without Symfony UX Live Component has no such step: these
        // classes are left out, and the rest of the module works as before.
        if (!trait_exists(\Symfony\UX\LiveComponent\DefaultActionTrait::class)) {
            $excluded[] = __DIR__ . '/LiveComponent/*';
            $excluded[] = __DIR__ . '/Hook/Front/CheckoutPaymentPartsHook.php';
        }

        $servicesConfigurator->load(self::getModuleCode().'\\', __DIR__)
            ->exclude($excluded)
            ->autowire(true)
            ->autoconfigure(true);
    }
}
