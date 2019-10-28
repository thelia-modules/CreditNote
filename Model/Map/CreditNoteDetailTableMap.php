<?php

namespace CreditNote\Model\Map;

use CreditNote\Model\CreditNoteDetail;
use CreditNote\Model\CreditNoteDetailQuery;
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
 * This class defines the structure of the 'credit_note_detail' table.
 *
 *
 *
 * This map class is used by Propel to do runtime db structure discovery.
 * For example, the createSelectSql() method checks the type of a given column used in an
 * ORDER BY clause to know whether it needs to apply SQL to make the ORDER BY case-insensitive
 * (i.e. if it's a text column type).
 *
 */
class CreditNoteDetailTableMap extends TableMap
{
    use InstancePoolTrait;
    use TableMapTrait;

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'CreditNote.Model.Map.CreditNoteDetailTableMap';

    /**
     * The default database name for this class
     */
    const DATABASE_NAME = 'thelia';

    /**
     * The table name for this class
     */
    const TABLE_NAME = 'credit_note_detail';

    /**
     * The related Propel class for this table
     */
    const OM_CLASS = '\\CreditNote\\Model\\CreditNoteDetail';

    /**
     * A class that can be returned by this tableMap
     */
    const CLASS_DEFAULT = 'CreditNote.Model.CreditNoteDetail';

    /**
     * The total number of columns
     */
    const NUM_COLUMNS = 11;

    /**
     * The number of lazy-loaded columns
     */
    const NUM_LAZY_LOAD_COLUMNS = 0;

    /**
     * The number of columns to hydrate (NUM_COLUMNS - NUM_LAZY_LOAD_COLUMNS)
     */
    const NUM_HYDRATE_COLUMNS = 11;

    /**
    * the column legacy name for the id field
    * @deprecated Legacy constant for compatibility. Use COL_ID.
    */
    const ID = 'credit_note_detail.id';
    /**
    * the column legacy name for the credit_note_id field
    * @deprecated Legacy constant for compatibility. Use COL_CREDIT_NOTE_ID.
    */
    const CREDIT_NOTE_ID = 'credit_note_detail.credit_note_id';
    /**
    * the column legacy name for the price field
    * @deprecated Legacy constant for compatibility. Use COL_PRICE.
    */
    const PRICE = 'credit_note_detail.price';
    /**
    * the column legacy name for the price_with_tax field
    * @deprecated Legacy constant for compatibility. Use COL_PRICE_WITH_TAX.
    */
    const PRICE_WITH_TAX = 'credit_note_detail.price_with_tax';
    /**
    * the column legacy name for the tax_rule_id field
    * @deprecated Legacy constant for compatibility. Use COL_TAX_RULE_ID.
    */
    const TAX_RULE_ID = 'credit_note_detail.tax_rule_id';
    /**
    * the column legacy name for the order_product_id field
    * @deprecated Legacy constant for compatibility. Use COL_ORDER_PRODUCT_ID.
    */
    const ORDER_PRODUCT_ID = 'credit_note_detail.order_product_id';
    /**
    * the column legacy name for the type field
    * @deprecated Legacy constant for compatibility. Use COL_TYPE.
    */
    const TYPE = 'credit_note_detail.type';
    /**
    * the column legacy name for the quantity field
    * @deprecated Legacy constant for compatibility. Use COL_QUANTITY.
    */
    const QUANTITY = 'credit_note_detail.quantity';
    /**
    * the column legacy name for the title field
    * @deprecated Legacy constant for compatibility. Use COL_TITLE.
    */
    const TITLE = 'credit_note_detail.title';
    /**
    * the column legacy name for the created_at field
    * @deprecated Legacy constant for compatibility. Use COL_CREATED_AT.
    */
    const CREATED_AT = 'credit_note_detail.created_at';
    /**
    * the column legacy name for the updated_at field
    * @deprecated Legacy constant for compatibility. Use COL_UPDATED_AT.
    */
    const UPDATED_AT = 'credit_note_detail.updated_at';
    /**
     * the column name for the id field
     */
    const COL_ID = 'credit_note_detail.id';
    /**
     * the column name for the credit_note_id field
     */
    const COL_CREDIT_NOTE_ID = 'credit_note_detail.credit_note_id';
    /**
     * the column name for the price field
     */
    const COL_PRICE = 'credit_note_detail.price';
    /**
     * the column name for the price_with_tax field
     */
    const COL_PRICE_WITH_TAX = 'credit_note_detail.price_with_tax';
    /**
     * the column name for the tax_rule_id field
     */
    const COL_TAX_RULE_ID = 'credit_note_detail.tax_rule_id';
    /**
     * the column name for the order_product_id field
     */
    const COL_ORDER_PRODUCT_ID = 'credit_note_detail.order_product_id';
    /**
     * the column name for the type field
     */
    const COL_TYPE = 'credit_note_detail.type';
    /**
     * the column name for the quantity field
     */
    const COL_QUANTITY = 'credit_note_detail.quantity';
    /**
     * the column name for the title field
     */
    const COL_TITLE = 'credit_note_detail.title';
    /**
     * the column name for the created_at field
     */
    const COL_CREATED_AT = 'credit_note_detail.created_at';
    /**
     * the column name for the updated_at field
     */
    const COL_UPDATED_AT = 'credit_note_detail.updated_at';

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
        self::TYPE_PHPNAME       => array('Id', 'CreditNoteId', 'Price', 'PriceWithTax', 'TaxRuleId', 'OrderProductId', 'Type', 'Quantity', 'Title', 'CreatedAt', 'UpdatedAt', ),
        self::TYPE_CAMELNAME     => array('id', 'creditNoteId', 'price', 'priceWithTax', 'taxRuleId', 'orderProductId', 'type', 'quantity', 'title', 'createdAt', 'updatedAt', ),
        self::TYPE_COLNAME       => array(CreditNoteDetailTableMap::COL_ID, CreditNoteDetailTableMap::COL_CREDIT_NOTE_ID, CreditNoteDetailTableMap::COL_PRICE, CreditNoteDetailTableMap::COL_PRICE_WITH_TAX, CreditNoteDetailTableMap::COL_TAX_RULE_ID, CreditNoteDetailTableMap::COL_ORDER_PRODUCT_ID, CreditNoteDetailTableMap::COL_TYPE, CreditNoteDetailTableMap::COL_QUANTITY, CreditNoteDetailTableMap::COL_TITLE, CreditNoteDetailTableMap::COL_CREATED_AT, CreditNoteDetailTableMap::COL_UPDATED_AT, ),
        self::TYPE_FIELDNAME     => array('id', 'credit_note_id', 'price', 'price_with_tax', 'tax_rule_id', 'order_product_id', 'type', 'quantity', 'title', 'created_at', 'updated_at', ),
        self::TYPE_NUM           => array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, )
    );

    /**
     * holds an array of keys for quick access to the fieldnames array
     *
     * first dimension keys are the type constants
     * e.g. self::$fieldKeys[self::TYPE_PHPNAME]['Id'] = 0
     */
    protected static $fieldKeys = array (
        self::TYPE_PHPNAME       => array('Id' => 0, 'CreditNoteId' => 1, 'Price' => 2, 'PriceWithTax' => 3, 'TaxRuleId' => 4, 'OrderProductId' => 5, 'Type' => 6, 'Quantity' => 7, 'Title' => 8, 'CreatedAt' => 9, 'UpdatedAt' => 10, ),
        self::TYPE_CAMELNAME     => array('id' => 0, 'creditNoteId' => 1, 'price' => 2, 'priceWithTax' => 3, 'taxRuleId' => 4, 'orderProductId' => 5, 'type' => 6, 'quantity' => 7, 'title' => 8, 'createdAt' => 9, 'updatedAt' => 10, ),
        self::TYPE_COLNAME       => array(CreditNoteDetailTableMap::COL_ID => 0, CreditNoteDetailTableMap::COL_CREDIT_NOTE_ID => 1, CreditNoteDetailTableMap::COL_PRICE => 2, CreditNoteDetailTableMap::COL_PRICE_WITH_TAX => 3, CreditNoteDetailTableMap::COL_TAX_RULE_ID => 4, CreditNoteDetailTableMap::COL_ORDER_PRODUCT_ID => 5, CreditNoteDetailTableMap::COL_TYPE => 6, CreditNoteDetailTableMap::COL_QUANTITY => 7, CreditNoteDetailTableMap::COL_TITLE => 8, CreditNoteDetailTableMap::COL_CREATED_AT => 9, CreditNoteDetailTableMap::COL_UPDATED_AT => 10, ),
        self::TYPE_FIELDNAME     => array('id' => 0, 'credit_note_id' => 1, 'price' => 2, 'price_with_tax' => 3, 'tax_rule_id' => 4, 'order_product_id' => 5, 'type' => 6, 'quantity' => 7, 'title' => 8, 'created_at' => 9, 'updated_at' => 10, ),
        self::TYPE_NUM           => array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, )
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
        $this->setName('credit_note_detail');
        $this->setPhpName('CreditNoteDetail');
        $this->setIdentifierQuoting(true);
        $this->setClassName('\\CreditNote\\Model\\CreditNoteDetail');
        $this->setPackage('CreditNote.Model');
        $this->setUseIdGenerator(true);
        // columns
        $this->addPrimaryKey('id', 'Id', 'INTEGER', true, null, null);
        $this->addForeignKey('credit_note_id', 'CreditNoteId', 'INTEGER', 'credit_note', 'id', true, null, null);
        $this->addColumn('price', 'Price', 'DECIMAL', false, 16, 0);
        $this->addColumn('price_with_tax', 'PriceWithTax', 'DECIMAL', false, 16, 0);
        $this->addForeignKey('tax_rule_id', 'TaxRuleId', 'INTEGER', 'tax_rule', 'id', false, null, null);
        $this->addForeignKey('order_product_id', 'OrderProductId', 'INTEGER', 'order_product', 'id', false, null, null);
        $this->addColumn('type', 'Type', 'VARCHAR', false, 55, null);
        $this->addColumn('quantity', 'Quantity', 'INTEGER', true, null, 0);
        $this->addColumn('title', 'Title', 'VARCHAR', false, 255, null);
        $this->addColumn('created_at', 'CreatedAt', 'TIMESTAMP', false, null, null);
        $this->addColumn('updated_at', 'UpdatedAt', 'TIMESTAMP', false, null, null);
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
        $this->addRelation('CreditNote', '\\CreditNote\\Model\\CreditNote', RelationMap::MANY_TO_ONE, array (
  0 =>
  array (
    0 => ':credit_note_id',
    1 => ':id',
  ),
), 'CASCADE', 'RESTRICT', null, false);
        $this->addRelation('OrderProduct', '\\Thelia\\Model\\OrderProduct', RelationMap::MANY_TO_ONE, array (
  0 =>
  array (
    0 => ':order_product_id',
    1 => ':id',
  ),
), 'RESTRICT', 'RESTRICT', null, false);
        $this->addRelation('TaxRule', '\\Thelia\\Model\\TaxRule', RelationMap::MANY_TO_ONE, array (
  0 =>
  array (
    0 => ':tax_rule_id',
    1 => ':id',
  ),
), 'RESTRICT', 'RESTRICT', null, false);
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
        );
    } // getBehaviors()

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
        return $withPrefix ? CreditNoteDetailTableMap::CLASS_DEFAULT : CreditNoteDetailTableMap::OM_CLASS;
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
     * @return array           (CreditNoteDetail object, last column rank)
     */
    public static function populateObject($row, $offset = 0, $indexType = TableMap::TYPE_NUM)
    {
        $key = CreditNoteDetailTableMap::getPrimaryKeyHashFromRow($row, $offset, $indexType);
        if (null !== ($obj = CreditNoteDetailTableMap::getInstanceFromPool($key))) {
            // We no longer rehydrate the object, since this can cause data loss.
            // See http://www.propelorm.org/ticket/509
            // $obj->hydrate($row, $offset, true); // rehydrate
            $col = $offset + CreditNoteDetailTableMap::NUM_HYDRATE_COLUMNS;
        } else {
            $cls = CreditNoteDetailTableMap::OM_CLASS;
            /** @var CreditNoteDetail $obj */
            $obj = new $cls();
            $col = $obj->hydrate($row, $offset, false, $indexType);
            CreditNoteDetailTableMap::addInstanceToPool($obj, $key);
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
            $key = CreditNoteDetailTableMap::getPrimaryKeyHashFromRow($row, 0, $dataFetcher->getIndexType());
            if (null !== ($obj = CreditNoteDetailTableMap::getInstanceFromPool($key))) {
                // We no longer rehydrate the object, since this can cause data loss.
                // See http://www.propelorm.org/ticket/509
                // $obj->hydrate($row, 0, true); // rehydrate
                $results[] = $obj;
            } else {
                /** @var CreditNoteDetail $obj */
                $obj = new $cls();
                $obj->hydrate($row);
                $results[] = $obj;
                CreditNoteDetailTableMap::addInstanceToPool($obj, $key);
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
            $criteria->addSelectColumn(CreditNoteDetailTableMap::COL_ID);
            $criteria->addSelectColumn(CreditNoteDetailTableMap::COL_CREDIT_NOTE_ID);
            $criteria->addSelectColumn(CreditNoteDetailTableMap::COL_PRICE);
            $criteria->addSelectColumn(CreditNoteDetailTableMap::COL_PRICE_WITH_TAX);
            $criteria->addSelectColumn(CreditNoteDetailTableMap::COL_TAX_RULE_ID);
            $criteria->addSelectColumn(CreditNoteDetailTableMap::COL_ORDER_PRODUCT_ID);
            $criteria->addSelectColumn(CreditNoteDetailTableMap::COL_TYPE);
            $criteria->addSelectColumn(CreditNoteDetailTableMap::COL_QUANTITY);
            $criteria->addSelectColumn(CreditNoteDetailTableMap::COL_TITLE);
            $criteria->addSelectColumn(CreditNoteDetailTableMap::COL_CREATED_AT);
            $criteria->addSelectColumn(CreditNoteDetailTableMap::COL_UPDATED_AT);
        } else {
            $criteria->addSelectColumn($alias . '.id');
            $criteria->addSelectColumn($alias . '.credit_note_id');
            $criteria->addSelectColumn($alias . '.price');
            $criteria->addSelectColumn($alias . '.price_with_tax');
            $criteria->addSelectColumn($alias . '.tax_rule_id');
            $criteria->addSelectColumn($alias . '.order_product_id');
            $criteria->addSelectColumn($alias . '.type');
            $criteria->addSelectColumn($alias . '.quantity');
            $criteria->addSelectColumn($alias . '.title');
            $criteria->addSelectColumn($alias . '.created_at');
            $criteria->addSelectColumn($alias . '.updated_at');
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
        return Propel::getServiceContainer()->getDatabaseMap(CreditNoteDetailTableMap::DATABASE_NAME)->getTable(CreditNoteDetailTableMap::TABLE_NAME);
    }

    /**
     * Add a TableMap instance to the database for this tableMap class.
     */
    public static function buildTableMap()
    {
        $dbMap = Propel::getServiceContainer()->getDatabaseMap(CreditNoteDetailTableMap::DATABASE_NAME);
        if (!$dbMap->hasTable(CreditNoteDetailTableMap::TABLE_NAME)) {
            $dbMap->addTableObject(new CreditNoteDetailTableMap());
        }
    }

    /**
     * Performs a DELETE on the database, given a CreditNoteDetail or Criteria object OR a primary key value.
     *
     * @param mixed               $values Criteria or CreditNoteDetail object or primary key or array of primary keys
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
            $con = Propel::getServiceContainer()->getWriteConnection(CreditNoteDetailTableMap::DATABASE_NAME);
        }

        if ($values instanceof Criteria) {
            // rename for clarity
            $criteria = $values;
        } elseif ($values instanceof \CreditNote\Model\CreditNoteDetail) { // it's a model object
            // create criteria based on pk values
            $criteria = $values->buildPkeyCriteria();
        } else { // it's a primary key, or an array of pks
            $criteria = new Criteria(CreditNoteDetailTableMap::DATABASE_NAME);
            $criteria->add(CreditNoteDetailTableMap::COL_ID, (array) $values, Criteria::IN);
        }

        $query = CreditNoteDetailQuery::create()->mergeWith($criteria);

        if ($values instanceof Criteria) {
            CreditNoteDetailTableMap::clearInstancePool();
        } elseif (!\is_object($values)) { // it's a primary key, or an array of pks
            foreach ((array) $values as $singleval) {
                CreditNoteDetailTableMap::removeInstanceFromPool($singleval);
            }
        }

        return $query->delete($con);
    }

    /**
     * Deletes all rows from the credit_note_detail table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public static function doDeleteAll(ConnectionInterface $con = null)
    {
        return CreditNoteDetailQuery::create()->doDeleteAll($con);
    }

    /**
     * Performs an INSERT on the database, given a CreditNoteDetail or Criteria object.
     *
     * @param mixed               $criteria Criteria or CreditNoteDetail object containing data that is used to create the INSERT statement.
     * @param ConnectionInterface $con the ConnectionInterface connection to use
     * @return mixed           The new primary key.
     * @throws PropelException Any exceptions caught during processing will be
     *                         rethrown wrapped into a PropelException.
     */
    public static function doInsert($criteria, ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(CreditNoteDetailTableMap::DATABASE_NAME);
        }

        if ($criteria instanceof Criteria) {
            $criteria = clone $criteria; // rename for clarity
        } else {
            $criteria = $criteria->buildCriteria(); // build Criteria from CreditNoteDetail object
        }

        if ($criteria->containsKey(CreditNoteDetailTableMap::COL_ID) && $criteria->keyContainsValue(CreditNoteDetailTableMap::COL_ID) ) {
            throw new PropelException('Cannot insert a value for auto-increment primary key ('.CreditNoteDetailTableMap::COL_ID.')');
        }


        // Set the correct dbName
        $query = CreditNoteDetailQuery::create()->mergeWith($criteria);

        // use transaction because $criteria could contain info
        // for more than one table (I guess, conceivably)
        return $con->transaction(function () use ($con, $query) {
            return $query->doInsert($con);
        });
    }

} // CreditNoteDetailTableMap
// This is the static code needed to register the TableMap for this table with the main Propel class.
//
CreditNoteDetailTableMap::buildTableMap();
