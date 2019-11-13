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
     * the column name for the ID field
     */
    const ID = 'credit_note_status_flow.ID';

    /**
     * the column name for the FROM_STATUS_ID field
     */
    const FROM_STATUS_ID = 'credit_note_status_flow.FROM_STATUS_ID';

    /**
     * the column name for the TO_STATUS_ID field
     */
    const TO_STATUS_ID = 'credit_note_status_flow.TO_STATUS_ID';

    /**
     * the column name for the PRIORITY field
     */
    const PRIORITY = 'credit_note_status_flow.PRIORITY';

    /**
     * the column name for the ROOT field
     */
    const ROOT = 'credit_note_status_flow.ROOT';

    /**
     * the column name for the CREATED_AT field
     */
    const CREATED_AT = 'credit_note_status_flow.CREATED_AT';

    /**
     * the column name for the UPDATED_AT field
     */
    const UPDATED_AT = 'credit_note_status_flow.UPDATED_AT';

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
        self::TYPE_STUDLYPHPNAME => array('id', 'fromStatusId', 'toStatusId', 'priority', 'root', 'createdAt', 'updatedAt', ),
        self::TYPE_COLNAME       => array(CreditNoteStatusFlowTableMap::ID, CreditNoteStatusFlowTableMap::FROM_STATUS_ID, CreditNoteStatusFlowTableMap::TO_STATUS_ID, CreditNoteStatusFlowTableMap::PRIORITY, CreditNoteStatusFlowTableMap::ROOT, CreditNoteStatusFlowTableMap::CREATED_AT, CreditNoteStatusFlowTableMap::UPDATED_AT, ),
        self::TYPE_RAW_COLNAME   => array('ID', 'FROM_STATUS_ID', 'TO_STATUS_ID', 'PRIORITY', 'ROOT', 'CREATED_AT', 'UPDATED_AT', ),
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
        self::TYPE_STUDLYPHPNAME => array('id' => 0, 'fromStatusId' => 1, 'toStatusId' => 2, 'priority' => 3, 'root' => 4, 'createdAt' => 5, 'updatedAt' => 6, ),
        self::TYPE_COLNAME       => array(CreditNoteStatusFlowTableMap::ID => 0, CreditNoteStatusFlowTableMap::FROM_STATUS_ID => 1, CreditNoteStatusFlowTableMap::TO_STATUS_ID => 2, CreditNoteStatusFlowTableMap::PRIORITY => 3, CreditNoteStatusFlowTableMap::ROOT => 4, CreditNoteStatusFlowTableMap::CREATED_AT => 5, CreditNoteStatusFlowTableMap::UPDATED_AT => 6, ),
        self::TYPE_RAW_COLNAME   => array('ID' => 0, 'FROM_STATUS_ID' => 1, 'TO_STATUS_ID' => 2, 'PRIORITY' => 3, 'ROOT' => 4, 'CREATED_AT' => 5, 'UPDATED_AT' => 6, ),
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
        $this->setClassName('\\CreditNote\\Model\\CreditNoteStatusFlow');
        $this->setPackage('CreditNote.Model');
        $this->setUseIdGenerator(true);
        // columns
        $this->addPrimaryKey('ID', 'Id', 'INTEGER', true, null, null);
        $this->addForeignKey('FROM_STATUS_ID', 'FromStatusId', 'INTEGER', 'credit_note_status', 'ID', true, null, null);
        $this->addForeignKey('TO_STATUS_ID', 'ToStatusId', 'INTEGER', 'credit_note_status', 'ID', true, null, null);
        $this->addColumn('PRIORITY', 'Priority', 'INTEGER', false, 11, null);
        $this->addColumn('ROOT', 'Root', 'BOOLEAN', true, 1, false);
        $this->addColumn('CREATED_AT', 'CreatedAt', 'TIMESTAMP', false, null, null);
        $this->addColumn('UPDATED_AT', 'UpdatedAt', 'TIMESTAMP', false, null, null);
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
        $this->addRelation('CreditNoteStatusRelatedByFromStatusId', '\\CreditNote\\Model\\CreditNoteStatus', RelationMap::MANY_TO_ONE, array('from_status_id' => 'id', ), 'CASCADE', 'RESTRICT');
        $this->addRelation('CreditNoteStatusRelatedByToStatusId', '\\CreditNote\\Model\\CreditNoteStatus', RelationMap::MANY_TO_ONE, array('to_status_id' => 'id', ), 'CASCADE', 'RESTRICT');
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
        return $withPrefix ? CreditNoteStatusFlowTableMap::CLASS_DEFAULT : CreditNoteStatusFlowTableMap::OM_CLASS;
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
     * @return array (CreditNoteStatusFlow object, last column rank)
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
     *         rethrown wrapped into a PropelException.
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
     *         rethrown wrapped into a PropelException.
     */
    public static function addSelectColumns(Criteria $criteria, $alias = null)
    {
        if (null === $alias) {
            $criteria->addSelectColumn(CreditNoteStatusFlowTableMap::ID);
            $criteria->addSelectColumn(CreditNoteStatusFlowTableMap::FROM_STATUS_ID);
            $criteria->addSelectColumn(CreditNoteStatusFlowTableMap::TO_STATUS_ID);
            $criteria->addSelectColumn(CreditNoteStatusFlowTableMap::PRIORITY);
            $criteria->addSelectColumn(CreditNoteStatusFlowTableMap::ROOT);
            $criteria->addSelectColumn(CreditNoteStatusFlowTableMap::CREATED_AT);
            $criteria->addSelectColumn(CreditNoteStatusFlowTableMap::UPDATED_AT);
        } else {
            $criteria->addSelectColumn($alias . '.ID');
            $criteria->addSelectColumn($alias . '.FROM_STATUS_ID');
            $criteria->addSelectColumn($alias . '.TO_STATUS_ID');
            $criteria->addSelectColumn($alias . '.PRIORITY');
            $criteria->addSelectColumn($alias . '.ROOT');
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
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).  This includes CASCADE-related rows
     *                if supported by native driver or if emulated using Propel.
     * @throws PropelException Any exceptions caught during processing will be
     *         rethrown wrapped into a PropelException.
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
            $criteria->add(CreditNoteStatusFlowTableMap::ID, (array) $values, Criteria::IN);
        }

        $query = CreditNoteStatusFlowQuery::create()->mergeWith($criteria);

        if ($values instanceof Criteria) { CreditNoteStatusFlowTableMap::clearInstancePool();
        } elseif (!is_object($values)) { // it's a primary key, or an array of pks
            foreach ((array) $values as $singleval) { CreditNoteStatusFlowTableMap::removeInstanceFromPool($singleval);
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
     *         rethrown wrapped into a PropelException.
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

        if ($criteria->containsKey(CreditNoteStatusFlowTableMap::ID) && $criteria->keyContainsValue(CreditNoteStatusFlowTableMap::ID) ) {
            throw new PropelException('Cannot insert a value for auto-increment primary key ('.CreditNoteStatusFlowTableMap::ID.')');
        }


        // Set the correct dbName
        $query = CreditNoteStatusFlowQuery::create()->mergeWith($criteria);

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

} // CreditNoteStatusFlowTableMap
// This is the static code needed to register the TableMap for this table with the main Propel class.
//
CreditNoteStatusFlowTableMap::buildTableMap();
