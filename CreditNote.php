<?php

namespace CreditNote;

use Propel\Runtime\Connection\ConnectionInterface;
use Thelia\Core\Translation\Translator;
use Thelia\Install\Database;
use Thelia\Model\Lang;
use Thelia\Model\LangQuery;
use Thelia\Model\Message;
use Thelia\Model\MessageQuery;
use Thelia\Module\BaseModule;

/**
 * CreditNote module class.
 */
class CreditNote extends BaseModule
{
    const MESSAGE_DOMAIN = "creditnote";
    const MESSAGE_DOMAIN_BO = "creditnote.bo.default";
    const MESSAGE_DOMAIN_FO = "creditnote.fo.default";
    const MESSAGE_DOMAIN_EMAIL = "creditnote.email.default";

    const ROUTER = "router.creditnote";

    /**
     * Configuration key for the generated coupons validity period, in days.
     * @var string
     */
    const CONF_KEY_COUPON_CODE_DURATION = "coupon_code_duration";
    /**
     * Default validity period for the generated coupons, in days.
     * @var int
     */
    const DEFAULT_COUPON_CODE_DURATION = 365;

    /**
     * Configuration key for the generated coupon codes prefix.
     * @var string
     */
    const CONF_KEY_COUPON_CODE_PREFIX = "coupon_code_prefix";
    /**
     * Default code prefix for the generated coupons.
     * @var string
     */
    const DEFAULT_COUPON_CODE_PREFIX = "CREDIT";

    /**
     * Configuration key for the flag restricting coupon codes from order credit notes to the original order customer.
     * @var string
     */
    const CONF_KEY_COUPON_CODE_RESTRICTED_TO_ORIGINAL_CUSTOMER = "coupon_code_restricted_to_original_customer";
    /**
     * Whether coupon codes from order credit notes should be restricted to the original order customer by default.
     * @var boolean
     */
    const DEFAULT_COUPON_CODE_RESTRICTED_TO_ORIGINAL_CUSTOMER = true;

    /**
     * Message code for the new order credit note notification to customer. Also the base name of default templates.
     * @var string
     */
    const MESSAGE_NAME_ORDER_CREDIT_NOTE_CREATED_NOTIFY_CUSTOMER = "order_credit_note_created_notify_customer";

    public function postActivation(ConnectionInterface $con = null)
    {
        $database = new Database($con);

        $database->insertSql(null, [__DIR__ . "/Config/create.sql", __DIR__ . "/Config/insert.sql"]);

        // set default configuration values
        static::setDefaultConfigValue(
            static::CONF_KEY_COUPON_CODE_DURATION,
            static::DEFAULT_COUPON_CODE_DURATION
        );
        static::setDefaultConfigValue(
            static::CONF_KEY_COUPON_CODE_PREFIX,
            static::DEFAULT_COUPON_CODE_PREFIX
        );
        static::setDefaultConfigValue(
            static::CONF_KEY_COUPON_CODE_RESTRICTED_TO_ORIGINAL_CUSTOMER,
            static::DEFAULT_COUPON_CODE_RESTRICTED_TO_ORIGINAL_CUSTOMER
        );

        // create mail templates
        $langages = LangQuery::create()->find($con);
        /** @var Lang $langage */
        foreach ($langages as $langage) {
            static::addTranslationRessources($langage->getLocale());
        }

        if (null ===
            MessageQuery::create()->findOneByName(static::MESSAGE_NAME_ORDER_CREDIT_NOTE_CREATED_NOTIFY_CUSTOMER)
        ) {
            $message = (new Message())
                ->setName(static::MESSAGE_NAME_ORDER_CREDIT_NOTE_CREATED_NOTIFY_CUSTOMER)
                ->setTextLayoutFileName("default-text-layout.tpl")
                ->setTextTemplateFileName(static::MESSAGE_NAME_ORDER_CREDIT_NOTE_CREATED_NOTIFY_CUSTOMER . ".txt")
                ->setHtmlLayoutFileName("default-html-layout.tpl")
                ->setHtmlTemplateFileName(static::MESSAGE_NAME_ORDER_CREDIT_NOTE_CREATED_NOTIFY_CUSTOMER . ".html");

            /** @var Lang $langage */
            foreach ($langages as $langage) {
                $message
                    ->setLocale($langage->getLocale())
                    ->setTitle(
                        Translator::getInstance()->trans(
                            "Message sent to the customer when a credit note is created from an order.",
                            [],
                            static::MESSAGE_DOMAIN_BO,
                            $langage->getLocale()
                        )
                    )
                    ->setSubject(
                        str_replace(
                            "%order_ref",
                            "{\$order_ref}",
                            Translator::getInstance()->trans(
                                "Credit note on your order %order_ref",
                                [],
                                static::MESSAGE_DOMAIN_EMAIL,
                                $langage->getLocale()
                            )
                        )
                    );
            }

            $message->save($con);
        }
    }

    /**
     * Set a configuration value of the module to a default if it is not set yet.
     * @param string $configKey Configuration key.
     * @param string $defaultValue Default configuration value.
     */
    protected static function setDefaultConfigValue($configKey, $defaultValue)
    {
        if (null === static::getConfigValue($configKey)) {
            static::setConfigValue($configKey, $defaultValue);
        }
    }

    /**
     * Add back-office and email translation resources for this module into the translator.
     * @param string $locale Locale
     */
    protected static function addTranslationRessources($locale)
    {
        // back-office
        Translator::getInstance()->addResource(
            "php",
            __DIR__ . "/I18n/backOffice/default/" . $locale . ".php",
            $locale,
            static::MESSAGE_DOMAIN_BO
        );

        // email
        Translator::getInstance()->addResource(
            "php",
            __DIR__ . "/I18n/email/default/" . $locale . ".php",
            $locale,
            static::MESSAGE_DOMAIN_EMAIL
        );
    }
}
