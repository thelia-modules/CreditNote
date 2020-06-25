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
    const NUM_COLUMNS = 19;

    /**
     * The number of lazy-loaded columns
     */
    const NUM_LAZY_LOAD_COLUMNS = 0;

    /**
     * The number of columns to hydrate (NUM_COLUMNS - NUM_LAZY_LOAD_COLUMNS)
     */
    const NUM_HYDRATE_COLUMNS = 19;

    /**
     * the column name for the ID field
     */
    const ID = 'credit_note.ID';

    /**
     * the column name for the REF field
     */
    const REF = 'credit_note.REF';

    /**
     * the column name for the INVOICE_REF field
     */
    const INVOICE_REF = 'credit_note.INVOICE_REF';

    /**
     * the column name for the INVOICE_ADDRESS_ID field
     */
    const INVOICE_ADDRESS_ID = 'credit_note.INVOICE_ADDRESS_ID';

    /**
     * the column name for the INVOICE_DATE field
     */
    const INVOICE_DATE = 'credit_note.INVOICE_DATE';

    /**
     * the column name for the ORDER_ID field
     */
    const ORDER_ID = 'credit_note.ORDER_ID';

    /**
     * the column name for the CUSTOMER_ID field
     */
    const CUSTOMER_ID = 'credit_note.CUSTOMER_ID';

    /**
     * the column name for the PARENT_ID field
     */
    const PARENT_ID = 'credit_note.PARENT_ID';

    /**
     * the column name for the TYPE_ID field
     */
    const TYPE_ID = 'credit_note.TYPE_ID';

    /**
     * the column name for the STATUS_ID field
     */
    const STATUS_ID = 'credit_note.STATUS_ID';

    /**
     * the column name for the CURRENCY_ID field
     */
    const CURRENCY_ID = 'credit_note.CURRENCY_ID';

    /**
     * the column name for the CURRENCY_RATE field
     */
    const CURRENCY_RATE = 'credit_note.CURRENCY_RATE';

    /**
     * the column name for the TOTAL_PRICE field
     */
    const TOTAL_PRICE = 'credit_note.TOTAL_PRICE';

    /**
     * the column name for the TOTAL_PRICE_WITH_TAX field
     */
    const TOTAL_PRICE_WITH_TAX = 'credit_note.TOTAL_PRICE_WITH_TAX';

    /**
     * the column name for the DISCOUNT_WITHOUT_TAX field
     */
    const DISCOUNT_WITHOUT_TAX = 'credit_note.DISCOUNT_WITHOUT_TAX';

    /**
     * the column name for the DISCOUNT_WITH_TAX field
     */
    const DISCOUNT_WITH_TAX = 'credit_note.DISCOUNT_WITH_TAX';

    /**
     * the column name for the ALLOW_PARTIAL_USE field
     */
    const ALLOW_PARTIAL_USE = 'credit_note.ALLOW_PARTIAL_USE';

    /**
     * the column name for the CREATED_AT field
     */
    const CREATED_AT = 'credit_note.CREATED_AT';

    /**
     * the column name for the UPDATED_AT field
     */
    const UPDATED_AT = 'credit_note.UPDATED_AT';

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
        self::TYPE_PHPNAME       => array('Id', 'Ref', 'InvoiceRef', 'InvoiceAddressId', 'InvoiceDate', 'OrderId', 'CustomerId', 'ParentId', 'TypeId', 'StatusId', 'CurrencyId', 'CurrencyRate', 'TotalPrice', 'TotalPriceWithTax', 'DiscountWithoutTax', 'DiscountWithTax', 'AllowPartialUse', 'CreatedAt', 'UpdatedAt', ),
        self::TYPE_STUDLYPHPNAME => array('id', 'ref', 'invoiceRef', 'invoiceAddressId', 'invoiceDate', 'orderId', 'customerId', 'parentId', 'typeId', 'statusId', 'currencyId', 'currencyRate', 'totalPrice', 'totalPriceWithTax', 'discountWithoutTax', 'discountWithTax', 'allowPartialUse', 'createdAt', 'updatedAt', ),
        self::TYPE_COLNAME       => array(CreditNoteTableMap::ID, CreditNoteTableMap::REF, CreditNoteTableMap::INVOICE_REF, CreditNoteTableMap::INVOICE_ADDRESS_ID, CreditNoteTableMap::INVOICE_DATE, CreditNoteTableMap::ORDER_ID, CreditNoteTableMap::CUSTOMER_ID, CreditNoteTableMap::PARENT_ID, CreditNoteTableMap::TYPE_ID, CreditNoteTableMap::STATUS_ID, CreditNoteTableMap::CURRENCY_ID, CreditNoteTableMap::CURRENCY_RATE, CreditNoteTableMap::TOTAL_PRICE, CreditNoteTableMap::TOTAL_PRICE_WITH_TAX, CreditNoteTableMap::DISCOUNT_WITHOUT_TAX, CreditNoteTableMap::DISCOUNT_WITH_TAX, CreditNoteTableMap::ALLOW_PARTIAL_USE, CreditNoteTableMap::CREATED_AT, CreditNoteTableMap::UPDATED_AT, ),
        self::TYPE_RAW_COLNAME   => array('ID', 'REF', 'INVOICE_REF', 'INVOICE_ADDRESS_ID', 'INVOICE_DATE', 'ORDER_ID', 'CUSTOMER_ID', 'PARENT_ID', 'TYPE_ID', 'STATUS_ID', 'CURRENCY_ID', 'CURRENCY_RATE', 'TOTAL_PRICE', 'TOTAL_PRICE_WITH_TAX', 'DISCOUNT_WITHOUT_TAX', 'DISCOUNT_WITH_TAX', 'ALLOW_PARTIAL_USE', 'CREATED_AT', 'UPDATED_AT', ),
        self::TYPE_FIELDNAME     => array('id', 'ref', 'invoice_ref', 'invoice_address_id', 'invoice_date', 'order_id', 'customer_id', 'parent_id', 'type_id', 'status_id', 'currency_id', 'currency_rate', 'total_price', 'total_price_with_tax', 'discount_without_tax', 'discount_with_tax', 'allow_partial_use', 'created_at', 'updated_at', ),
        self::TYPE_NUM           => array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, )
    );

    /**
     * holds an array of keys for quick access to the fieldnames array
     *
     * first dimension keys are the type constants
     * e.g. self::$fieldKeys[self::TYPE_PHPNAME]['Id'] = 0
     */
    protected static $fieldKeys = array (
        self::TYPE_PHPNAME       => array('Id' => 0, 'Ref' => 1, 'InvoiceRef' => 2, 'InvoiceAddressId' => 3, 'InvoiceDate' => 4, 'OrderId' => 5, 'CustomerId' => 6, 'ParentId' => 7, 'TypeId' => 8, 'StatusId' => 9, 'CurrencyId' => 10, 'CurrencyRate' => 11, 'TotalPrice' => 12, 'TotalPriceWithTax' => 13, 'DiscountWithoutTax' => 14, 'DiscountWithTax' => 15, 'AllowPartialUse' => 16, 'CreatedAt' => 17, 'UpdatedAt' => 18, ),
        self::TYPE_STUDLYPHPNAME => array('id' => 0, 'ref' => 1, 'invoiceRef' => 2, 'invoiceAddressId' => 3, 'invoiceDate' => 4, 'orderId' => 5, 'customerId' => 6, 'parentId' => 7, 'typeId' => 8, 'statusId' => 9, 'currencyId' => 10, 'currencyRate' => 11, 'totalPrice' => 12, 'totalPriceWithTax' => 13, 'discountWithoutTax' => 14, 'discountWithTax' => 15, 'allowPartialUse' => 16, 'createdAt' => 17, 'updatedAt' => 18, ),
        self::TYPE_COLNAME       => array(CreditNoteTableMap::ID => 0, CreditNoteTableMap::REF => 1, CreditNoteTableMap::INVOICE_REF => 2, CreditNoteTableMap::INVOICE_ADDRESS_ID => 3, CreditNoteTableMap::INVOICE_DATE => 4, CreditNoteTableMap::ORDER_ID => 5, CreditNoteTableMap::CUSTOMER_ID => 6, CreditNoteTableMap::PARENT_ID => 7, CreditNoteTableMap::TYPE_ID => 8, CreditNoteTableMap::STATUS_ID => 9, CreditNoteTableMap::CURRENCY_ID => 10, CreditNoteTableMap::CURRENCY_RATE => 11, CreditNoteTableMap::TOTAL_PRICE => 12, CreditNoteTableMap::TOTAL_PRICE_WITH_TAX => 13, CreditNoteTableMap::DISCOUNT_WITHOUT_TAX => 14, CreditNoteTableMap::DISCOUNT_WITH_TAX => 15, CreditNoteTableMap::ALLOW_PARTIAL_USE => 16, CreditNoteTableMap::CREATED_AT => 17, CreditNoteTableMap::UPDATED_AT => 18, ),
        self::TYPE_RAW_COLNAME   => array('ID' => 0, 'REF' => 1, 'INVOICE_REF' => 2, 'INVOICE_ADDRESS_ID' => 3, 'INVOICE_DATE' => 4, 'ORDER_ID' => 5, 'CUSTOMER_ID' => 6, 'PARENT_ID' => 7, 'TYPE_ID' => 8, 'STATUS_ID' => 9, 'CURRENCY_ID' => 10, 'CURRENCY_RATE' => 11, 'TOTAL_PRICE' => 12, 'TOTAL_PRICE_WITH_TAX' => 13, 'DISCOUNT_WITHOUT_TAX' => 14, 'DISCOUNT_WITH_TAX' => 15, 'ALLOW_PARTIAL_USE' => 16, 'CREATED_AT' => 17, 'UPDATED_AT' => 18, ),
        self::TYPE_FIELDNAME     => array('id' => 0, 'ref' => 1, 'invoice_ref' => 2, 'invoice_address_id' => 3, 'invoice_date' => 4, 'order_id' => 5, 'customer_id' => 6, 'parent_id' => 7, 'type_id' => 8, 'status_id' => 9, 'currency_id' => 10, 'currency_rate' => 11, 'total_price' => 12, 'total_price_with_tax' => 13, 'discount_without_tax' => 14, 'discount_with_tax' => 15, 'allow_partial_use' => 16, 'created_at' => 17, 'updated_at' => 18, ),
        self::TYPE_NUM           => array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, )
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
        $this->setClassName('\\CreditNote\\Model\\CreditNote');
        $this->setPackage('CreditNote.Model');
        $this->setUseIdGenerator(true);
        // columns
        $this->addPrimaryKey('ID', 'Id', 'INTEGER', true, null, null);
        $this->addColumn('REF', 'Ref', 'VARCHAR', false, 45, null);
        $this->addColumn('INVOICE_REF', 'InvoiceRef', 'VARCHAR', false, 45, null);
        $this->addForeignKey('INVOICE_ADDRESS_ID', 'InvoiceAddressId', 'INTEGER', 'credit_note_address', 'ID', true, null, null);
        $this->addColumn('INVOICE_DATE', 'InvoiceDate', 'TIMESTAMP', false, null, null);
        $this->addForeignKey('ORDER_ID', 'OrderId', 'INTEGER', 'order', 'ID', false, null, null);
        $this->addForeignKey('CUSTOMER_ID', 'CustomerId', 'INTEGER', 'customer', 'ID', true, null, null);
        $this->addForeignKey('PARENT_ID', 'ParentId', 'INTEGER', 'credit_note', 'ID', false, null, null);
        $this->addForeignKey('TYPE_ID', 'TypeId', 'INTEGER', 'credit_note_type', 'ID', true, null, null);
        $this->addForeignKey('STATUS_ID', 'StatusId', 'INTEGER', 'credit_note_status', 'ID', true, null, null);
        $this->addForeignKey('CURRENCY_ID', 'CurrencyId', 'INTEGER', 'currency', 'ID', true, null, null);
        $this->addColumn('CURRENCY_RATE', 'CurrencyRate', 'FLOAT', false, null, null);
        $this->addColumn('TOTAL_PRICE', 'TotalPrice', 'DECIMAL', false, 16, 0);
        $this->addColumn('TOTAL_PRICE_WITH_TAX', 'TotalPriceWithTax', 'DECIMAL', false, 16, 0);
        $this->addColumn('DISCOUNT_WITHOUT_TAX', 'DiscountWithoutTax', 'DECIMAL', false, 16, 0);
        $this->addColumn('DISCOUNT_WITH_TAX', 'DiscountWithTax', 'DECIMAL', false, 16, 0);
        $this->addColumn('ALLOW_PARTIAL_USE', 'AllowPartialUse', 'BOOLEAN', false, 1, true);
        $this->addColumn('CREATED_AT', 'CreatedAt', 'TIMESTAMP', false, null, null);
        $this->addColumn('UPDATED_AT', 'UpdatedAt', 'TIMESTAMP', false, null, null);
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
        $this->addRelation('Order', '\\Thelia\\Model\\Order', RelationMap::MANY_TO_ONE, array('order_id' => 'id', ), 'RESTRICT', 'RESTRICT');
        $this->addRelation('Customer', '\\Thelia\\Model\\Customer', RelationMap::MANY_TO_ONE, array('customer_id' => 'id', ), 'RESTRICT', 'RESTRICT');
        $this->addRelation('CreditNoteRelatedByParentId', '\\CreditNote\\Model\\CreditNote', RelationMap::MANY_TO_ONE, array('parent_id' => 'id', ), 'RESTRICT', 'RESTRICT');
        $this->addRelation('CreditNoteType', '\\CreditNote\\Model\\CreditNoteType', RelationMap::MANY_TO_ONE, array('type_id' => 'id', ), 'RESTRICT', 'RESTRICT');
        $this->addRelation('CreditNoteStatus', '\\CreditNote\\Model\\CreditNoteStatus', RelationMap::MANY_TO_ONE, array('status_id' => 'id', ), 'RESTRICT', 'RESTRICT');
        $this->addRelation('Currency', '\\Thelia\\Model\\Currency', RelationMap::MANY_TO_ONE, array('currency_id' => 'id', ), 'RESTRICT', 'RESTRICT');
        $this->addRelation('CreditNoteAddress', '\\CreditNote\\Model\\CreditNoteAddress', RelationMap::MANY_TO_ONE, array('invoice_address_id' => 'id', ), 'RESTRICT', 'RESTRICT');
        $this->addRelation('CreditNoteRelatedById', '\\CreditNote\\Model\\CreditNote', RelationMap::ONE_TO_MANY, array('id' => 'parent_id', ), 'RESTRICT', 'RESTRICT', 'CreditNotesRelatedById');
        $this->addRelation('OrderCreditNote', '\\CreditNote\\Model\\OrderCreditNote', RelationMap::ONE_TO_MANY, array('id' => 'credit_note_id', ), 'CASCADE', 'RESTRICT', 'OrderCreditNotes');
        $this->addRelation('CartCreditNote', '\\CreditNote\\Model\\CartCreditNote', RelationMap::ONE_TO_MANY, array('id' => 'credit_note_id', ), 'CASCADE', 'RESTRICT', 'CartCreditNotes');
        $this->addRelation('CreditNoteDetail', '\\CreditNote\\Model\\CreditNoteDetail', RelationMap::ONE_TO_MANY, array('id' => 'credit_note_id', ), 'CASCADE', 'RESTRICT', 'CreditNoteDetails');
        $this->addRelation('CreditNoteComment', '\\CreditNote\\Model\\CreditNoteComment', RelationMap::ONE_TO_MANY, array('id' => 'credit_note_id', ), 'CASCADE', 'RESTRICT', 'CreditNoteComments');
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
            'timestampable' => array('create_column' => 'created_at', 'update_column' => 'updated_at', ),
        );
    } // getBehaviors()
    /**
     * Method to invalidate the instance pool of all tables related to credit_note     * by a foreign key with ON DELETE CASCADE
     */
    public static function clearRelatedInstancePool()
    {
        // Invalidate objects in ".$this->getClassNameFromBuilder($joinedTableTableMapBuilder)." instance pool,
        // since one or more of them may be deleted by ON DELETE CASCADE/SETNULL rule.
                OrderCreditNoteTableMap::clearInstancePool();
                CartCreditNoteTableMap::clearInstancePool();
                CreditNoteDetailTableMap::clearInstancePool();
                CreditNoteCommentTableMap::clearInstancePool();
            }

    /**
     * Retrieves a string version of the primary key from the DB resultset row that can be used to uniquely identify a row in this table.
     *
     * For tables with a single-column primary key, that simple pkey value will be returned.  For tables with
     * a multi-column primary key, a serialize()d version of the primary key will be returned.
     *
     * @param array  $row       resultset row.
     * @param int    $offset    The 0-based offset for reading from the resultset row.
     * @param string $indexType One of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_STUDLYPHPNAME
     *                           TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM
     */
    public static function getPrimaryKeyHashFromRow($row, $offset = 0, $indexType = TableMap::TYPE_NUM)
    {
        // If the PK cannot be derived from the row, return NULL.
        if ($row[TableMap::TYPE_NUM == $indexType ? 0 + $offset : static::translateFieldName('Id', TableMap::TYPE_PHPNAME, $indexType)] === null) {
            return null;
        }

        return (string) $row[TableMap::TYPE_NUM == $indexType ? 0 + $offset : static::translateFieldName('Id', TableMap::TYPE_PHPNAME, $indexType)];
    }

    /**
     * Retrieves the primary key from the DB resultset row
     * For tables with a single-column primary key, that simple pkey value will be returned.  For tables with
     * a multi-column primary key, an array of the primary key columns will be returned.
     *
     * @param array  $row       resultset row.
     * @param int    $offset    The 0-based offset for reading from the resultset row.
     * @param string $indexType One of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_STUDLYPHPNAME
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
                                 One of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_STUDLYPHPNAME
     *                           TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM.
     *
     * @throws PropelException Any exceptions caught during processing will be
     *         rethrown wrapped into a PropelException.
     * @return array (CreditNote object, last column rank)
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
     *         rethrown wrapped into a PropelException.
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
     *         rethrown wrapped into a PropelException.
     */
    public static function addSelectColumns(Criteria $criteria, $alias = null)
    {
        if (null === $alias) {
            $criteria->addSelectColumn(CreditNoteTableMap::ID);
            $criteria->addSelectColumn(CreditNoteTableMap::REF);
            $criteria->addSelectColumn(CreditNoteTableMap::INVOICE_REF);
            $criteria->addSelectColumn(CreditNoteTableMap::INVOICE_ADDRESS_ID);
            $criteria->addSelectColumn(CreditNoteTableMap::INVOICE_DATE);
            $criteria->addSelectColumn(CreditNoteTableMap::ORDER_ID);
            $criteria->addSelectColumn(CreditNoteTableMap::CUSTOMER_ID);
            $criteria->addSelectColumn(CreditNoteTableMap::PARENT_ID);
            $criteria->addSelectColumn(CreditNoteTableMap::TYPE_ID);
            $criteria->addSelectColumn(CreditNoteTableMap::STATUS_ID);
            $criteria->addSelectColumn(CreditNoteTableMap::CURRENCY_ID);
            $criteria->addSelectColumn(CreditNoteTableMap::CURRENCY_RATE);
            $criteria->addSelectColumn(CreditNoteTableMap::TOTAL_PRICE);
            $criteria->addSelectColumn(CreditNoteTableMap::TOTAL_PRICE_WITH_TAX);
            $criteria->addSelectColumn(CreditNoteTableMap::DISCOUNT_WITHOUT_TAX);
            $criteria->addSelectColumn(CreditNoteTableMap::DISCOUNT_WITH_TAX);
            $criteria->addSelectColumn(CreditNoteTableMap::ALLOW_PARTIAL_USE);
            $criteria->addSelectColumn(CreditNoteTableMap::CREATED_AT);
            $criteria->addSelectColumn(CreditNoteTableMap::UPDATED_AT);
        } else {
            $criteria->addSelectColumn($alias . '.ID');
            $criteria->addSelectColumn($alias . '.REF');
            $criteria->addSelectColumn($alias . '.INVOICE_REF');
            $criteria->addSelectColumn($alias . '.INVOICE_ADDRESS_ID');
            $criteria->addSelectColumn($alias . '.INVOICE_DATE');
            $criteria->addSelectColumn($alias . '.ORDER_ID');
            $criteria->addSelectColumn($alias . '.CUSTOMER_ID');
            $criteria->addSelectColumn($alias . '.PARENT_ID');
            $criteria->addSelectColumn($alias . '.TYPE_ID');
            $criteria->addSelectColumn($alias . '.STATUS_ID');
            $criteria->addSelectColumn($alias . '.CURRENCY_ID');
            $criteria->addSelectColumn($alias . '.CURRENCY_RATE');
            $criteria->addSelectColumn($alias . '.TOTAL_PRICE');
            $criteria->addSelectColumn($alias . '.TOTAL_PRICE_WITH_TAX');
            $criteria->addSelectColumn($alias . '.DISCOUNT_WITHOUT_TAX');
            $criteria->addSelectColumn($alias . '.DISCOUNT_WITH_TAX');
            $criteria->addSelectColumn($alias . '.ALLOW_PARTIAL_USE');
            $criteria->addSelectColumn($alias . '.CREATED_AT');
            $criteria->addSelectColumn($alias . '.UPDATED_AT');
        }
    }

    /**
     * Returns the TableMap related to this object.
     * This method is not needed for general use but a specific application could have a need.
     * @return TableMap
     * @throws PropelException Any exceptions caught during processing will be
     *         rethrown wrapped into a PropelException.
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
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).  This includes CASCADE-related rows
     *                if supported by native driver or if emulated using Propel.
     * @throws PropelException Any exceptions caught during processing will be
     *         rethrown wrapped into a PropelException.
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
            $criteria->add(CreditNoteTableMap::ID, (array) $values, Criteria::IN);
        }

        $query = CreditNoteQuery::create()->mergeWith($criteria);

        if ($values instanceof Criteria) { CreditNoteTableMap::clearInstancePool();
        } elseif (!is_object($values)) { // it's a primary key, or an array of pks
            foreach ((array) $values as $singleval) { CreditNoteTableMap::removeInstanceFromPool($singleval);
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
     *         rethrown wrapped into a PropelException.
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

        if ($criteria->containsKey(CreditNoteTableMap::ID) && $criteria->keyContainsValue(CreditNoteTableMap::ID) ) {
            throw new PropelException('Cannot insert a value for auto-increment primary key ('.CreditNoteTableMap::ID.')');
        }


        // Set the correct dbName
        $query = CreditNoteQuery::create()->mergeWith($criteria);

        try {
            // use transaction because $criteria could contain info
            // for more than one table (I guess, conceivably)
            $con->beginTransaction();
            $pk = $query->doInsert($con);
            $con->commit();
        } catch (PropelException $e) {
            $con->rollBack();
            throw $e;
        }

        return $pk;
    }

} // CreditNoteTableMap
// This is the static code needed to register the TableMap for this table with the main Propel class.
//
CreditNoteTableMap::buildTableMap();
