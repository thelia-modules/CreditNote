<?php

namespace CreditNote\Model\Map;

use CreditNote\Model\CreditNoteStatus;
use CreditNote\Model\CreditNoteStatusQuery;
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
 * This class defines the structure of the 'credit_note_status' table.
 *
 *
 *
 * This map class is used by Propel to do runtime db structure discovery.
 * For example, the createSelectSql() method checks the type of a given column used in an
 * ORDER BY clause to know whether it needs to apply SQL to make the ORDER BY case-insensitive
 * (i.e. if it's a text column type).
 *
 */
class CreditNoteStatusTableMap extends TableMap
{
    use InstancePoolTrait;
    use TableMapTrait;
    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'CreditNote.Model.Map.CreditNoteStatusTableMap';

    /**
     * The default database name for this class
     */
    const DATABASE_NAME = 'thelia';

    /**
     * The table name for this class
     */
    const TABLE_NAME = 'credit_note_status';

    /**
     * The related Propel class for this table
     */
    const OM_CLASS = '\\CreditNote\\Model\\CreditNoteStatus';

    /**
     * A class that can be returned by this tableMap
     */
    const CLASS_DEFAULT = 'CreditNote.Model.CreditNoteStatus';

    /**
     * The total number of columns
     */
    const NUM_COLUMNS = 8;

    /**
     * The number of lazy-loaded columns
     */
    const NUM_LAZY_LOAD_COLUMNS = 0;

    /**
     * The number of columns to hydrate (NUM_COLUMNS - NUM_LAZY_LOAD_COLUMNS)
     */
    const NUM_HYDRATE_COLUMNS = 8;

    /**
     * the column name for the ID field
     */
    const ID = 'credit_note_status.ID';

    /**
     * the column name for the CODE field
     */
    const CODE = 'credit_note_status.CODE';

    /**
     * the column name for the COLOR field
     */
    const COLOR = 'credit_note_status.COLOR';

    /**
     * the column name for the INVOICED field
     */
    const INVOICED = 'credit_note_status.INVOICED';

    /**
     * the column name for the USED field
     */
    const USED = 'credit_note_status.USED';

    /**
     * the column name for the POSITION field
     */
    const POSITION = 'credit_note_status.POSITION';

    /**
     * the column name for the CREATED_AT field
     */
    const CREATED_AT = 'credit_note_status.CREATED_AT';

    /**
     * the column name for the UPDATED_AT field
     */
    const UPDATED_AT = 'credit_note_status.UPDATED_AT';

    /**
     * The default string format for model objects of the related table
     */
    const DEFAULT_STRING_FORMAT = 'YAML';

    // i18n behavior

    /**
     * The default locale to use for translations.
     *
     * @var string
     */
    const DEFAULT_LOCALE = 'en_US';

    /**
     * holds an array of fieldnames
     *
     * first dimension keys are the type constants
     * e.g. self::$fieldNames[self::TYPE_PHPNAME][0] = 'Id'
     */
    protected static $fieldNames = array (
        self::TYPE_PHPNAME       => array('Id', 'Code', 'Color', 'Invoiced', 'Used', 'Position', 'CreatedAt', 'UpdatedAt', ),
        self::TYPE_STUDLYPHPNAME => array('id', 'code', 'color', 'invoiced', 'used', 'position', 'createdAt', 'updatedAt', ),
        self::TYPE_COLNAME       => array(CreditNoteStatusTableMap::ID, CreditNoteStatusTableMap::CODE, CreditNoteStatusTableMap::COLOR, CreditNoteStatusTableMap::INVOICED, CreditNoteStatusTableMap::USED, CreditNoteStatusTableMap::POSITION, CreditNoteStatusTableMap::CREATED_AT, CreditNoteStatusTableMap::UPDATED_AT, ),
        self::TYPE_RAW_COLNAME   => array('ID', 'CODE', 'COLOR', 'INVOICED', 'USED', 'POSITION', 'CREATED_AT', 'UPDATED_AT', ),
        self::TYPE_FIELDNAME     => array('id', 'code', 'color', 'invoiced', 'used', 'position', 'created_at', 'updated_at', ),
        self::TYPE_NUM           => array(0, 1, 2, 3, 4, 5, 6, 7, )
    );

    /**
     * holds an array of keys for quick access to the fieldnames array
     *
     * first dimension keys are the type constants
     * e.g. self::$fieldKeys[self::TYPE_PHPNAME]['Id'] = 0
     */
    protected static $fieldKeys = array (
        self::TYPE_PHPNAME       => array('Id' => 0, 'Code' => 1, 'Color' => 2, 'Invoiced' => 3, 'Used' => 4, 'Position' => 5, 'CreatedAt' => 6, 'UpdatedAt' => 7, ),
        self::TYPE_STUDLYPHPNAME => array('id' => 0, 'code' => 1, 'color' => 2, 'invoiced' => 3, 'used' => 4, 'position' => 5, 'createdAt' => 6, 'updatedAt' => 7, ),
        self::TYPE_COLNAME       => array(CreditNoteStatusTableMap::ID => 0, CreditNoteStatusTableMap::CODE => 1, CreditNoteStatusTableMap::COLOR => 2, CreditNoteStatusTableMap::INVOICED => 3, CreditNoteStatusTableMap::USED => 4, CreditNoteStatusTableMap::POSITION => 5, CreditNoteStatusTableMap::CREATED_AT => 6, CreditNoteStatusTableMap::UPDATED_AT => 7, ),
        self::TYPE_RAW_COLNAME   => array('ID' => 0, 'CODE' => 1, 'COLOR' => 2, 'INVOICED' => 3, 'USED' => 4, 'POSITION' => 5, 'CREATED_AT' => 6, 'UPDATED_AT' => 7, ),
        self::TYPE_FIELDNAME     => array('id' => 0, 'code' => 1, 'color' => 2, 'invoiced' => 3, 'used' => 4, 'position' => 5, 'created_at' => 6, 'updated_at' => 7, ),
        self::TYPE_NUM           => array(0, 1, 2, 3, 4, 5, 6, 7, )
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
        $this->setName('credit_note_status');
        $this->setPhpName('CreditNoteStatus');
        $this->setClassName('\\CreditNote\\Model\\CreditNoteStatus');
        $this->setPackage('CreditNote.Model');
        $this->setUseIdGenerator(true);
        // columns
        $this->addPrimaryKey('ID', 'Id', 'INTEGER', true, null, null);
        $this->addColumn('CODE', 'Code', 'VARCHAR', false, 45, null);
        $this->addColumn('COLOR', 'Color', 'CHAR', false, 7, null);
        $this->addColumn('INVOICED', 'Invoiced', 'BOOLEAN', true, 1, false);
        $this->addColumn('USED', 'Used', 'BOOLEAN', true, 1, false);
        $this->addColumn('POSITION', 'Position', 'INTEGER', false, 11, null);
        $this->addColumn('CREATED_AT', 'CreatedAt', 'TIMESTAMP', false, null, null);
        $this->addColumn('UPDATED_AT', 'UpdatedAt', 'TIMESTAMP', false, null, null);
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
        $this->addRelation('CreditNote', '\\CreditNote\\Model\\CreditNote', RelationMap::ONE_TO_MANY, array('id' => 'status_id', ), 'RESTRICT', 'RESTRICT', 'CreditNotes');
        $this->addRelation('CreditNoteStatusFlowRelatedByFromStatusId', '\\CreditNote\\Model\\CreditNoteStatusFlow', RelationMap::ONE_TO_MANY, array('id' => 'from_status_id', ), 'CASCADE', 'RESTRICT', 'CreditNoteStatusFlowsRelatedByFromStatusId');
        $this->addRelation('CreditNoteStatusFlowRelatedByToStatusId', '\\CreditNote\\Model\\CreditNoteStatusFlow', RelationMap::ONE_TO_MANY, array('id' => 'to_status_id', ), 'CASCADE', 'RESTRICT', 'CreditNoteStatusFlowsRelatedByToStatusId');
        $this->addRelation('CreditNoteStatusI18n', '\\CreditNote\\Model\\CreditNoteStatusI18n', RelationMap::ONE_TO_MANY, array('id' => 'id', ), 'CASCADE', null, 'CreditNoteStatusI18ns');
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
            'i18n' => array('i18n_table' => '%TABLE%_i18n', 'i18n_phpname' => '%PHPNAME%I18n', 'i18n_columns' => 'title, description, chapo, postscriptum', 'locale_column' => 'locale', 'locale_length' => '5', 'default_locale' => '', 'locale_alias' => '', ),
            'timestampable' => array('create_column' => 'created_at', 'update_column' => 'updated_at', ),
        );
    } // getBehaviors()
    /**
     * Method to invalidate the instance pool of all tables related to credit_note_status     * by a foreign key with ON DELETE CASCADE
     */
    public static function clearRelatedInstancePool()
    {
        // Invalidate objects in ".$this->getClassNameFromBuilder($joinedTableTableMapBuilder)." instance pool,
        // since one or more of them may be deleted by ON DELETE CASCADE/SETNULL rule.
                CreditNoteStatusFlowTableMap::clearInstancePool();
                CreditNoteStatusI18nTableMap::clearInstancePool();
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
        return $withPrefix ? CreditNoteStatusTableMap::CLASS_DEFAULT : CreditNoteStatusTableMap::OM_CLASS;
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
     * @return array (CreditNoteStatus object, last column rank)
     */
    public static function populateObject($row, $offset = 0, $indexType = TableMap::TYPE_NUM)
    {
        $key = CreditNoteStatusTableMap::getPrimaryKeyHashFromRow($row, $offset, $indexType);
        if (null !== ($obj = CreditNoteStatusTableMap::getInstanceFromPool($key))) {
            // We no longer rehydrate the object, since this can cause data loss.
            // See http://www.propelorm.org/ticket/509
            // $obj->hydrate($row, $offset, true); // rehydrate
            $col = $offset + CreditNoteStatusTableMap::NUM_HYDRATE_COLUMNS;
        } else {
            $cls = CreditNoteStatusTableMap::OM_CLASS;
            $obj = new $cls();
            $col = $obj->hydrate($row, $offset, false, $indexType);
            CreditNoteStatusTableMap::addInstanceToPool($obj, $key);
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
            $key = CreditNoteStatusTableMap::getPrimaryKeyHashFromRow($row, 0, $dataFetcher->getIndexType());
            if (null !== ($obj = CreditNoteStatusTableMap::getInstanceFromPool($key))) {
                // We no longer rehydrate the object, since this can cause data loss.
                // See http://www.propelorm.org/ticket/509
                // $obj->hydrate($row, 0, true); // rehydrate
                $results[] = $obj;
            } else {
                $obj = new $cls();
                $obj->hydrate($row);
                $results[] = $obj;
                CreditNoteStatusTableMap::addInstanceToPool($obj, $key);
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
            $criteria->addSelectColumn(CreditNoteStatusTableMap::ID);
            $criteria->addSelectColumn(CreditNoteStatusTableMap::CODE);
            $criteria->addSelectColumn(CreditNoteStatusTableMap::COLOR);
            $criteria->addSelectColumn(CreditNoteStatusTableMap::INVOICED);
            $criteria->addSelectColumn(CreditNoteStatusTableMap::USED);
            $criteria->addSelectColumn(CreditNoteStatusTableMap::POSITION);
            $criteria->addSelectColumn(CreditNoteStatusTableMap::CREATED_AT);
            $criteria->addSelectColumn(CreditNoteStatusTableMap::UPDATED_AT);
        } else {
            $criteria->addSelectColumn($alias . '.ID');
            $criteria->addSelectColumn($alias . '.CODE');
            $criteria->addSelectColumn($alias . '.COLOR');
            $criteria->addSelectColumn($alias . '.INVOICED');
            $criteria->addSelectColumn($alias . '.USED');
            $criteria->addSelectColumn($alias . '.POSITION');
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
        return Propel::getServiceContainer()->getDatabaseMap(CreditNoteStatusTableMap::DATABASE_NAME)->getTable(CreditNoteStatusTableMap::TABLE_NAME);
    }

    /**
     * Add a TableMap instance to the database for this tableMap class.
     */
    public static function buildTableMap()
    {
      $dbMap = Propel::getServiceContainer()->getDatabaseMap(CreditNoteStatusTableMap::DATABASE_NAME);
      if (!$dbMap->hasTable(CreditNoteStatusTableMap::TABLE_NAME)) {
        $dbMap->addTableObject(new CreditNoteStatusTableMap());
      }
    }

    /**
     * Performs a DELETE on the database, given a CreditNoteStatus or Criteria object OR a primary key value.
     *
     * @param mixed               $values Criteria or CreditNoteStatus object or primary key or array of primary keys
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
            $con = Propel::getServiceContainer()->getWriteConnection(CreditNoteStatusTableMap::DATABASE_NAME);
        }

        if ($values instanceof Criteria) {
            // rename for clarity
            $criteria = $values;
        } elseif ($values instanceof \CreditNote\Model\CreditNoteStatus) { // it's a model object
            // create criteria based on pk values
            $criteria = $values->buildPkeyCriteria();
        } else { // it's a primary key, or an array of pks
            $criteria = new Criteria(CreditNoteStatusTableMap::DATABASE_NAME);
            $criteria->add(CreditNoteStatusTableMap::ID, (array) $values, Criteria::IN);
        }

        $query = CreditNoteStatusQuery::create()->mergeWith($criteria);

        if ($values instanceof Criteria) { CreditNoteStatusTableMap::clearInstancePool();
        } elseif (!is_object($values)) { // it's a primary key, or an array of pks
            foreach ((array) $values as $singleval) { CreditNoteStatusTableMap::removeInstanceFromPool($singleval);
            }
        }

        return $query->delete($con);
    }

    /**
     * Deletes all rows from the credit_note_status table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public static function doDeleteAll(ConnectionInterface $con = null)
    {
        return CreditNoteStatusQuery::create()->doDeleteAll($con);
    }

    /**
     * Performs an INSERT on the database, given a CreditNoteStatus or Criteria object.
     *
     * @param mixed               $criteria Criteria or CreditNoteStatus object containing data that is used to create the INSERT statement.
     * @param ConnectionInterface $con the ConnectionInterface connection to use
     * @return mixed           The new primary key.
     * @throws PropelException Any exceptions caught during processing will be
     *         rethrown wrapped into a PropelException.
     */
    public static function doInsert($criteria, ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(CreditNoteStatusTableMap::DATABASE_NAME);
        }

        if ($criteria instanceof Criteria) {
            $criteria = clone $criteria; // rename for clarity
        } else {
            $criteria = $criteria->buildCriteria(); // build Criteria from CreditNoteStatus object
        }

        if ($criteria->containsKey(CreditNoteStatusTableMap::ID) && $criteria->keyContainsValue(CreditNoteStatusTableMap::ID) ) {
            throw new PropelException('Cannot insert a value for auto-increment primary key ('.CreditNoteStatusTableMap::ID.')');
        }


        // Set the correct dbName
        $query = CreditNoteStatusQuery::create()->mergeWith($criteria);

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

} // CreditNoteStatusTableMap
// This is the static code needed to register the TableMap for this table with the main Propel class.
//
CreditNoteStatusTableMap::buildTableMap();
