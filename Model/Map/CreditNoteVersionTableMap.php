<?php

namespace CreditNote\Model\Map;

use CreditNote\Model\CreditNoteVersion;
use CreditNote\Model\CreditNoteVersionQuery;
use Propel\Runtime\Propel;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\InstancePoolTrait;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\DataFetcher\DataFetcherInterface;
use Propel\Runtime\Exception\PropelException;
use Propel\Runtime\Map\RelationMap;
use Propel\Runtime\Map\TableMap;
use Propel\Runtime\Map\TableMapTrait;


/**
 * This class defines the structure of the 'credit_note_version' table.
 *
 *
 *
 * This map class is used by Propel to do runtime db structure discovery.
 * For example, the createSelectSql() method checks the type of a given column used in an
 * ORDER BY clause to know whether it needs to apply SQL to make the ORDER BY case-insensitive
 * (i.e. if it's a text column type).
 *
 */
class CreditNoteVersionTableMap extends TableMap
{
    use InstancePoolTrait;
    use TableMapTrait;

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'CreditNote.Model.Map.CreditNoteVersionTableMap';

    /**
     * The default database name for this class
     */
    const DATABASE_NAME = 'thelia';

    /**
     * The table name for this class
     */
    const TABLE_NAME = 'credit_note_version';

    /**
     * The related Propel class for this table
     */
    const OM_CLASS = '\\CreditNote\\Model\\CreditNoteVersion';

    /**
     * A class that can be returned by this tableMap
     */
    const CLASS_DEFAULT = 'CreditNote.Model.CreditNoteVersion';

    /**
     * The total number of columns
     */
    const NUM_COLUMNS = 27;

    /**
     * The number of lazy-loaded columns
     */
    const NUM_LAZY_LOAD_COLUMNS = 0;

    /**
     * The number of columns to hydrate (NUM_COLUMNS - NUM_LAZY_LOAD_COLUMNS)
     */
    const NUM_HYDRATE_COLUMNS = 27;

