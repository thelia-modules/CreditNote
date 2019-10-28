<?php

namespace CreditNote\Model\Map;

use CreditNote\Model\CreditNoteStatusFlow;
use CreditNote\Model\CreditNoteStatusFlowQuery;
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
 * This class defines the structure of the 'credit_note_status_flow' table.
 *
 *
 *
 * This map class is used by Propel to do runtime db structure discovery.
 * For example, the createSelectSql() method checks the type of a given column used in an
 * ORDER BY clause to know whether it needs to apply SQL to make the ORDER BY case-insensitive
 * (i.e. if it's a text column type).
 *
 */
class CreditNoteStatusFlowTableMap extends TableMap
{
    use InstancePoolTrait;
    use TableMapTrait;

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'CreditNote.Model.Map.CreditNoteStatusFlowTableMap';

    /**
     * The default database name for this class
     */
    const DATABASE_NAME = 'thelia';

    /**
     * The table name for this class
     */
    const TABLE_NAME = 'credit_note_status_flow';

    /**
     * The related Propel class for this table
     */
    const OM_CLASS = '\\CreditNote\\Model\\CreditNoteStatusFlow';

    /**
     * A class that can be returned by this tableMap
     */
    const CLASS_DEFAULT = 'CreditNote.Model.CreditNoteStatusFlow';

    /**
     * The total number of columns
     */
    const NUM_COLUMNS = 7;

    /**
     * The number of lazy-loaded columns
     */
    const NUM_LAZY_LOAD_COLUMNS = 0;

    /**
     * The number of columns to hydrate (NUM_COLUMNS - NUM_LAZY_LOAD_COLUMNS)
     */
    const NUM_HYDRATE_COLUMNS = 7;

    /**
    * the column legacy name for the id field
    * @deprecated Legacy constant for compatibility. Use COL_ID.
    */
    const ID = 'credit_note_status_flow.id';
    /**
    * the column legacy name for the from_status_id field
    * @deprecated Legacy constant for compatibility. Use COL_FROM_STATUS_ID.
    */
    const FROM_STATUS_ID = 'credit_note_status_flow.from_status_id';
    /**
    * the column legacy name for the to_status_id field
    * @deprecated Legacy constant for compatibility. Use COL_TO_STATUS_ID.
    */
    const TO_STATUS_ID = 'credit_note_status_flow.to_status_id';
    /**
    * the column legacy name for the priority field
    * @deprecated Legacy constant for compatibility. Use COL_PRIORITY.
    */
    const PRIORITY = 'credit_note_status_flow.priority';
    /**
    * the column legacy name for the root field
    * @deprecated Legacy constant for compatibility. Use COL_ROOT.
    */
    const ROOT = 'credit_note_status_flow.root';
    /**
    * the column legacy name for the created_at field
    * @deprecated Legacy constant for compatibility. Use COL_CREATED_AT.
    */
    const CREATED_AT = 'credit_note_status_flow.created_at';
    /**
    * the column legacy name for the updated_at field
    * @deprecated Legacy constant for compatibility. Use COL_UPDATED_AT.
    */
    const UPDATED_AT = 'credit_note_status_flow.updated_at';
    /**
     * the column name for the id field
     */
    const COL_ID = 'credit_note_status_flow.id';
    /**
     * the column name for the from_status_id field
     */
    const COL_FROM_STATUS_ID = 'credit_note_status_flow.from_status_id';
    /**
     * the column name for the to_status_id field
     */
    const COL_TO_STATUS_ID = 'credit_note_status_flow.to_status_id';
    /**
     * the column name for the priority field
     */
    const COL_PRIORITY = 'credit_note_status_flow.priority';
    /**
     * the column name for the root field
     */
    const COL_ROOT = 'credit_note_status_flow.root';
    /**
     * the column name for the created_at field
     */
    const COL_CREATED_AT = 'credit_note_status_flow.created_at';
    /**
     * the column name for the updated_at field
     */
    const COL_UPDATED_AT = 'credit_note_status_flow.updated_at';

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
        self::TYPE_PHPNAME       => array('Id', 'FromStatusId', 'ToStatusId', 'Priority', 'Root', 'CreatedAt', 'UpdatedAt', ),
        self::TYPE_CAMELNAME     => array('id', 'fromStatusId', 'toStatusId', 'priority', 'root', 'createdAt', 'updatedAt', ),
        self::TYPE_COLNAME       => array(CreditNoteStatusFlowTableMap::COL_ID, CreditNoteStatusFlowTableMap::COL_FROM_STATUS_ID, CreditNoteStatusFlowTableMap::COL_TO_STATUS_ID, CreditNoteStatusFlowTableMap::COL_PRIORITY, CreditNoteStatusFlowTableMap::COL_ROOT, CreditNoteStatusFlowTableMap::COL_CREATED_AT, CreditNoteStatusFlowTableMap::COL_UPDATED_AT, ),
        self::TYPE_FIELDNAME     => array('id', 'from_status_id', 'to_status_id', 'priority', 'root', 'created_at', 'updated_at', ),
        self::TYPE_NUM           => array(0, 1, 2, 3, 4, 5, 6, )
    );

    /**
     * holds an array of keys for quick access to the fieldnames array
     *
     * first dimension keys are the type constants
     * e.g. self::$fieldKeys[self::TYPE_PHPNAME]['Id'] = 0
     */
    protected static $fieldKeys = array (
        self::TYPE_PHPNAME       => array('Id' => 0, 'FromStatusId' => 1, 'ToStatusId' => 2, 'Priority' => 3, 'Root' => 4, 'CreatedAt' => 5, 'UpdatedAt' => 6, ),
        self::TYPE_CAMELNAME     => array('id' => 0, 'fromStatusId' => 1, 'toStatusId' => 2, 'priority' => 3, 'root' => 4, 'createdAt' => 5, 'updatedAt' => 6, ),
        self::TYPE_COLNAME       => array(CreditNoteStatusFlowTableMap::COL_ID => 0, CreditNoteStatusFlowTableMap::COL_FROM_STATUS_ID => 1, CreditNoteStatusFlowTableMap::COL_TO_STATUS_ID => 2, CreditNoteStatusFlowTableMap::COL_PRIORITY => 3, CreditNoteStatusFlowTableMap::COL_ROOT => 4, CreditNoteStatusFlowTableMap::COL_CREATED_AT => 5, CreditNoteStatusFlowTableMap::COL_UPDATED_AT => 6, ),
        self::TYPE_FIELDNAME     => array('id' => 0, 'from_status_id' => 1, 'to_status_id' => 2, 'priority' => 3, 'root' => 4, 'created_at' => 5, 'updated_at' => 6, ),
        self::TYPE_NUM           => array(0, 1, 2, 3, 4, 5, 6, )
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
        $this->setName('credit_note_status_flow');
        $this->setPhpName('CreditNoteStatusFlow');
        $this->setIdentifierQuoting(true);
        $this->setClassName('\\CreditNote\\Model\\CreditNoteStatusFlow');
        $this->setPackage('CreditNote.Model');
        $this->setUseIdGenerator(true);
        // columns
        $this->addPrimaryKey('id', 'Id', 'INTEGER', true, null, null);
        $this->addForeignKey('from_status_id', 'FromStatusId', 'INTEGER', 'credit_note_status', 'id', true, null, null);
        $this->addForeignKey('to_status_id', 'ToStatusId', 'INTEGER', 'credit_note_status', 'id', true, null, null);
        $this->addColumn('priority', 'Priority', 'INTEGER', false, 11, null);
        $this->addColumn('root', 'Root', 'BOOLEAN', true, 1, false);
        $this->addColumn('created_at', 'CreatedAt', 'TIMESTAMP', false, null, null);
        $this->addColumn('updated_at', 'UpdatedAt', 'TIMESTAMP', false, null, null);
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
        $this->addRelation('CreditNoteStatusRelatedByFromStatusId', '\\CreditNote\\Model\\CreditNoteStatus', RelationMap::MANY_TO_ONE, array (
  0 =>
  array (
    0 => ':from_status_id',
    1 => ':id',
  ),
), 'CASCADE', 'RESTRICT', null, false);
        $this->addRelation('CreditNoteStatusRelatedByToStatusId', '\\CreditNote\\Model\\CreditNoteStatus', RelationMap::MANY_TO_ONE, array (
  0 =>
  array (
    0 => ':to_status_id',
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
        return $withPrefix ? CreditNoteStatusFlowTableMap::CLASS_DEFAULT : CreditNoteStatusFlowTableMap::OM_CLASS;
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
     * @return array           (CreditNoteStatusFlow object, last column rank)
     */
    public static function populateObject($row, $offset = 0, $indexType = TableMap::TYPE_NUM)
    {
        $key = CreditNoteStatusFlowTableMap::getPrimaryKeyHashFromRow($row, $offset, $indexType);
        if (null !== ($obj = CreditNoteStatusFlowTableMap::getInstanceFromPool($key))) {
            // We no longer rehydrate the object, since this can cause data loss.
            // See http://www.propelorm.org/ticket/509
            // $obj->hydrate($row, $offset, true); // rehydrate
            $col = $offset + CreditNoteStatusFlowTableMap::NUM_HYDRATE_COLUMNS;
        } else {
            $cls = CreditNoteStatusFlowTableMap::OM_CLASS;
            /** @var CreditNoteStatusFlow $obj */
            $obj = new $cls();
            $col = $obj->hydrate($row, $offset, false, $indexType);
            CreditNoteStatusFlowTableMap::addInstanceToPool($obj, $key);
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
            $key = CreditNoteStatusFlowTableMap::getPrimaryKeyHashFromRow($row, 0, $dataFetcher->getIndexType());
            if (null !== ($obj = CreditNoteStatusFlowTableMap::getInstanceFromPool($key))) {
                // We no longer rehydrate the object, since this can cause data loss.
                // See http://www.propelorm.org/ticket/509
                // $obj->hydrate($row, 0, true); // rehydrate
                $results[] = $obj;
            } else {
                /** @var CreditNoteStatusFlow $obj */
                $obj = new $cls();
                $obj->hydrate($row);
                $results[] = $obj;
                CreditNoteStatusFlowTableMap::addInstanceToPool($obj, $key);
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
            $criteria->addSelectColumn(CreditNoteStatusFlowTableMap::COL_ID);
            $criteria->addSelectColumn(CreditNoteStatusFlowTableMap::COL_FROM_STATUS_ID);
            $criteria->addSelectColumn(CreditNoteStatusFlowTableMap::COL_TO_STATUS_ID);
            $criteria->addSelectColumn(CreditNoteStatusFlowTableMap::COL_PRIORITY);
            $criteria->addSelectColumn(CreditNoteStatusFlowTableMap::COL_ROOT);
            $criteria->addSelectColumn(CreditNoteStatusFlowTableMap::COL_CREATED_AT);
            $criteria->addSelectColumn(CreditNoteStatusFlowTableMap::COL_UPDATED_AT);
        } else {
            $criteria->addSelectColumn($alias . '.id');
            $criteria->addSelectColumn($alias . '.from_status_id');
            $criteria->addSelectColumn($alias . '.to_status_id');
            $criteria->addSelectColumn($alias . '.priority');
            $criteria->addSelectColumn($alias . '.root');
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
        return Propel::getServiceContainer()->getDatabaseMap(CreditNoteStatusFlowTableMap::DATABASE_NAME)->getTable(CreditNoteStatusFlowTableMap::TABLE_NAME);
    }

    /**
     * Add a TableMap instance to the database for this tableMap class.
     */
    public static function buildTableMap()
    {
        $dbMap = Propel::getServiceContainer()->getDatabaseMap(CreditNoteStatusFlowTableMap::DATABASE_NAME);
        if (!$dbMap->hasTable(CreditNoteStatusFlowTableMap::TABLE_NAME)) {
            $dbMap->addTableObject(new CreditNoteStatusFlowTableMap());
        }
    }

    /**
     * Performs a DELETE on the database, given a CreditNoteStatusFlow or Criteria object OR a primary key value.
     *
     * @param mixed               $values Criteria or CreditNoteStatusFlow object or primary key or array of primary keys
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
            $con = Propel::getServiceContainer()->getWriteConnection(CreditNoteStatusFlowTableMap::DATABASE_NAME);
        }

        if ($values instanceof Criteria) {
            // rename for clarity
            $criteria = $values;
        } elseif ($values instanceof \CreditNote\Model\CreditNoteStatusFlow) { // it's a model object
            // create criteria based on pk values
            $criteria = $values->buildPkeyCriteria();
        } else { // it's a primary key, or an array of pks
            $criteria = new Criteria(CreditNoteStatusFlowTableMap::DATABASE_NAME);
            $criteria->add(CreditNoteStatusFlowTableMap::COL_ID, (array) $values, Criteria::IN);
        }

        $query = CreditNoteStatusFlowQuery::create()->mergeWith($criteria);

        if ($values instanceof Criteria) {
            CreditNoteStatusFlowTableMap::clearInstancePool();
        } elseif (!\is_object($values)) { // it's a primary key, or an array of pks
            foreach ((array) $values as $singleval) {
                CreditNoteStatusFlowTableMap::removeInstanceFromPool($singleval);
            }
        }

        return $query->delete($con);
    }

    /**
     * Deletes all rows from the credit_note_status_flow table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public static function doDeleteAll(ConnectionInterface $con = null)
    {
        return CreditNoteStatusFlowQuery::create()->doDeleteAll($con);
    }

    /**
     * Performs an INSERT on the database, given a CreditNoteStatusFlow or Criteria object.
     *
     * @param mixed               $criteria Criteria or CreditNoteStatusFlow object containing data that is used to create the INSERT statement.
     * @param ConnectionInterface $con the ConnectionInterface connection to use
     * @return mixed           The new primary key.
     * @throws PropelException Any exceptions caught during processing will be
     *                         rethrown wrapped into a PropelException.
     */
    public static function doInsert($criteria, ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(CreditNoteStatusFlowTableMap::DATABASE_NAME);
        }

        if ($criteria instanceof Criteria) {
            $criteria = clone $criteria; // rename for clarity
        } else {
            $criteria = $criteria->buildCriteria(); // build Criteria from CreditNoteStatusFlow object
        }

        if ($criteria->containsKey(CreditNoteStatusFlowTableMap::COL_ID) && $criteria->keyContainsValue(CreditNoteStatusFlowTableMap::COL_ID) ) {
            throw new PropelException('Cannot insert a value for auto-increment primary key ('.CreditNoteStatusFlowTableMap::COL_ID.')');
        }


        // Set the correct dbName
        $query = CreditNoteStatusFlowQuery::create()->mergeWith($criteria);

        // use transaction because $criteria could contain info
        // for more than one table (I guess, conceivably)
        return $con->transaction(function () use ($con, $query) {
            return $query->doInsert($con);
        });
    }

} // CreditNoteStatusFlowTableMap
// This is the static code needed to register the TableMap for this table with the main Propel class.
//
CreditNoteStatusFlowTableMap::buildTableMap();
