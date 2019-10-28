<?php

namespace CreditNote\Model\Map;

use CreditNote\Model\CreditNote;
use CreditNote\Model\CreditNoteQuery;
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
 * This class defines the structure of the 'credit_note' table.
 *
 *
 *
 * This map class is used by Propel to do runtime db structure discovery.
 * For example, the createSelectSql() method checks the type of a given column used in an
 * ORDER BY clause to know whether it needs to apply SQL to make the ORDER BY case-insensitive
 * (i.e. if it's a text column type).
 *
 */
class CreditNoteTableMap extends TableMap
{
    use InstancePoolTrait;
    use TableMapTrait;

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'CreditNote.Model.Map.CreditNoteTableMap';

    /**
     * The default database name for this class
     */
    const DATABASE_NAME = 'thelia';

    /**
     * The table name for this class
     */
    const TABLE_NAME = 'credit_note';

    /**
     * The related Propel class for this table
     */
    const OM_CLASS = '\\CreditNote\\Model\\CreditNote';

    /**
     * A class that can be returned by this tableMap
     */
    const CLASS_DEFAULT = 'CreditNote.Model.CreditNote';

    /**
     * The total number of columns
     */
    const NUM_COLUMNS = 22;

    /**
     * The number of lazy-loaded columns
     */
    const NUM_LAZY_LOAD_COLUMNS = 0;

    /**
     * The number of columns to hydrate (NUM_COLUMNS - NUM_LAZY_LOAD_COLUMNS)
     */
    const NUM_HYDRATE_COLUMNS = 22;

    /**
    * the column legacy name for the id field
    * @deprecated Legacy constant for compatibility. Use COL_ID.
    */
    const ID = 'credit_note.id';
    /**
    * the column legacy name for the ref field
    * @deprecated Legacy constant for compatibility. Use COL_REF.
    */
    const REF = 'credit_note.ref';
    /**
    * the column legacy name for the invoice_ref field
    * @deprecated Legacy constant for compatibility. Use COL_INVOICE_REF.
    */
    const INVOICE_REF = 'credit_note.invoice_ref';
    /**
    * the column legacy name for the invoice_address_id field
    * @deprecated Legacy constant for compatibility. Use COL_INVOICE_ADDRESS_ID.
    */
    const INVOICE_ADDRESS_ID = 'credit_note.invoice_address_id';
    /**
    * the column legacy name for the invoice_date field
    * @deprecated Legacy constant for compatibility. Use COL_INVOICE_DATE.
    */
    const INVOICE_DATE = 'credit_note.invoice_date';
    /**
    * the column legacy name for the order_id field
    * @deprecated Legacy constant for compatibility. Use COL_ORDER_ID.
    */
    const ORDER_ID = 'credit_note.order_id';
    /**
    * the column legacy name for the customer_id field
    * @deprecated Legacy constant for compatibility. Use COL_CUSTOMER_ID.
    */
    const CUSTOMER_ID = 'credit_note.customer_id';
    /**
    * the column legacy name for the parent_id field
    * @deprecated Legacy constant for compatibility. Use COL_PARENT_ID.
    */
    const PARENT_ID = 'credit_note.parent_id';
    /**
    * the column legacy name for the type_id field
    * @deprecated Legacy constant for compatibility. Use COL_TYPE_ID.
    */
    const TYPE_ID = 'credit_note.type_id';
    /**
    * the column legacy name for the status_id field
    * @deprecated Legacy constant for compatibility. Use COL_STATUS_ID.
    */
    const STATUS_ID = 'credit_note.status_id';
    /**
    * the column legacy name for the currency_id field
    * @deprecated Legacy constant for compatibility. Use COL_CURRENCY_ID.
    */
    const CURRENCY_ID = 'credit_note.currency_id';
    /**
    * the column legacy name for the currency_rate field
    * @deprecated Legacy constant for compatibility. Use COL_CURRENCY_RATE.
    */
    const CURRENCY_RATE = 'credit_note.currency_rate';
    /**
    * the column legacy name for the total_price field
    * @deprecated Legacy constant for compatibility. Use COL_TOTAL_PRICE.
    */
    const TOTAL_PRICE = 'credit_note.total_price';
    /**
    * the column legacy name for the total_price_with_tax field
    * @deprecated Legacy constant for compatibility. Use COL_TOTAL_PRICE_WITH_TAX.
    */
    const TOTAL_PRICE_WITH_TAX = 'credit_note.total_price_with_tax';
    /**
    * the column legacy name for the discount_without_tax field
    * @deprecated Legacy constant for compatibility. Use COL_DISCOUNT_WITHOUT_TAX.
    */
    const DISCOUNT_WITHOUT_TAX = 'credit_note.discount_without_tax';
    /**
    * the column legacy name for the discount_with_tax field
    * @deprecated Legacy constant for compatibility. Use COL_DISCOUNT_WITH_TAX.
    */
    const DISCOUNT_WITH_TAX = 'credit_note.discount_with_tax';
    /**
    * the column legacy name for the allow_partial_use field
    * @deprecated Legacy constant for compatibility. Use COL_ALLOW_PARTIAL_USE.
    */
    const ALLOW_PARTIAL_USE = 'credit_note.allow_partial_use';
    /**
    * the column legacy name for the created_at field
    * @deprecated Legacy constant for compatibility. Use COL_CREATED_AT.
    */
    const CREATED_AT = 'credit_note.created_at';
    /**
    * the column legacy name for the updated_at field
    * @deprecated Legacy constant for compatibility. Use COL_UPDATED_AT.
    */
    const UPDATED_AT = 'credit_note.updated_at';
    /**
    * the column legacy name for the version field
    * @deprecated Legacy constant for compatibility. Use COL_VERSION.
    */
    const VERSION = 'credit_note.version';
    /**
    * the column legacy name for the version_created_at field
    * @deprecated Legacy constant for compatibility. Use COL_VERSION_CREATED_AT.
    */
    const VERSION_CREATED_AT = 'credit_note.version_created_at';
    /**
    * the column legacy name for the version_created_by field
    * @deprecated Legacy constant for compatibility. Use COL_VERSION_CREATED_BY.
    */
    const VERSION_CREATED_BY = 'credit_note.version_created_by';
    /**
     * the column name for the id field
     */
    const COL_ID = 'credit_note.id';
    /**
     * the column name for the ref field
     */
    const COL_REF = 'credit_note.ref';
    /**
     * the column name for the invoice_ref field
     */
    const COL_INVOICE_REF = 'credit_note.invoice_ref';
    /**
     * the column name for the invoice_address_id field
     */
    const COL_INVOICE_ADDRESS_ID = 'credit_note.invoice_address_id';
    /**
     * the column name for the invoice_date field
     */
    const COL_INVOICE_DATE = 'credit_note.invoice_date';
    /**
     * the column name for the order_id field
     */
    const COL_ORDER_ID = 'credit_note.order_id';
    /**
     * the column name for the customer_id field
     */
    const COL_CUSTOMER_ID = 'credit_note.customer_id';
    /**
     * the column name for the parent_id field
     */
    const COL_PARENT_ID = 'credit_note.parent_id';
    /**
     * the column name for the type_id field
     */
    const COL_TYPE_ID = 'credit_note.type_id';
    /**
     * the column name for the status_id field
     */
    const COL_STATUS_ID = 'credit_note.status_id';
    /**
     * the column name for the currency_id field
     */
    const COL_CURRENCY_ID = 'credit_note.currency_id';
    /**
     * the column name for the currency_rate field
     */
    const COL_CURRENCY_RATE = 'credit_note.currency_rate';
    /**
     * the column name for the total_price field
     */
    const COL_TOTAL_PRICE = 'credit_note.total_price';
    /**
     * the column name for the total_price_with_tax field
     */
    const COL_TOTAL_PRICE_WITH_TAX = 'credit_note.total_price_with_tax';
    /**
     * the column name for the discount_without_tax field
     */
    const COL_DISCOUNT_WITHOUT_TAX = 'credit_note.discount_without_tax';
    /**
     * the column name for the discount_with_tax field
     */
    const COL_DISCOUNT_WITH_TAX = 'credit_note.discount_with_tax';
    /**
     * the column name for the allow_partial_use field
     */
    const COL_ALLOW_PARTIAL_USE = 'credit_note.allow_partial_use';
    /**
     * the column name for the created_at field
     */
    const COL_CREATED_AT = 'credit_note.created_at';
    /**
     * the column name for the updated_at field
     */
    const COL_UPDATED_AT = 'credit_note.updated_at';
    /**
     * the column name for the version field
     */
    const COL_VERSION = 'credit_note.version';
    /**
     * the column name for the version_created_at field
     */
    const COL_VERSION_CREATED_AT = 'credit_note.version_created_at';
    /**
     * the column name for the version_created_by field
     */
    const COL_VERSION_CREATED_BY = 'credit_note.version_created_by';

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
        self::TYPE_PHPNAME       => array('Id', 'Ref', 'InvoiceRef', 'InvoiceAddressId', 'InvoiceDate', 'OrderId', 'CustomerId', 'ParentId', 'TypeId', 'StatusId', 'CurrencyId', 'CurrencyRate', 'TotalPrice', 'TotalPriceWithTax', 'DiscountWithoutTax', 'DiscountWithTax', 'AllowPartialUse', 'CreatedAt', 'UpdatedAt', 'Version', 'VersionCreatedAt', 'VersionCreatedBy', ),
        self::TYPE_CAMELNAME     => array('id', 'ref', 'invoiceRef', 'invoiceAddressId', 'invoiceDate', 'orderId', 'customerId', 'parentId', 'typeId', 'statusId', 'currencyId', 'currencyRate', 'totalPrice', 'totalPriceWithTax', 'discountWithoutTax', 'discountWithTax', 'allowPartialUse', 'createdAt', 'updatedAt', 'version', 'versionCreatedAt', 'versionCreatedBy', ),
        self::TYPE_COLNAME       => array(CreditNoteTableMap::COL_ID, CreditNoteTableMap::COL_REF, CreditNoteTableMap::COL_INVOICE_REF, CreditNoteTableMap::COL_INVOICE_ADDRESS_ID, CreditNoteTableMap::COL_INVOICE_DATE, CreditNoteTableMap::COL_ORDER_ID, CreditNoteTableMap::COL_CUSTOMER_ID, CreditNoteTableMap::COL_PARENT_ID, CreditNoteTableMap::COL_TYPE_ID, CreditNoteTableMap::COL_STATUS_ID, CreditNoteTableMap::COL_CURRENCY_ID, CreditNoteTableMap::COL_CURRENCY_RATE, CreditNoteTableMap::COL_TOTAL_PRICE, CreditNoteTableMap::COL_TOTAL_PRICE_WITH_TAX, CreditNoteTableMap::COL_DISCOUNT_WITHOUT_TAX, CreditNoteTableMap::COL_DISCOUNT_WITH_TAX, CreditNoteTableMap::COL_ALLOW_PARTIAL_USE, CreditNoteTableMap::COL_CREATED_AT, CreditNoteTableMap::COL_UPDATED_AT, CreditNoteTableMap::COL_VERSION, CreditNoteTableMap::COL_VERSION_CREATED_AT, CreditNoteTableMap::COL_VERSION_CREATED_BY, ),
        self::TYPE_FIELDNAME     => array('id', 'ref', 'invoice_ref', 'invoice_address_id', 'invoice_date', 'order_id', 'customer_id', 'parent_id', 'type_id', 'status_id', 'currency_id', 'currency_rate', 'total_price', 'total_price_with_tax', 'discount_without_tax', 'discount_with_tax', 'allow_partial_use', 'created_at', 'updated_at', 'version', 'version_created_at', 'version_created_by', ),
        self::TYPE_NUM           => array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, )
    );

    /**
     * holds an array of keys for quick access to the fieldnames array
     *
     * first dimension keys are the type constants
     * e.g. self::$fieldKeys[self::TYPE_PHPNAME]['Id'] = 0
     */
    protected static $fieldKeys = array (
        self::TYPE_PHPNAME       => array('Id' => 0, 'Ref' => 1, 'InvoiceRef' => 2, 'InvoiceAddressId' => 3, 'InvoiceDate' => 4, 'OrderId' => 5, 'CustomerId' => 6, 'ParentId' => 7, 'TypeId' => 8, 'StatusId' => 9, 'CurrencyId' => 10, 'CurrencyRate' => 11, 'TotalPrice' => 12, 'TotalPriceWithTax' => 13, 'DiscountWithoutTax' => 14, 'DiscountWithTax' => 15, 'AllowPartialUse' => 16, 'CreatedAt' => 17, 'UpdatedAt' => 18, 'Version' => 19, 'VersionCreatedAt' => 20, 'VersionCreatedBy' => 21, ),
        self::TYPE_CAMELNAME     => array('id' => 0, 'ref' => 1, 'invoiceRef' => 2, 'invoiceAddressId' => 3, 'invoiceDate' => 4, 'orderId' => 5, 'customerId' => 6, 'parentId' => 7, 'typeId' => 8, 'statusId' => 9, 'currencyId' => 10, 'currencyRate' => 11, 'totalPrice' => 12, 'totalPriceWithTax' => 13, 'discountWithoutTax' => 14, 'discountWithTax' => 15, 'allowPartialUse' => 16, 'createdAt' => 17, 'updatedAt' => 18, 'version' => 19, 'versionCreatedAt' => 20, 'versionCreatedBy' => 21, ),
        self::TYPE_COLNAME       => array(CreditNoteTableMap::COL_ID => 0, CreditNoteTableMap::COL_REF => 1, CreditNoteTableMap::COL_INVOICE_REF => 2, CreditNoteTableMap::COL_INVOICE_ADDRESS_ID => 3, CreditNoteTableMap::COL_INVOICE_DATE => 4, CreditNoteTableMap::COL_ORDER_ID => 5, CreditNoteTableMap::COL_CUSTOMER_ID => 6, CreditNoteTableMap::COL_PARENT_ID => 7, CreditNoteTableMap::COL_TYPE_ID => 8, CreditNoteTableMap::COL_STATUS_ID => 9, CreditNoteTableMap::COL_CURRENCY_ID => 10, CreditNoteTableMap::COL_CURRENCY_RATE => 11, CreditNoteTableMap::COL_TOTAL_PRICE => 12, CreditNoteTableMap::COL_TOTAL_PRICE_WITH_TAX => 13, CreditNoteTableMap::COL_DISCOUNT_WITHOUT_TAX => 14, CreditNoteTableMap::COL_DISCOUNT_WITH_TAX => 15, CreditNoteTableMap::COL_ALLOW_PARTIAL_USE => 16, CreditNoteTableMap::COL_CREATED_AT => 17, CreditNoteTableMap::COL_UPDATED_AT => 18, CreditNoteTableMap::COL_VERSION => 19, CreditNoteTableMap::COL_VERSION_CREATED_AT => 20, CreditNoteTableMap::COL_VERSION_CREATED_BY => 21, ),
        self::TYPE_FIELDNAME     => array('id' => 0, 'ref' => 1, 'invoice_ref' => 2, 'invoice_address_id' => 3, 'invoice_date' => 4, 'order_id' => 5, 'customer_id' => 6, 'parent_id' => 7, 'type_id' => 8, 'status_id' => 9, 'currency_id' => 10, 'currency_rate' => 11, 'total_price' => 12, 'total_price_with_tax' => 13, 'discount_without_tax' => 14, 'discount_with_tax' => 15, 'allow_partial_use' => 16, 'created_at' => 17, 'updated_at' => 18, 'version' => 19, 'version_created_at' => 20, 'version_created_by' => 21, ),
        self::TYPE_NUM           => array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, )
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
        $this->setName('credit_note');
        $this->setPhpName('CreditNote');
        $this->setIdentifierQuoting(true);
        $this->setClassName('\\CreditNote\\Model\\CreditNote');
        $this->setPackage('CreditNote.Model');
        $this->setUseIdGenerator(true);
        // columns
        $this->addPrimaryKey('id', 'Id', 'INTEGER', true, null, null);
        $this->addColumn('ref', 'Ref', 'VARCHAR', false, 45, null);
        $this->addColumn('invoice_ref', 'InvoiceRef', 'VARCHAR', false, 45, null);
        $this->addForeignKey('invoice_address_id', 'InvoiceAddressId', 'INTEGER', 'credit_note_address', 'id', true, null, null);
        $this->addColumn('invoice_date', 'InvoiceDate', 'TIMESTAMP', false, null, null);
        $this->addForeignKey('order_id', 'OrderId', 'INTEGER', 'order', 'id', false, null, null);
        $this->addForeignKey('customer_id', 'CustomerId', 'INTEGER', 'customer', 'id', true, null, null);
        $this->addForeignKey('parent_id', 'ParentId', 'INTEGER', 'credit_note', 'id', false, null, null);
        $this->addForeignKey('type_id', 'TypeId', 'INTEGER', 'credit_note_type', 'id', true, null, null);
        $this->addForeignKey('status_id', 'StatusId', 'INTEGER', 'credit_note_status', 'id', true, null, null);
        $this->addForeignKey('currency_id', 'CurrencyId', 'INTEGER', 'currency', 'id', true, null, null);
        $this->addColumn('currency_rate', 'CurrencyRate', 'FLOAT', false, null, null);
        $this->addColumn('total_price', 'TotalPrice', 'DECIMAL', false, 16, 0);
        $this->addColumn('total_price_with_tax', 'TotalPriceWithTax', 'DECIMAL', false, 16, 0);
        $this->addColumn('discount_without_tax', 'DiscountWithoutTax', 'DECIMAL', false, 16, 0);
        $this->addColumn('discount_with_tax', 'DiscountWithTax', 'DECIMAL', false, 16, 0);
        $this->addColumn('allow_partial_use', 'AllowPartialUse', 'BOOLEAN', false, 1, true);
        $this->addColumn('created_at', 'CreatedAt', 'TIMESTAMP', false, null, null);
        $this->addColumn('updated_at', 'UpdatedAt', 'TIMESTAMP', false, null, null);
        $this->addColumn('version', 'Version', 'INTEGER', false, null, 0);
        $this->addColumn('version_created_at', 'VersionCreatedAt', 'TIMESTAMP', false, null, null);
        $this->addColumn('version_created_by', 'VersionCreatedBy', 'VARCHAR', false, 100, null);
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
        $this->addRelation('Order', '\\Thelia\\Model\\Order', RelationMap::MANY_TO_ONE, array (
  0 =>
  array (
    0 => ':order_id',
    1 => ':id',
  ),
), 'RESTRICT', 'RESTRICT', null, false);
        $this->addRelation('Customer', '\\Thelia\\Model\\Customer', RelationMap::MANY_TO_ONE, array (
  0 =>
  array (
    0 => ':customer_id',
    1 => ':id',
  ),
), 'RESTRICT', 'RESTRICT', null, false);
        $this->addRelation('CreditNoteRelatedByParentId', '\\CreditNote\\Model\\CreditNote', RelationMap::MANY_TO_ONE, array (
  0 =>
  array (
    0 => ':parent_id',
    1 => ':id',
  ),
), 'RESTRICT', 'RESTRICT', null, false);
        $this->addRelation('CreditNoteType', '\\CreditNote\\Model\\CreditNoteType', RelationMap::MANY_TO_ONE, array (
  0 =>
  array (
    0 => ':type_id',
    1 => ':id',
  ),
), 'RESTRICT', 'RESTRICT', null, false);
        $this->addRelation('CreditNoteStatus', '\\CreditNote\\Model\\CreditNoteStatus', RelationMap::MANY_TO_ONE, array (
  0 =>
  array (
    0 => ':status_id',
    1 => ':id',
  ),
), 'RESTRICT', 'RESTRICT', null, false);
        $this->addRelation('Currency', '\\Thelia\\Model\\Currency', RelationMap::MANY_TO_ONE, array (
  0 =>
  array (
    0 => ':currency_id',
    1 => ':id',
  ),
), 'RESTRICT', 'RESTRICT', null, false);
        $this->addRelation('CreditNoteAddress', '\\CreditNote\\Model\\CreditNoteAddress', RelationMap::MANY_TO_ONE, array (
  0 =>
  array (
    0 => ':invoice_address_id',
    1 => ':id',
  ),
), 'RESTRICT', 'RESTRICT', null, false);
        $this->addRelation('CreditNoteRelatedById', '\\CreditNote\\Model\\CreditNote', RelationMap::ONE_TO_MANY, array (
  0 =>
  array (
    0 => ':parent_id',
    1 => ':id',
  ),
), 'RESTRICT', 'RESTRICT', 'CreditNotesRelatedById', false);
        $this->addRelation('OrderCreditNote', '\\CreditNote\\Model\\OrderCreditNote', RelationMap::ONE_TO_MANY, array (
  0 =>
  array (
    0 => ':credit_note_id',
    1 => ':id',
  ),
), 'CASCADE', 'RESTRICT', 'OrderCreditNotes', false);
        $this->addRelation('CartCreditNote', '\\CreditNote\\Model\\CartCreditNote', RelationMap::ONE_TO_MANY, array (
  0 =>
  array (
    0 => ':credit_note_id',
    1 => ':id',
  ),
), 'CASCADE', 'RESTRICT', 'CartCreditNotes', false);
        $this->addRelation('CreditNoteDetail', '\\CreditNote\\Model\\CreditNoteDetail', RelationMap::ONE_TO_MANY, array (
  0 =>
  array (
    0 => ':credit_note_id',
    1 => ':id',
  ),
), 'CASCADE', 'RESTRICT', 'CreditNoteDetails', false);
        $this->addRelation('CreditNoteComment', '\\CreditNote\\Model\\CreditNoteComment', RelationMap::ONE_TO_MANY, array (
  0 =>
  array (
    0 => ':credit_note_id',
    1 => ':id',
  ),
), 'CASCADE', 'RESTRICT', 'CreditNoteComments', false);
        $this->addRelation('CreditNoteVersion', '\\CreditNote\\Model\\CreditNoteVersion', RelationMap::ONE_TO_MANY, array (
  0 =>
  array (
    0 => ':id',
    1 => ':id',
  ),
), 'CASCADE', null, 'CreditNoteVersions', false);
    } // buildRelations()

    /**
     *
     * Gets the list of behaviors registered for this table
     *
     * @return array Associative array (name => parameters) of behaviors
     */
    public function getBehaviors()
    {
        return array(
            'timestampable' => array('create_column' => 'created_at', 'update_column' => 'updated_at', 'disable_created_at' => 'false', 'disable_updated_at' => 'false', ),
            'versionable' => array('version_column' => 'version', 'version_table' => '', 'log_created_at' => 'true', 'log_created_by' => 'true', 'log_comment' => 'false', 'version_created_at_column' => 'version_created_at', 'version_created_by_column' => 'version_created_by', 'version_comment_column' => 'version_comment', 'indices' => 'false', ),
        );
    } // getBehaviors()
    /**
     * Method to invalidate the instance pool of all tables related to credit_note     * by a foreign key with ON DELETE CASCADE
     */
    public static function clearRelatedInstancePool()
    {
        // Invalidate objects in related instance pools,
        // since one or more of them may be deleted by ON DELETE CASCADE/SETNULL rule.
        OrderCreditNoteTableMap::clearInstancePool();
        CartCreditNoteTableMap::clearInstancePool();
        CreditNoteDetailTableMap::clearInstancePool();
        CreditNoteCommentTableMap::clearInstancePool();
        CreditNoteVersionTableMap::clearInstancePool();
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
        if ($row[TableMap::TYPE_NUM == $indexType ? 0 + $offset : static::translateFieldName('Id', TableMap::TYPE_PHPNAME, $indexType)] === null) {
            return null;
        }

        return null === $row[TableMap::TYPE_NUM == $indexType ? 0 + $offset : static::translateFieldName('Id', TableMap::TYPE_PHPNAME, $indexType)] || is_scalar($row[TableMap::TYPE_NUM == $indexType ? 0 + $offset : static::translateFieldName('Id', TableMap::TYPE_PHPNAME, $indexType)]) || is_callable([$row[TableMap::TYPE_NUM == $indexType ? 0 + $offset : static::translateFieldName('Id', TableMap::TYPE_PHPNAME, $indexType)], '__toString']) ? (string) $row[TableMap::TYPE_NUM == $indexType ? 0 + $offset : static::translateFieldName('Id', TableMap::TYPE_PHPNAME, $indexType)] : $row[TableMap::TYPE_NUM == $indexType ? 0 + $offset : static::translateFieldName('Id', TableMap::TYPE_PHPNAME, $indexType)];
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
        return (int) $row[
            $indexType == TableMap::TYPE_NUM
                ? 0 + $offset
                : self::translateFieldName('Id', TableMap::TYPE_PHPNAME, $indexType)
        ];
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
        return $withPrefix ? CreditNoteTableMap::CLASS_DEFAULT : CreditNoteTableMap::OM_CLASS;
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
     * @return array           (CreditNote object, last column rank)
     */
    public static function populateObject($row, $offset = 0, $indexType = TableMap::TYPE_NUM)
    {
        $key = CreditNoteTableMap::getPrimaryKeyHashFromRow($row, $offset, $indexType);
        if (null !== ($obj = CreditNoteTableMap::getInstanceFromPool($key))) {
            // We no longer rehydrate the object, since this can cause data loss.
            // See http://www.propelorm.org/ticket/509
            // $obj->hydrate($row, $offset, true); // rehydrate
            $col = $offset + CreditNoteTableMap::NUM_HYDRATE_COLUMNS;
        } else {
            $cls = CreditNoteTableMap::OM_CLASS;
            /** @var CreditNote $obj */
            $obj = new $cls();
            $col = $obj->hydrate($row, $offset, false, $indexType);
            CreditNoteTableMap::addInstanceToPool($obj, $key);
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
            $key = CreditNoteTableMap::getPrimaryKeyHashFromRow($row, 0, $dataFetcher->getIndexType());
            if (null !== ($obj = CreditNoteTableMap::getInstanceFromPool($key))) {
                // We no longer rehydrate the object, since this can cause data loss.
                // See http://www.propelorm.org/ticket/509
                // $obj->hydrate($row, 0, true); // rehydrate
                $results[] = $obj;
            } else {
                /** @var CreditNote $obj */
                $obj = new $cls();
                $obj->hydrate($row);
                $results[] = $obj;
                CreditNoteTableMap::addInstanceToPool($obj, $key);
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
            $criteria->addSelectColumn(CreditNoteTableMap::COL_ID);
            $criteria->addSelectColumn(CreditNoteTableMap::COL_REF);
            $criteria->addSelectColumn(CreditNoteTableMap::COL_INVOICE_REF);
            $criteria->addSelectColumn(CreditNoteTableMap::COL_INVOICE_ADDRESS_ID);
            $criteria->addSelectColumn(CreditNoteTableMap::COL_INVOICE_DATE);
            $criteria->addSelectColumn(CreditNoteTableMap::COL_ORDER_ID);
            $criteria->addSelectColumn(CreditNoteTableMap::COL_CUSTOMER_ID);
            $criteria->addSelectColumn(CreditNoteTableMap::COL_PARENT_ID);
            $criteria->addSelectColumn(CreditNoteTableMap::COL_TYPE_ID);
            $criteria->addSelectColumn(CreditNoteTableMap::COL_STATUS_ID);
            $criteria->addSelectColumn(CreditNoteTableMap::COL_CURRENCY_ID);
            $criteria->addSelectColumn(CreditNoteTableMap::COL_CURRENCY_RATE);
            $criteria->addSelectColumn(CreditNoteTableMap::COL_TOTAL_PRICE);
            $criteria->addSelectColumn(CreditNoteTableMap::COL_TOTAL_PRICE_WITH_TAX);
            $criteria->addSelectColumn(CreditNoteTableMap::COL_DISCOUNT_WITHOUT_TAX);
            $criteria->addSelectColumn(CreditNoteTableMap::COL_DISCOUNT_WITH_TAX);
            $criteria->addSelectColumn(CreditNoteTableMap::COL_ALLOW_PARTIAL_USE);
            $criteria->addSelectColumn(CreditNoteTableMap::COL_CREATED_AT);
            $criteria->addSelectColumn(CreditNoteTableMap::COL_UPDATED_AT);
            $criteria->addSelectColumn(CreditNoteTableMap::COL_VERSION);
            $criteria->addSelectColumn(CreditNoteTableMap::COL_VERSION_CREATED_AT);
            $criteria->addSelectColumn(CreditNoteTableMap::COL_VERSION_CREATED_BY);
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
        return Propel::getServiceContainer()->getDatabaseMap(CreditNoteTableMap::DATABASE_NAME)->getTable(CreditNoteTableMap::TABLE_NAME);
    }

    /**
     * Add a TableMap instance to the database for this tableMap class.
     */
    public static function buildTableMap()
    {
        $dbMap = Propel::getServiceContainer()->getDatabaseMap(CreditNoteTableMap::DATABASE_NAME);
        if (!$dbMap->hasTable(CreditNoteTableMap::TABLE_NAME)) {
            $dbMap->addTableObject(new CreditNoteTableMap());
        }
    }

    /**
     * Performs a DELETE on the database, given a CreditNote or Criteria object OR a primary key value.
     *
     * @param mixed               $values Criteria or CreditNote object or primary key or array of primary keys
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
            $con = Propel::getServiceContainer()->getWriteConnection(CreditNoteTableMap::DATABASE_NAME);
        }

        if ($values instanceof Criteria) {
            // rename for clarity
            $criteria = $values;
        } elseif ($values instanceof \CreditNote\Model\CreditNote) { // it's a model object
            // create criteria based on pk values
            $criteria = $values->buildPkeyCriteria();
        } else { // it's a primary key, or an array of pks
            $criteria = new Criteria(CreditNoteTableMap::DATABASE_NAME);
            $criteria->add(CreditNoteTableMap::COL_ID, (array) $values, Criteria::IN);
        }

        $query = CreditNoteQuery::create()->mergeWith($criteria);

        if ($values instanceof Criteria) {
            CreditNoteTableMap::clearInstancePool();
        } elseif (!\is_object($values)) { // it's a primary key, or an array of pks
            foreach ((array) $values as $singleval) {
                CreditNoteTableMap::removeInstanceFromPool($singleval);
            }
        }

        return $query->delete($con);
    }

    /**
     * Deletes all rows from the credit_note table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public static function doDeleteAll(ConnectionInterface $con = null)
    {
        return CreditNoteQuery::create()->doDeleteAll($con);
    }

    /**
     * Performs an INSERT on the database, given a CreditNote or Criteria object.
     *
     * @param mixed               $criteria Criteria or CreditNote object containing data that is used to create the INSERT statement.
     * @param ConnectionInterface $con the ConnectionInterface connection to use
     * @return mixed           The new primary key.
     * @throws PropelException Any exceptions caught during processing will be
     *                         rethrown wrapped into a PropelException.
     */
    public static function doInsert($criteria, ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(CreditNoteTableMap::DATABASE_NAME);
        }

        if ($criteria instanceof Criteria) {
            $criteria = clone $criteria; // rename for clarity
        } else {
            $criteria = $criteria->buildCriteria(); // build Criteria from CreditNote object
        }

        if ($criteria->containsKey(CreditNoteTableMap::COL_ID) && $criteria->keyContainsValue(CreditNoteTableMap::COL_ID) ) {
            throw new PropelException('Cannot insert a value for auto-increment primary key ('.CreditNoteTableMap::COL_ID.')');
        }


        // Set the correct dbName
        $query = CreditNoteQuery::create()->mergeWith($criteria);

        // use transaction because $criteria could contain info
        // for more than one table (I guess, conceivably)
        return $con->transaction(function () use ($con, $query) {
            return $query->doInsert($con);
        });
    }

} // CreditNoteTableMap
// This is the static code needed to register the TableMap for this table with the main Propel class.
//
CreditNoteTableMap::buildTableMap();