    /**
    * the column legacy name for the id field
    * @deprecated Legacy constant for compatibility. Use COL_ID.
    */
    const ID = 'credit_note_version.id';
    /**
    * the column legacy name for the ref field
    * @deprecated Legacy constant for compatibility. Use COL_REF.
    */
    const REF = 'credit_note_version.ref';
    /**
    * the column legacy name for the invoice_ref field
    * @deprecated Legacy constant for compatibility. Use COL_INVOICE_REF.
    */
    const INVOICE_REF = 'credit_note_version.invoice_ref';
    /**
    * the column legacy name for the invoice_address_id field
    * @deprecated Legacy constant for compatibility. Use COL_INVOICE_ADDRESS_ID.
    */
    const INVOICE_ADDRESS_ID = 'credit_note_version.invoice_address_id';
    /**
    * the column legacy name for the invoice_date field
    * @deprecated Legacy constant for compatibility. Use COL_INVOICE_DATE.
    */
    const INVOICE_DATE = 'credit_note_version.invoice_date';
    /**
    * the column legacy name for the order_id field
    * @deprecated Legacy constant for compatibility. Use COL_ORDER_ID.
    */
    const ORDER_ID = 'credit_note_version.order_id';
    /**
    * the column legacy name for the customer_id field
    * @deprecated Legacy constant for compatibility. Use COL_CUSTOMER_ID.
    */
    const CUSTOMER_ID = 'credit_note_version.customer_id';
    /**
    * the column legacy name for the parent_id field
    * @deprecated Legacy constant for compatibility. Use COL_PARENT_ID.
    */
    const PARENT_ID = 'credit_note_version.parent_id';
    /**
    * the column legacy name for the type_id field
    * @deprecated Legacy constant for compatibility. Use COL_TYPE_ID.
    */
    const TYPE_ID = 'credit_note_version.type_id';
    /**
    * the column legacy name for the status_id field
    * @deprecated Legacy constant for compatibility. Use COL_STATUS_ID.
    */
    const STATUS_ID = 'credit_note_version.status_id';
    /**
    * the column legacy name for the currency_id field
    * @deprecated Legacy constant for compatibility. Use COL_CURRENCY_ID.
    */
    const CURRENCY_ID = 'credit_note_version.currency_id';
    /**
    * the column legacy name for the currency_rate field
    * @deprecated Legacy constant for compatibility. Use COL_CURRENCY_RATE.
    */
    const CURRENCY_RATE = 'credit_note_version.currency_rate';
    /**
    * the column legacy name for the total_price field
    * @deprecated Legacy constant for compatibility. Use COL_TOTAL_PRICE.
    */
    const TOTAL_PRICE = 'credit_note_version.total_price';
    /**
    * the column legacy name for the total_price_with_tax field
    * @deprecated Legacy constant for compatibility. Use COL_TOTAL_PRICE_WITH_TAX.
    */
    const TOTAL_PRICE_WITH_TAX = 'credit_note_version.total_price_with_tax';
    /**
    * the column legacy name for the discount_without_tax field
    * @deprecated Legacy constant for compatibility. Use COL_DISCOUNT_WITHOUT_TAX.
    */
    const DISCOUNT_WITHOUT_TAX = 'credit_note_version.discount_without_tax';
    /**
    * the column legacy name for the discount_with_tax field
    * @deprecated Legacy constant for compatibility. Use COL_DISCOUNT_WITH_TAX.
    */
    const DISCOUNT_WITH_TAX = 'credit_note_version.discount_with_tax';
    /**
    * the column legacy name for the allow_partial_use field
    * @deprecated Legacy constant for compatibility. Use COL_ALLOW_PARTIAL_USE.
    */
    const ALLOW_PARTIAL_USE = 'credit_note_version.allow_partial_use';
    /**
    * the column legacy name for the created_at field
    * @deprecated Legacy constant for compatibility. Use COL_CREATED_AT.
    */
    const CREATED_AT = 'credit_note_version.created_at';
    /**
    * the column legacy name for the updated_at field
    * @deprecated Legacy constant for compatibility. Use COL_UPDATED_AT.
    */
    const UPDATED_AT = 'credit_note_version.updated_at';
    /**
    * the column legacy name for the version field
    * @deprecated Legacy constant for compatibility. Use COL_VERSION.
    */
    const VERSION = 'credit_note_version.version';
    /**
    * the column legacy name for the version_created_at field
    * @deprecated Legacy constant for compatibility. Use COL_VERSION_CREATED_AT.
    */
    const VERSION_CREATED_AT = 'credit_note_version.version_created_at';
    /**
    * the column legacy name for the version_created_by field
    * @deprecated Legacy constant for compatibility. Use COL_VERSION_CREATED_BY.
    */
    const VERSION_CREATED_BY = 'credit_note_version.version_created_by';
    /**
    * the column legacy name for the order_id_version field
    * @deprecated Legacy constant for compatibility. Use COL_ORDER_ID_VERSION.
    */
    const ORDER_ID_VERSION = 'credit_note_version.order_id_version';
    /**
    * the column legacy name for the customer_id_version field
    * @deprecated Legacy constant for compatibility. Use COL_CUSTOMER_ID_VERSION.
    */
    const CUSTOMER_ID_VERSION = 'credit_note_version.customer_id_version';
    /**
    * the column legacy name for the parent_id_version field
    * @deprecated Legacy constant for compatibility. Use COL_PARENT_ID_VERSION.
    */
    const PARENT_ID_VERSION = 'credit_note_version.parent_id_version';
    /**
    * the column legacy name for the credit_note_ids field
    * @deprecated Legacy constant for compatibility. Use COL_CREDIT_NOTE_IDS.
    */
    const CREDIT_NOTE_IDS = 'credit_note_version.credit_note_ids';
    /**
    * the column legacy name for the credit_note_versions field
    * @deprecated Legacy constant for compatibility. Use COL_CREDIT_NOTE_VERSIONS.
    */
    const CREDIT_NOTE_VERSIONS = 'credit_note_version.credit_note_versions';
    /**
     * the column name for the id field
     */
    const COL_ID = 'credit_note_version.id';
    /**
     * the column name for the ref field
     */
    const COL_REF = 'credit_note_version.ref';
    /**
     * the column name for the invoice_ref field
     */
    const COL_INVOICE_REF = 'credit_note_version.invoice_ref';
    /**
     * the column name for the invoice_address_id field
     */
    const COL_INVOICE_ADDRESS_ID = 'credit_note_version.invoice_address_id';
    /**
     * the column name for the invoice_date field
     */
    const COL_INVOICE_DATE = 'credit_note_version.invoice_date';
    /**
     * the column name for the order_id field
     */
    const COL_ORDER_ID = 'credit_note_version.order_id';
    /**
     * the column name for the customer_id field
     */
    const COL_CUSTOMER_ID = 'credit_note_version.customer_id';
    /**
     * the column name for the parent_id field
     */
    const COL_PARENT_ID = 'credit_note_version.parent_id';
    /**
     * the column name for the type_id field
     */
    const COL_TYPE_ID = 'credit_note_version.type_id';
    /**
     * the column name for the status_id field
     */
    const COL_STATUS_ID = 'credit_note_version.status_id';
    /**
     * the column name for the currency_id field
     */
    const COL_CURRENCY_ID = 'credit_note_version.currency_id';
    /**
     * the column name for the currency_rate field
     */
    const COL_CURRENCY_RATE = 'credit_note_version.currency_rate';
    /**
     * the column name for the total_price field
     */
    const COL_TOTAL_PRICE = 'credit_note_version.total_price';
    /**
     * the column name for the total_price_with_tax field
     */
    const COL_TOTAL_PRICE_WITH_TAX = 'credit_note_version.total_price_with_tax';
    /**
     * the column name for the discount_without_tax field
     */
    const COL_DISCOUNT_WITHOUT_TAX = 'credit_note_version.discount_without_tax';
    /**
     * the column name for the discount_with_tax field
     */
    const COL_DISCOUNT_WITH_TAX = 'credit_note_version.discount_with_tax';
    /**
     * the column name for the allow_partial_use field
     */
    const COL_ALLOW_PARTIAL_USE = 'credit_note_version.allow_partial_use';
    /**
     * the column name for the created_at field
     */
    const COL_CREATED_AT = 'credit_note_version.created_at';
    /**
     * the column name for the updated_at field
     */
    const COL_UPDATED_AT = 'credit_note_version.updated_at';
    /**
     * the column name for the version field
     */
    const COL_VERSION = 'credit_note_version.version';
    /**
     * the column name for the version_created_at field
     */
    const COL_VERSION_CREATED_AT = 'credit_note_version.version_created_at';
    /**
     * the column name for the version_created_by field
     */
    const COL_VERSION_CREATED_BY = 'credit_note_version.version_created_by';
    /**
     * the column name for the order_id_version field
     */
    const COL_ORDER_ID_VERSION = 'credit_note_version.order_id_version';
    /**
     * the column name for the customer_id_version field
     */
    const COL_CUSTOMER_ID_VERSION = 'credit_note_version.customer_id_version';
    /**
     * the column name for the parent_id_version field
     */
    const COL_PARENT_ID_VERSION = 'credit_note_version.parent_id_version';
    /**
     * the column name for the credit_note_ids field
     */
    const COL_CREDIT_NOTE_IDS = 'credit_note_version.credit_note_ids';
    /**
     * the column name for the credit_note_versions field
     */
    const COL_CREDIT_NOTE_VERSIONS = 'credit_note_version.credit_note_versions';

