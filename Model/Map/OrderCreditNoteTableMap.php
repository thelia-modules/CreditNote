<?php

namespace CreditNote\Model\Map;

use CreditNote\Model\OrderCreditNote;
use CreditNote\Model\OrderCreditNoteQuery;
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
 * This class defines the structure of the 'order_credit_note' table.
 *
 *
 *
 * This map class is used by Propel to do runtime db structure discovery.
 * For example, the createSelectSql() method checks the type of a given column used in an
 * ORDER BY clause to know whether it needs to apply SQL to make the ORDER BY case-insensitive
 * (i.e. if it's a text column type).
 *
 */
class OrderCreditNoteTableMap extends TableMap
{
    use InstancePoolTrait;
    use TableMapTrait;

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'CreditNote.Model.Map.OrderCreditNoteTableMap';

    /**
     * The default database name for this class
     */
    const DATABASE_NAME = 'thelia';

    /**
     * The table name for this class
     */
    const TABLE_NAME = 'order_credit_note';

    /**
     * The related Propel class for this table
     */
    const OM_CLASS = '\\CreditNote\\Model\\OrderCreditNote';

    /**
     * A class that can be returned by this tableMap
     */
    const CLASS_DEFAULT = 'CreditNote.Model.OrderCreditNote';

    /**
     * The total number of columns
     */
    const NUM_COLUMNS = 5;

    /**
     * The number of lazy-loaded columns
     */
    const NUM_LAZY_LOAD_COLUMNS = 0;

    /**
     * The number of columns to hydrate (NUM_COLUMNS - NUM_LAZY_LOAD_COLUMNS)
     */
    const NUM_HYDRATE_COLUMNS = 5;

    /**
    * the column legacy name for the order_id field
    * @deprecated Legacy constant for compatibility. Use COL_ORDER_ID.
    */
    const ORDER_ID = 'order_credit_note.order_id';
    /**
    * the column legacy name for the credit_note_id field
    * @deprecated Legacy constant for compatibility. Use COL_CREDIT_NOTE_ID.
    */
    const CREDIT_NOTE_ID = 'order_credit_note.credit_note_id';
    /**
    * the column legacy name for the amount_price field
    * @deprecated Legacy constant for compatibility. Use COL_AMOUNT_PRICE.
    */
    const AMOUNT_PRICE = 'order_credit_note.amount_price';
    /**
    * the column legacy name for the created_at field
    * @deprecated Legacy constant for compatibility. Use COL_CREATED_AT.
    */
    const CREATED_AT = 'order_credit_note.created_at';
    /**
    * the column legacy name for the updated_at field
    * @deprecated Legacy constant for compatibility. Use COL_UPDATED_AT.
    */
    const UPDATED_AT = 'order_credit_note.updated_at';
    /**
     * the column name for the order_id field
     */
    const COL_ORDER_ID = 'order_credit_note.order_id';
    /**
     * the column name for the credit_note_id field
     */
    const COL_CREDIT_NOTE_ID = 'order_credit_note.credit_note_id';
    /**
     * the column name for the amount_price field
     */
    const COL_AMOUNT_PRICE = 'order_credit_note.amount_price';
    /**
     * the column name for the created_at field
     */
    const COL_CREATED_AT = 'order_credit_note.created_at';
    /**
     * the column name for the updated_at field
     */
    const COL_UPDATED_AT = 'order_credit_note.updated_at';

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
        self::TYPE_PHPNAME       => array('OrderId', 'CreditNoteId', 'AmountPrice', 'CreatedAt', 'UpdatedAt', ),
        self::TYPE_CAMELNAME     => array('orderId', 'creditNoteId', 'amountPrice', 'createdAt', 'updatedAt', ),
        self::TYPE_COLNAME       => array(OrderCreditNoteTableMap::COL_ORDER_ID, OrderCreditNoteTableMap::COL_CREDIT_NOTE_ID, OrderCreditNoteTableMap::COL_AMOUNT_PRICE, OrderCreditNoteTableMap::COL_CREATED_AT, OrderCreditNoteTableMap::COL_UPDATED_AT, ),
        self::TYPE_FIELDNAME     => array('order_id', 'credit_note_id', 'amount_price', 'created_at', 'updated_at', ),
        self::TYPE_NUM           => array(0, 1, 2, 3, 4, )
    );

    /**
     * holds an array of keys for quick access to the fieldnames array
     *
     * first dimension keys are the type constants
     * e.g. self::$fieldKeys[self::TYPE_PHPNAME]['Id'] = 0
     */
    protected static $fieldKeys = array (
        self::TYPE_PHPNAME       => array('OrderId' => 0, 'CreditNoteId' => 1, 'AmountPrice' => 2, 'CreatedAt' => 3, 'UpdatedAt' => 4, ),
        self::TYPE_CAMELNAME     => array('orderId' => 0, 'creditNoteId' => 1, 'amountPrice' => 2, 'createdAt' => 3, 'updatedAt' => 4, ),
        self::TYPE_COLNAME       => array(OrderCreditNoteTableMap::COL_ORDER_ID => 0, OrderCreditNoteTableMap::COL_CREDIT_NOTE_ID => 1, OrderCreditNoteTableMap::COL_AMOUNT_PRICE => 2, OrderCreditNoteTableMap::COL_CREATED_AT => 3, OrderCreditNoteTableMap::COL_UPDATED_AT => 4, ),
        self::TYPE_FIELDNAME     => array('order_id' => 0, 'credit_note_id' => 1, 'amount_price' => 2, 'created_at' => 3, 'updated_at' => 4, ),
        self::TYPE_NUM           => array(0, 1, 2, 3, 4, )
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
        $this->setName('order_credit_note');
        $this->setPhpName('OrderCreditNote');
        $this->setIdentifierQuoting(true);
        $this->setClassName('\\CreditNote\\Model\\OrderCreditNote');
        $this->setPackage('CreditNote.Model');
        $this->setUseIdGenerator(false);
        // columns
        $this->addForeignPrimaryKey('order_id', 'OrderId', 'INTEGER' , 'order', 'id', true, null, null);
        $this->addForeignPrimaryKey('credit_note_id', 'CreditNoteId', 'INTEGER' , 'credit_note', 'id', true, null, null);
        $this->addColumn('amount_price', 'AmountPrice', 'DECIMAL', false, 16, 0);
        $this->addColumn('created_at', 'CreatedAt', 'TIMESTAMP', false, null, null);
        $this->addColumn('updated_at', 'UpdatedAt', 'TIMESTAMP', false, null, null);
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
), 'CASCADE', 'RESTRICT', null, false);
        $this->addRelation('CreditNote', '\\CreditNote\\Model\\CreditNote', RelationMap::MANY_TO_ONE, array (
  0 =>
  array (
    0 => ':credit_note_id',
    1 => ':id',
  ),
), 'CASCADE', 'RESTRICT', null, false);
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
     * Adds an object to the instance pool.
     *
     * Propel keeps cached copies of objects in an instance pool when they are retrieved
     * from the database. In some cases you may need to explicitly add objects
     * to the cache in order to ensure that the same objects are always returned by find*()
     * and findPk*() calls.
     *
     * @param \CreditNote\Model\OrderCreditNote $obj A \CreditNote\Model\OrderCreditNote object.
     * @param string $key             (optional) key to use for instance map (for performance boost if key was already calculated externally).
     */
    public static function addInstanceToPool($obj, $key = null)
    {
        if (Propel::isInstancePoolingEnabled()) {
            if (null === $key) {
                $key = serialize([(null === $obj->getOrderId() || is_scalar($obj->getOrderId()) || is_callable([$obj->getOrderId(), '__toString']) ? (string) $obj->getOrderId() : $obj->getOrderId()), (null === $obj->getCreditNoteId() || is_scalar($obj->getCreditNoteId()) || is_callable([$obj->getCreditNoteId(), '__toString']) ? (string) $obj->getCreditNoteId() : $obj->getCreditNoteId())]);
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
     * @param mixed $value A \CreditNote\Model\OrderCreditNote object or a primary key value.
     */
    public static function removeInstanceFromPool($value)
    {
        if (Propel::isInstancePoolingEnabled() && null !== $value) {
            if (\is_object($value) && $value instanceof \CreditNote\Model\OrderCreditNote) {
                $key = serialize([(null === $value->getOrderId() || is_scalar($value->getOrderId()) || is_callable([$value->getOrderId(), '__toString']) ? (string) $value->getOrderId() : $value->getOrderId()), (null === $value->getCreditNoteId() || is_scalar($value->getCreditNoteId()) || is_callable([$value->getCreditNoteId(), '__toString']) ? (string) $value->getCreditNoteId() : $value->getCreditNoteId())]);

            } elseif (\is_array($value) && count($value) === 2) {
                // assume we've been passed a primary key";
                $key = serialize([(null === $value[0] || is_scalar($value[0]) || is_callable([$value[0], '__toString']) ? (string) $value[0] : $value[0]), (null === $value[1] || is_scalar($value[1]) || is_callable([$value[1], '__toString']) ? (string) $value[1] : $value[1])]);
            } elseif ($value instanceof Criteria) {
                self::$instances = [];

                return;
            } else {
                $e = new PropelException("Invalid value passed to removeInstanceFromPool().  Expected primary key or \CreditNote\Model\OrderCreditNote object; got " . (\is_object($value) ? \get_class($value) . ' object.' : var_export($value, true)));
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
        if ($row[TableMap::TYPE_NUM == $indexType ? 0 + $offset : static::translateFieldName('OrderId', TableMap::TYPE_PHPNAME, $indexType)] === null && $row[TableMap::TYPE_NUM == $indexType ? 1 + $offset : static::translateFieldName('CreditNoteId', TableMap::TYPE_PHPNAME, $indexType)] === null) {
            return null;
        }

        return serialize([(null === $row[TableMap::TYPE_NUM == $indexType ? 0 + $offset : static::translateFieldName('OrderId', TableMap::TYPE_PHPNAME, $indexType)] || is_scalar($row[TableMap::TYPE_NUM == $indexType ? 0 + $offset : static::translateFieldName('OrderId', TableMap::TYPE_PHPNAME, $indexType)]) || is_callable([$row[TableMap::TYPE_NUM == $indexType ? 0 + $offset : static::translateFieldName('OrderId', TableMap::TYPE_PHPNAME, $indexType)], '__toString']) ? (string) $row[TableMap::TYPE_NUM == $indexType ? 0 + $offset : static::translateFieldName('OrderId', TableMap::TYPE_PHPNAME, $indexType)] : $row[TableMap::TYPE_NUM == $indexType ? 0 + $offset : static::translateFieldName('OrderId', TableMap::TYPE_PHPNAME, $indexType)]), (null === $row[TableMap::TYPE_NUM == $indexType ? 1 + $offset : static::translateFieldName('CreditNoteId', TableMap::TYPE_PHPNAME, $indexType)] || is_scalar($row[TableMap::TYPE_NUM == $indexType ? 1 + $offset : static::translateFieldName('CreditNoteId', TableMap::TYPE_PHPNAME, $indexType)]) || is_callable([$row[TableMap::TYPE_NUM == $indexType ? 1 + $offset : static::translateFieldName('CreditNoteId', TableMap::TYPE_PHPNAME, $indexType)], '__toString']) ? (string) $row[TableMap::TYPE_NUM == $indexType ? 1 + $offset : static::translateFieldName('CreditNoteId', TableMap::TYPE_PHPNAME, $indexType)] : $row[TableMap::TYPE_NUM == $indexType ? 1 + $offset : static::translateFieldName('CreditNoteId', TableMap::TYPE_PHPNAME, $indexType)])]);
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
                : self::translateFieldName('OrderId', TableMap::TYPE_PHPNAME, $indexType)
        ];
        $pks[] = (int) $row[
            $indexType == TableMap::TYPE_NUM
                ? 1 + $offset
                : self::translateFieldName('CreditNoteId', TableMap::TYPE_PHPNAME, $indexType)
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
        return $withPrefix ? OrderCreditNoteTableMap::CLASS_DEFAULT : OrderCreditNoteTableMap::OM_CLASS;
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
     * @return array           (OrderCreditNote object, last column rank)
     */
    public static function populateObject($row, $offset = 0, $indexType = TableMap::TYPE_NUM)
    {
        $key = OrderCreditNoteTableMap::getPrimaryKeyHashFromRow($row, $offset, $indexType);
        if (null !== ($obj = OrderCreditNoteTableMap::getInstanceFromPool($key))) {
            // We no longer rehydrate the object, since this can cause data loss.
            // See http://www.propelorm.org/ticket/509
            // $obj->hydrate($row, $offset, true); // rehydrate
            $col = $offset + OrderCreditNoteTableMap::NUM_HYDRATE_COLUMNS;
        } else {
            $cls = OrderCreditNoteTableMap::OM_CLASS;
            /** @var OrderCreditNote $obj */
            $obj = new $cls();
            $col = $obj->hydrate($row, $offset, false, $indexType);
            OrderCreditNoteTableMap::addInstanceToPool($obj, $key);
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
            $key = OrderCreditNoteTableMap::getPrimaryKeyHashFromRow($row, 0, $dataFetcher->getIndexType());
            if (null !== ($obj = OrderCreditNoteTableMap::getInstanceFromPool($key))) {
                // We no longer rehydrate the object, since this can cause data loss.
                // See http://www.propelorm.org/ticket/509
                // $obj->hydrate($row, 0, true); // rehydrate
                $results[] = $obj;
            } else {
                /** @var OrderCreditNote $obj */
                $obj = new $cls();
                $obj->hydrate($row);
                $results[] = $obj;
                OrderCreditNoteTableMap::addInstanceToPool($obj, $key);
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
            $criteria->addSelectColumn(OrderCreditNoteTableMap::COL_ORDER_ID);
            $criteria->addSelectColumn(OrderCreditNoteTableMap::COL_CREDIT_NOTE_ID);
            $criteria->addSelectColumn(OrderCreditNoteTableMap::COL_AMOUNT_PRICE);
            $criteria->addSelectColumn(OrderCreditNoteTableMap::COL_CREATED_AT);
            $criteria->addSelectColumn(OrderCreditNoteTableMap::COL_UPDATED_AT);
        } else {
            $criteria->addSelectColumn($alias . '.order_id');
            $criteria->addSelectColumn($alias . '.credit_note_id');
            $criteria->addSelectColumn($alias . '.amount_price');
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
        return Propel::getServiceContainer()->getDatabaseMap(OrderCreditNoteTableMap::DATABASE_NAME)->getTable(OrderCreditNoteTableMap::TABLE_NAME);
    }

    /**
     * Add a TableMap instance to the database for this tableMap class.
     */
    public static function buildTableMap()
    {
        $dbMap = Propel::getServiceContainer()->getDatabaseMap(OrderCreditNoteTableMap::DATABASE_NAME);
        if (!$dbMap->hasTable(OrderCreditNoteTableMap::TABLE_NAME)) {
            $dbMap->addTableObject(new OrderCreditNoteTableMap());
        }
    }

    /**
     * Performs a DELETE on the database, given a OrderCreditNote or Criteria object OR a primary key value.
     *
     * @param mixed               $values Criteria or OrderCreditNote object or primary key or array of primary keys
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
            $con = Propel::getServiceContainer()->getWriteConnection(OrderCreditNoteTableMap::DATABASE_NAME);
        }

        if ($values instanceof Criteria) {
            // rename for clarity
            $criteria = $values;
        } elseif ($values instanceof \CreditNote\Model\OrderCreditNote) { // it's a model object
            // create criteria based on pk values
            $criteria = $values->buildPkeyCriteria();
        } else { // it's a primary key, or an array of pks
            $criteria = new Criteria(OrderCreditNoteTableMap::DATABASE_NAME);
            // primary key is composite; we therefore, expect
            // the primary key passed to be an array of pkey values
            if (count($values) == count($values, COUNT_RECURSIVE)) {
                // array is not multi-dimensional
                $values = array($values);
            }
            foreach ($values as $value) {
                $criterion = $criteria->getNewCriterion(OrderCreditNoteTableMap::COL_ORDER_ID, $value[0]);
                $criterion->addAnd($criteria->getNewCriterion(OrderCreditNoteTableMap::COL_CREDIT_NOTE_ID, $value[1]));
                $criteria->addOr($criterion);
            }
        }

        $query = OrderCreditNoteQuery::create()->mergeWith($criteria);

        if ($values instanceof Criteria) {
            OrderCreditNoteTableMap::clearInstancePool();
        } elseif (!\is_object($values)) { // it's a primary key, or an array of pks
            foreach ((array) $values as $singleval) {
                OrderCreditNoteTableMap::removeInstanceFromPool($singleval);
            }
        }

        return $query->delete($con);
    }

    /**
     * Deletes all rows from the order_credit_note table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public static function doDeleteAll(ConnectionInterface $con = null)
    {
        return OrderCreditNoteQuery::create()->doDeleteAll($con);
    }

    /**
     * Performs an INSERT on the database, given a OrderCreditNote or Criteria object.
     *
     * @param mixed               $criteria Criteria or OrderCreditNote object containing data that is used to create the INSERT statement.
     * @param ConnectionInterface $con the ConnectionInterface connection to use
     * @return mixed           The new primary key.
     * @throws PropelException Any exceptions caught during processing will be
     *                         rethrown wrapped into a PropelException.
     */
    public static function doInsert($criteria, ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(OrderCreditNoteTableMap::DATABASE_NAME);
        }

        if ($criteria instanceof Criteria) {
            $criteria = clone $criteria; // rename for clarity
        } else {
            $criteria = $criteria->buildCriteria(); // build Criteria from OrderCreditNote object
        }


        // Set the correct dbName
        $query = OrderCreditNoteQuery::create()->mergeWith($criteria);

        // use transaction because $criteria could contain info
        // for more than one table (I guess, conceivably)
        return $con->transaction(function () use ($con, $query) {
            return $query->doInsert($con);
        });
    }

} // OrderCreditNoteTableMap
// This is the static code needed to register the TableMap for this table with the main Propel class.
//
OrderCreditNoteTableMap::buildTableMap();