    /**
     * The default string format for model objects of the related table
     */
    const DEFAULT_STRING_FORMAT = 'YAML';

    /**
     * holds an array of fieldnames
     *
     * first dimension keys are the type constants
     * e.g. self::$fieldNames[self::TYPE_PHPNAME][0] = 'Id'
     */
    protected static $fieldNames = array (
        self::TYPE_PHPNAME       => array('Id', 'Ref', 'InvoiceRef', 'InvoiceAddressId', 'InvoiceDate', 'OrderId', 'CustomerId', 'ParentId', 'TypeId', 'StatusId', 'CurrencyId', 'CurrencyRate', 'TotalPrice', 'TotalPriceWithTax', 'DiscountWithoutTax', 'DiscountWithTax', 'AllowPartialUse', 'CreatedAt', 'UpdatedAt', 'Version', 'VersionCreatedAt', 'VersionCreatedBy', 'OrderIdVersion', 'CustomerIdVersion', 'ParentIdVersion', 'CreditNoteIds', 'CreditNoteVersions', ),
        self::TYPE_CAMELNAME     => array('id', 'ref', 'invoiceRef', 'invoiceAddressId', 'invoiceDate', 'orderId', 'customerId', 'parentId', 'typeId', 'statusId', 'currencyId', 'currencyRate', 'totalPrice', 'totalPriceWithTax', 'discountWithoutTax', 'discountWithTax', 'allowPartialUse', 'createdAt', 'updatedAt', 'version', 'versionCreatedAt', 'versionCreatedBy', 'orderIdVersion', 'customerIdVersion', 'parentIdVersion', 'creditNoteIds', 'creditNoteVersions', ),
        self::TYPE_COLNAME       => array(CreditNoteVersionTableMap::COL_ID, CreditNoteVersionTableMap::COL_REF, CreditNoteVersionTableMap::COL_INVOICE_REF, CreditNoteVersionTableMap::COL_INVOICE_ADDRESS_ID, CreditNoteVersionTableMap::COL_INVOICE_DATE, CreditNoteVersionTableMap::COL_ORDER_ID, CreditNoteVersionTableMap::COL_CUSTOMER_ID, CreditNoteVersionTableMap::COL_PARENT_ID, CreditNoteVersionTableMap::COL_TYPE_ID, CreditNoteVersionTableMap::COL_STATUS_ID, CreditNoteVersionTableMap::COL_CURRENCY_ID, CreditNoteVersionTableMap::COL_CURRENCY_RATE, CreditNoteVersionTableMap::COL_TOTAL_PRICE, CreditNoteVersionTableMap::COL_TOTAL_PRICE_WITH_TAX, CreditNoteVersionTableMap::COL_DISCOUNT_WITHOUT_TAX, CreditNoteVersionTableMap::COL_DISCOUNT_WITH_TAX, CreditNoteVersionTableMap::COL_ALLOW_PARTIAL_USE, CreditNoteVersionTableMap::COL_CREATED_AT, CreditNoteVersionTableMap::COL_UPDATED_AT, CreditNoteVersionTableMap::COL_VERSION, CreditNoteVersionTableMap::COL_VERSION_CREATED_AT, CreditNoteVersionTableMap::COL_VERSION_CREATED_BY, CreditNoteVersionTableMap::COL_ORDER_ID_VERSION, CreditNoteVersionTableMap::COL_CUSTOMER_ID_VERSION, CreditNoteVersionTableMap::COL_PARENT_ID_VERSION, CreditNoteVersionTableMap::COL_CREDIT_NOTE_IDS, CreditNoteVersionTableMap::COL_CREDIT_NOTE_VERSIONS, ),
        self::TYPE_FIELDNAME     => array('id', 'ref', 'invoice_ref', 'invoice_address_id', 'invoice_date', 'order_id', 'customer_id', 'parent_id', 'type_id', 'status_id', 'currency_id', 'currency_rate', 'total_price', 'total_price_with_tax', 'discount_without_tax', 'discount_with_tax', 'allow_partial_use', 'created_at', 'updated_at', 'version', 'version_created_at', 'version_created_by', 'order_id_version', 'customer_id_version', 'parent_id_version', 'credit_note_ids', 'credit_note_versions', ),
        self::TYPE_NUM           => array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, )
    );

    /**
     * holds an array of keys for quick access to the fieldnames array
     *
     * first dimension keys are the type constants
     * e.g. self::$fieldKeys[self::TYPE_PHPNAME]['Id'] = 0
     */
    protected static $fieldKeys = array (
        self::TYPE_PHPNAME       => array('Id' => 0, 'Ref' => 1, 'InvoiceRef' => 2, 'InvoiceAddressId' => 3, 'InvoiceDate' => 4, 'OrderId' => 5, 'CustomerId' => 6, 'ParentId' => 7, 'TypeId' => 8, 'StatusId' => 9, 'CurrencyId' => 10, 'CurrencyRate' => 11, 'TotalPrice' => 12, 'TotalPriceWithTax' => 13, 'DiscountWithoutTax' => 14, 'DiscountWithTax' => 15, 'AllowPartialUse' => 16, 'CreatedAt' => 17, 'UpdatedAt' => 18, 'Version' => 19, 'VersionCreatedAt' => 20, 'VersionCreatedBy' => 21, 'OrderIdVersion' => 22, 'CustomerIdVersion' => 23, 'ParentIdVersion' => 24, 'CreditNoteIds' => 25, 'CreditNoteVersions' => 26, ),
        self::TYPE_CAMELNAME     => array('id' => 0, 'ref' => 1, 'invoiceRef' => 2, 'invoiceAddressId' => 3, 'invoiceDate' => 4, 'orderId' => 5, 'customerId' => 6, 'parentId' => 7, 'typeId' => 8, 'statusId' => 9, 'currencyId' => 10, 'currencyRate' => 11, 'totalPrice' => 12, 'totalPriceWithTax' => 13, 'discountWithoutTax' => 14, 'discountWithTax' => 15, 'allowPartialUse' => 16, 'createdAt' => 17, 'updatedAt' => 18, 'version' => 19, 'versionCreatedAt' => 20, 'versionCreatedBy' => 21, 'orderIdVersion' => 22, 'customerIdVersion' => 23, 'parentIdVersion' => 24, 'creditNoteIds' => 25, 'creditNoteVersions' => 26, ),
        self::TYPE_COLNAME       => array(CreditNoteVersionTableMap::COL_ID => 0, CreditNoteVersionTableMap::COL_REF => 1, CreditNoteVersionTableMap::COL_INVOICE_REF => 2, CreditNoteVersionTableMap::COL_INVOICE_ADDRESS_ID => 3, CreditNoteVersionTableMap::COL_INVOICE_DATE => 4, CreditNoteVersionTableMap::COL_ORDER_ID => 5, CreditNoteVersionTableMap::COL_CUSTOMER_ID => 6, CreditNoteVersionTableMap::COL_PARENT_ID => 7, CreditNoteVersionTableMap::COL_TYPE_ID => 8, CreditNoteVersionTableMap::COL_STATUS_ID => 9, CreditNoteVersionTableMap::COL_CURRENCY_ID => 10, CreditNoteVersionTableMap::COL_CURRENCY_RATE => 11, CreditNoteVersionTableMap::COL_TOTAL_PRICE => 12, CreditNoteVersionTableMap::COL_TOTAL_PRICE_WITH_TAX => 13, CreditNoteVersionTableMap::COL_DISCOUNT_WITHOUT_TAX => 14, CreditNoteVersionTableMap::COL_DISCOUNT_WITH_TAX => 15, CreditNoteVersionTableMap::COL_ALLOW_PARTIAL_USE => 16, CreditNoteVersionTableMap::COL_CREATED_AT => 17, CreditNoteVersionTableMap::COL_UPDATED_AT => 18, CreditNoteVersionTableMap::COL_VERSION => 19, CreditNoteVersionTableMap::COL_VERSION_CREATED_AT => 20, CreditNoteVersionTableMap::COL_VERSION_CREATED_BY => 21, CreditNoteVersionTableMap::COL_ORDER_ID_VERSION => 22, CreditNoteVersionTableMap::COL_CUSTOMER_ID_VERSION => 23, CreditNoteVersionTableMap::COL_PARENT_ID_VERSION => 24, CreditNoteVersionTableMap::COL_CREDIT_NOTE_IDS => 25, CreditNoteVersionTableMap::COL_CREDIT_NOTE_VERSIONS => 26, ),
        self::TYPE_FIELDNAME     => array('id' => 0, 'ref' => 1, 'invoice_ref' => 2, 'invoice_address_id' => 3, 'invoice_date' => 4, 'order_id' => 5, 'customer_id' => 6, 'parent_id' => 7, 'type_id' => 8, 'status_id' => 9, 'currency_id' => 10, 'currency_rate' => 11, 'total_price' => 12, 'total_price_with_tax' => 13, 'discount_without_tax' => 14, 'discount_with_tax' => 15, 'allow_partial_use' => 16, 'created_at' => 17, 'updated_at' => 18, 'version' => 19, 'version_created_at' => 20, 'version_created_by' => 21, 'order_id_version' => 22, 'customer_id_version' => 23, 'parent_id_version' => 24, 'credit_note_ids' => 25, 'credit_note_versions' => 26, ),
        self::TYPE_NUM           => array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, )
    );

    /**
     * Initialize the table attributes and columns
     * Relations are not initialized by this method since they are lazy loaded
     *
     * @return void
     * @throws PropelException
     */
    public function initialize()
    {
        // attributes
        $this->setName('credit_note_version');
        $this->setPhpName('CreditNoteVersion');
        $this->setIdentifierQuoting(true);
        $this->setClassName('\\CreditNote\\Model\\CreditNoteVersion');
        $this->setPackage('CreditNote.Model');
        $this->setUseIdGenerator(false);
        // columns
        $this->addForeignPrimaryKey('id', 'Id', 'INTEGER' , 'credit_note', 'id', true, null, null);
        $this->addColumn('ref', 'Ref', 'VARCHAR', false, 45, null);
        $this->addColumn('invoice_ref', 'InvoiceRef', 'VARCHAR', false, 45, null);
        $this->addColumn('invoice_address_id', 'InvoiceAddressId', 'INTEGER', true, null, null);
        $this->addColumn('invoice_date', 'InvoiceDate', 'TIMESTAMP', false, null, null);
        $this->addColumn('order_id', 'OrderId', 'INTEGER', false, null, null);
        $this->addColumn('customer_id', 'CustomerId', 'INTEGER', true, null, null);
        $this->addColumn('parent_id', 'ParentId', 'INTEGER', false, null, null);
        $this->addColumn('type_id', 'TypeId', 'INTEGER', true, null, null);
        $this->addColumn('status_id', 'StatusId', 'INTEGER', true, null, null);
        $this->addColumn('currency_id', 'CurrencyId', 'INTEGER', true, null, null);
        $this->addColumn('currency_rate', 'CurrencyRate', 'FLOAT', false, null, null);
        $this->addColumn('total_price', 'TotalPrice', 'DECIMAL', false, 16, 0);
        $this->addColumn('total_price_with_tax', 'TotalPriceWithTax', 'DECIMAL', false, 16, 0);
        $this->addColumn('discount_without_tax', 'DiscountWithoutTax', 'DECIMAL', false, 16, 0);
        $this->addColumn('discount_with_tax', 'DiscountWithTax', 'DECIMAL', false, 16, 0);
        $this->addColumn('allow_partial_use', 'AllowPartialUse', 'BOOLEAN', false, 1, true);
        $this->addColumn('created_at', 'CreatedAt', 'TIMESTAMP', false, null, null);
        $this->addColumn('updated_at', 'UpdatedAt', 'TIMESTAMP', false, null, null);
        $this->addPrimaryKey('version', 'Version', 'INTEGER', true, null, 0);
        $this->addColumn('version_created_at', 'VersionCreatedAt', 'TIMESTAMP', false, null, null);
        $this->addColumn('version_created_by', 'VersionCreatedBy', 'VARCHAR', false, 100, null);
        $this->addColumn('order_id_version', 'OrderIdVersion', 'INTEGER', false, null, 0);
        $this->addColumn('customer_id_version', 'CustomerIdVersion', 'INTEGER', false, null, 0);
        $this->addColumn('parent_id_version', 'ParentIdVersion', 'INTEGER', false, null, 0);
        $this->addColumn('credit_note_ids', 'CreditNoteIds', 'ARRAY', false, null, null);
        $this->addColumn('credit_note_versions', 'CreditNoteVersions', 'ARRAY', false, null, null);
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
        $this->addRelation('CreditNote', '\\CreditNote\\Model\\CreditNote', RelationMap::MANY_TO_ONE, array (
  0 =>
  array (
    0 => ':id',
    1 => ':id',
  ),
), 'CASCADE', null, null, false);
    } // buildRelations()

    /**
     * Adds an object to the instance pool.
     *
     * Propel keeps cached copies of objects in an instance pool when they are retrieved
     * from the database. In some cases you may need to explicitly add objects
     * to the cache in order to ensure that the same objects are always returned by find*()
     * and findPk*() calls.
     *
     * @param \CreditNote\Model\CreditNoteVersion $obj A \CreditNote\Model\CreditNoteVersion object.
     * @param string $key             (optional) key to use for instance map (for performance boost if key was already calculated externally).
     */
    public static function addInstanceToPool($obj, $key = null)
    {
        if (Propel::isInstancePoolingEnabled()) {
            if (null === $key) {
                $key = serialize([(null === $obj->getId() || is_scalar($obj->getId()) || is_callable([$obj->getId(), '__toString']) ? (string) $obj->getId() : $obj->getId()), (null === $obj->getVersion() || is_scalar($obj->getVersion()) || is_callable([$obj->getVersion(), '__toString']) ? (string) $obj->getVersion() : $obj->getVersion())]);
            } // if key === null
            self::$instances[$key] = $obj;
        }
    }

    /**
     * Removes an object from the instance pool.
     *
     * Propel keeps cached copies of objects in an instance pool when they are retrieved
     * from the database.  In some cases -- especially when you override doDelete
     * methods in your stub classes -- you may need to explicitly remove objects
     * from the cache in order to prevent returning objects that no longer exist.
     *
     * @param mixed $value A \CreditNote\Model\CreditNoteVersion object or a primary key value.
     */
    public static function removeInstanceFromPool($value)
    {
        if (Propel::isInstancePoolingEnabled() && null !== $value) {
            if (\is_object($value) && $value instanceof \CreditNote\Model\CreditNoteVersion) {
                $key = serialize([(null === $value->getId() || is_scalar($value->getId()) || is_callable([$value->getId(), '__toString']) ? (string) $value->getId() : $value->getId()), (null === $value->getVersion() || is_scalar($value->getVersion()) || is_callable([$value->getVersion(), '__toString']) ? (string) $value->getVersion() : $value->getVersion())]);

            } elseif (\is_array($value) && count($value) === 2) {
                // assume we've been passed a primary key";
                $key = serialize([(null === $value[0] || is_scalar($value[0]) || is_callable([$value[0], '__toString']) ? (string) $value[0] : $value[0]), (null === $value[1] || is_scalar($value[1]) || is_callable([$value[1], '__toString']) ? (string) $value[1] : $value[1])]);
            } elseif ($value instanceof Criteria) {
                self::$instances = [];

                return;
            } else {
                $e = new PropelException("Invalid value passed to removeInstanceFromPool().  Expected primary key or \CreditNote\Model\CreditNoteVersion object; got " . (\is_object($value) ? \get_class($value) . ' object.' : var_export($value, true)));
                throw $e;
            }

            unset(self::$instances[$key]);
        }
    }

    /**
     * Retrieves a string version of the primary key from the DB resultset row that can be used to uniquely identify a row in this table.
     *
     * For tables with a single-column primary key, that simple pkey value will be returned.  For tables with
     * a multi-column primary key, a serialize()d version of the primary key will be returned.
     *
     * @param array  $row       resultset row.
     * @param int    $offset    The 0-based offset for reading from the resultset row.
     * @param string $indexType One of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_CAMELNAME
     *                           TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM
     *
     * @return string The primary key hash of the row
     */
    public static function getPrimaryKeyHashFromRow($row, $offset = 0, $indexType = TableMap::TYPE_NUM)
    {
        // If the PK cannot be derived from the row, return NULL.
        if ($row[TableMap::TYPE_NUM == $indexType ? 0 + $offset : static::translateFieldName('Id', TableMap::TYPE_PHPNAME, $indexType)] === null && $row[TableMap::TYPE_NUM == $indexType ? 19 + $offset : static::translateFieldName('Version', TableMap::TYPE_PHPNAME, $indexType)] === null) {
            return null;
        }

        return serialize([(null === $row[TableMap::TYPE_NUM == $indexType ? 0 + $offset : static::translateFieldName('Id', TableMap::TYPE_PHPNAME, $indexType)] || is_scalar($row[TableMap::TYPE_NUM == $indexType ? 0 + $offset : static::translateFieldName('Id', TableMap::TYPE_PHPNAME, $indexType)]) || is_callable([$row[TableMap::TYPE_NUM == $indexType ? 0 + $offset : static::translateFieldName('Id', TableMap::TYPE_PHPNAME, $indexType)], '__toString']) ? (string) $row[TableMap::TYPE_NUM == $indexType ? 0 + $offset : static::translateFieldName('Id', TableMap::TYPE_PHPNAME, $indexType)] : $row[TableMap::TYPE_NUM == $indexType ? 0 + $offset : static::translateFieldName('Id', TableMap::TYPE_PHPNAME, $indexType)]), (null === $row[TableMap::TYPE_NUM == $indexType ? 19 + $offset : static::translateFieldName('Version', TableMap::TYPE_PHPNAME, $indexType)] || is_scalar($row[TableMap::TYPE_NUM == $indexType ? 19 + $offset : static::translateFieldName('Version', TableMap::TYPE_PHPNAME, $indexType)]) || is_callable([$row[TableMap::TYPE_NUM == $indexType ? 19 + $offset : static::translateFieldName('Version', TableMap::TYPE_PHPNAME, $indexType)], '__toString']) ? (string) $row[TableMap::TYPE_NUM == $indexType ? 19 + $offset : static::translateFieldName('Version', TableMap::TYPE_PHPNAME, $indexType)] : $row[TableMap::TYPE_NUM == $indexType ? 19 + $offset : static::translateFieldName('Version', TableMap::TYPE_PHPNAME, $indexType)])]);
    }

    /**
     * Retrieves the primary key from the DB resultset row
     * For tables with a single-column primary key, that simple pkey value will be returned.  For tables with
     * a multi-column primary key, an array of the primary key columns will be returned.
     *
     * @param array  $row       resultset row.
     * @param int    $offset    The 0-based offset for reading from the resultset row.
     * @param string $indexType One of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_CAMELNAME
     *                           TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM
     *
     * @return mixed The primary key of the row
     */
    public static function getPrimaryKeyFromRow($row, $offset = 0, $indexType = TableMap::TYPE_NUM)
    {
            $pks = [];

        $pks[] = (int) $row[
            $indexType == TableMap::TYPE_NUM
                ? 0 + $offset
                : self::translateFieldName('Id', TableMap::TYPE_PHPNAME, $indexType)
        ];
        $pks[] = (int) $row[
            $indexType == TableMap::TYPE_NUM
                ? 19 + $offset
                : self::translateFieldName('Version', TableMap::TYPE_PHPNAME, $indexType)
        ];

        return $pks;
    }

    /**
     * The class that the tableMap will make instances of.
     *
     * If $withPrefix is true, the returned path
     * uses a dot-path notation which is translated into a path
     * relative to a location on the PHP include_path.
     * (e.g. path.to.MyClass -> 'path/to/MyClass.php')
     *
     * @param boolean $withPrefix Whether or not to return the path with the class name
     * @return string path.to.ClassName
     */
    public static function getOMClass($withPrefix = true)
    {
        return $withPrefix ? CreditNoteVersionTableMap::CLASS_DEFAULT : CreditNoteVersionTableMap::OM_CLASS;
    }

    /**
     * Populates an object of the default type or an object that inherit from the default.
     *
     * @param array  $row       row returned by DataFetcher->fetch().
     * @param int    $offset    The 0-based offset for reading from the resultset row.
     * @param string $indexType The index type of $row. Mostly DataFetcher->getIndexType().
                                 One of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_CAMELNAME
     *                           TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM.
     *
     * @throws PropelException Any exceptions caught during processing will be
     *                         rethrown wrapped into a PropelException.
     * @return array           (CreditNoteVersion object, last column rank)
     */
    public static function populateObject($row, $offset = 0, $indexType = TableMap::TYPE_NUM)
    {
        $key = CreditNoteVersionTableMap::getPrimaryKeyHashFromRow($row, $offset, $indexType);
        if (null !== ($obj = CreditNoteVersionTableMap::getInstanceFromPool($key))) {
            // We no longer rehydrate the object, since this can cause data loss.
            // See http://www.propelorm.org/ticket/509
            // $obj->hydrate($row, $offset, true); // rehydrate
            $col = $offset + CreditNoteVersionTableMap::NUM_HYDRATE_COLUMNS;
        } else {
            $cls = CreditNoteVersionTableMap::OM_CLASS;
            /** @var CreditNoteVersion $obj */
            $obj = new $cls();
            $col = $obj->hydrate($row, $offset, false, $indexType);
            CreditNoteVersionTableMap::addInstanceToPool($obj, $key);
        }

        return array($obj, $col);
    }

    /**
     * The returned array will contain objects of the default type or
     * objects that inherit from the default.
     *
     * @param DataFetcherInterface $dataFetcher
     * @return array
     * @throws PropelException Any exceptions caught during processing will be
     *                         rethrown wrapped into a PropelException.
     */
    public static function populateObjects(DataFetcherInterface $dataFetcher)
    {
        $results = array();

        // set the class once to avoid overhead in the loop
        $cls = static::getOMClass(false);
        // populate the object(s)
        while ($row = $dataFetcher->fetch()) {
            $key = CreditNoteVersionTableMap::getPrimaryKeyHashFromRow($row, 0, $dataFetcher->getIndexType());
            if (null !== ($obj = CreditNoteVersionTableMap::getInstanceFromPool($key))) {
                // We no longer rehydrate the object, since this can cause data loss.
                // See http://www.propelorm.org/ticket/509
                // $obj->hydrate($row, 0, true); // rehydrate
                $results[] = $obj;
            } else {
                /** @var CreditNoteVersion $obj */
                $obj = new $cls();
                $obj->hydrate($row);
                $results[] = $obj;
                CreditNoteVersionTableMap::addInstanceToPool($obj, $key);
            } // if key exists
        }

        return $results;
    }
    /**
     * Add all the columns needed to create a new object.
     *
     * Note: any columns that were marked with lazyLoad="true" in the
     * XML schema will not be added to the select list and only loaded
     * on demand.
     *
     * @param Criteria $criteria object containing the columns to add.
     * @param string   $alias    optional table alias
     * @throws PropelException Any exceptions caught during processing will be
     *                         rethrown wrapped into a PropelException.
     */
    public static function addSelectColumns(Criteria $criteria, $alias = null)
    {
        if (null === $alias) {
            $criteria->addSelectColumn(CreditNoteVersionTableMap::COL_ID);
            $criteria->addSelectColumn(CreditNoteVersionTableMap::COL_REF);
            $criteria->addSelectColumn(CreditNoteVersionTableMap::COL_INVOICE_REF);
            $criteria->addSelectColumn(CreditNoteVersionTableMap::COL_INVOICE_ADDRESS_ID);
            $criteria->addSelectColumn(CreditNoteVersionTableMap::COL_INVOICE_DATE);
            $criteria->addSelectColumn(CreditNoteVersionTableMap::COL_ORDER_ID);
            $criteria->addSelectColumn(CreditNoteVersionTableMap::COL_CUSTOMER_ID);
            $criteria->addSelectColumn(CreditNoteVersionTableMap::COL_PARENT_ID);
            $criteria->addSelectColumn(CreditNoteVersionTableMap::COL_TYPE_ID);
            $criteria->addSelectColumn(CreditNoteVersionTableMap::COL_STATUS_ID);
            $criteria->addSelectColumn(CreditNoteVersionTableMap::COL_CURRENCY_ID);
            $criteria->addSelectColumn(CreditNoteVersionTableMap::COL_CURRENCY_RATE);
            $criteria->addSelectColumn(CreditNoteVersionTableMap::COL_TOTAL_PRICE);
            $criteria->addSelectColumn(CreditNoteVersionTableMap::COL_TOTAL_PRICE_WITH_TAX);
            $criteria->addSelectColumn(CreditNoteVersionTableMap::COL_DISCOUNT_WITHOUT_TAX);
            $criteria->addSelectColumn(CreditNoteVersionTableMap::COL_DISCOUNT_WITH_TAX);
            $criteria->addSelectColumn(CreditNoteVersionTableMap::COL_ALLOW_PARTIAL_USE);
            $criteria->addSelectColumn(CreditNoteVersionTableMap::COL_CREATED_AT);
            $criteria->addSelectColumn(CreditNoteVersionTableMap::COL_UPDATED_AT);
            $criteria->addSelectColumn(CreditNoteVersionTableMap::COL_VERSION);
            $criteria->addSelectColumn(CreditNoteVersionTableMap::COL_VERSION_CREATED_AT);
            $criteria->addSelectColumn(CreditNoteVersionTableMap::COL_VERSION_CREATED_BY);
            $criteria->addSelectColumn(CreditNoteVersionTableMap::COL_ORDER_ID_VERSION);
            $criteria->addSelectColumn(CreditNoteVersionTableMap::COL_CUSTOMER_ID_VERSION);
            $criteria->addSelectColumn(CreditNoteVersionTableMap::COL_PARENT_ID_VERSION);
            $criteria->addSelectColumn(CreditNoteVersionTableMap::COL_CREDIT_NOTE_IDS);
            $criteria->addSelectColumn(CreditNoteVersionTableMap::COL_CREDIT_NOTE_VERSIONS);
        } else {
            $criteria->addSelectColumn($alias . '.id');
            $criteria->addSelectColumn($alias . '.ref');
            $criteria->addSelectColumn($alias . '.invoice_ref');
            $criteria->addSelectColumn($alias . '.invoice_address_id');
            $criteria->addSelectColumn($alias . '.invoice_date');
            $criteria->addSelectColumn($alias . '.order_id');
            $criteria->addSelectColumn($alias . '.customer_id');
            $criteria->addSelectColumn($alias . '.parent_id');
            $criteria->addSelectColumn($alias . '.type_id');
            $criteria->addSelectColumn($alias . '.status_id');
            $criteria->addSelectColumn($alias . '.currency_id');
            $criteria->addSelectColumn($alias . '.currency_rate');
            $criteria->addSelectColumn($alias . '.total_price');
            $criteria->addSelectColumn($alias . '.total_price_with_tax');
            $criteria->addSelectColumn($alias . '.discount_without_tax');
            $criteria->addSelectColumn($alias . '.discount_with_tax');
            $criteria->addSelectColumn($alias . '.allow_partial_use');
            $criteria->addSelectColumn($alias . '.created_at');
            $criteria->addSelectColumn($alias . '.updated_at');
            $criteria->addSelectColumn($alias . '.version');
            $criteria->addSelectColumn($alias . '.version_created_at');
            $criteria->addSelectColumn($alias . '.version_created_by');
            $criteria->addSelectColumn($alias . '.order_id_version');
            $criteria->addSelectColumn($alias . '.customer_id_version');
            $criteria->addSelectColumn($alias . '.parent_id_version');
            $criteria->addSelectColumn($alias . '.credit_note_ids');
            $criteria->addSelectColumn($alias . '.credit_note_versions');
        }
    }

    /**
     * Returns the TableMap related to this object.
     * This method is not needed for general use but a specific application could have a need.
     * @return TableMap
     * @throws PropelException Any exceptions caught during processing will be
     *                         rethrown wrapped into a PropelException.
     */
    public static function getTableMap()
    {
        return Propel::getServiceContainer()->getDatabaseMap(CreditNoteVersionTableMap::DATABASE_NAME)->getTable(CreditNoteVersionTableMap::TABLE_NAME);
    }

    /**
     * Add a TableMap instance to the database for this tableMap class.
     */
    public static function buildTableMap()
    {
        $dbMap = Propel::getServiceContainer()->getDatabaseMap(CreditNoteVersionTableMap::DATABASE_NAME);
        if (!$dbMap->hasTable(CreditNoteVersionTableMap::TABLE_NAME)) {
            $dbMap->addTableObject(new CreditNoteVersionTableMap());
        }
    }

    /**
     * Performs a DELETE on the database, given a CreditNoteVersion or Criteria object OR a primary key value.
     *
     * @param mixed               $values Criteria or CreditNoteVersion object or primary key or array of primary keys
     *              which is used to create the DELETE statement
     * @param  ConnectionInterface $con the connection to use
     * @return int             The number of affected rows (if supported by underlying database driver).  This includes CASCADE-related rows
     *                         if supported by native driver or if emulated using Propel.
     * @throws PropelException Any exceptions caught during processing will be
     *                         rethrown wrapped into a PropelException.
     */
     public static function doDelete($values, ConnectionInterface $con = null)
     {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(CreditNoteVersionTableMap::DATABASE_NAME);
        }

        if ($values instanceof Criteria) {
            // rename for clarity
            $criteria = $values;
        } elseif ($values instanceof \CreditNote\Model\CreditNoteVersion) { // it's a model object
            // create criteria based on pk values
            $criteria = $values->buildPkeyCriteria();
        } else { // it's a primary key, or an array of pks
            $criteria = new Criteria(CreditNoteVersionTableMap::DATABASE_NAME);
            // primary key is composite; we therefore, expect
            // the primary key passed to be an array of pkey values
            if (count($values) == count($values, COUNT_RECURSIVE)) {
                // array is not multi-dimensional
                $values = array($values);
            }
            foreach ($values as $value) {
                $criterion = $criteria->getNewCriterion(CreditNoteVersionTableMap::COL_ID, $value[0]);
                $criterion->addAnd($criteria->getNewCriterion(CreditNoteVersionTableMap::COL_VERSION, $value[1]));
                $criteria->addOr($criterion);
            }
        }

        $query = CreditNoteVersionQuery::create()->mergeWith($criteria);

        if ($values instanceof Criteria) {
            CreditNoteVersionTableMap::clearInstancePool();
        } elseif (!\is_object($values)) { // it's a primary key, or an array of pks
            foreach ((array) $values as $singleval) {
                CreditNoteVersionTableMap::removeInstanceFromPool($singleval);
            }
        }

        return $query->delete($con);
    }

    /**
     * Deletes all rows from the credit_note_version table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public static function doDeleteAll(ConnectionInterface $con = null)
    {
        return CreditNoteVersionQuery::create()->doDeleteAll($con);
    }

    /**
     * Performs an INSERT on the database, given a CreditNoteVersion or Criteria object.
     *
     * @param mixed               $criteria Criteria or CreditNoteVersion object containing data that is used to create the INSERT statement.
     * @param ConnectionInterface $con the ConnectionInterface connection to use
     * @return mixed           The new primary key.
     * @throws PropelException Any exceptions caught during processing will be
     *                         rethrown wrapped into a PropelException.
     */
    public static function doInsert($criteria, ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(CreditNoteVersionTableMap::DATABASE_NAME);
        }

        if ($criteria instanceof Criteria) {
            $criteria = clone $criteria; // rename for clarity
        } else {
            $criteria = $criteria->buildCriteria(); // build Criteria from CreditNoteVersion object
        }


        // Set the correct dbName
        $query = CreditNoteVersionQuery::create()->mergeWith($criteria);

        // use transaction because $criteria could contain info
        // for more than one table (I guess, conceivably)
        return $con->transaction(function () use ($con, $query) {
            return $query->doInsert($con);
        });
    }

} // CreditNoteVersionTableMap
// This is the static code needed to register the TableMap for this table with the main Propel class.
//
CreditNoteVersionTableMap::buildTableMap();
