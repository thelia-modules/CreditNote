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
    * the column legacy name for the id field
    * @deprecated Legacy constant for compatibility. Use COL_ID.
    */
    const ID = 'credit_note_status.id';
    /**
    * the column legacy name for the code field
    * @deprecated Legacy constant for compatibility. Use COL_CODE.
    */
    const CODE = 'credit_note_status.code';
    /**
    * the column legacy name for the color field
    * @deprecated Legacy constant for compatibility. Use COL_COLOR.
    */
    const COLOR = 'credit_note_status.color';
    /**
    * the column legacy name for the invoiced field
    * @deprecated Legacy constant for compatibility. Use COL_INVOICED.
    */
    const INVOICED = 'credit_note_status.invoiced';
    /**
    * the column legacy name for the used field
    * @deprecated Legacy constant for compatibility. Use COL_USED.
    */
    const USED = 'credit_note_status.used';
    /**
    * the column legacy name for the position field
    * @deprecated Legacy constant for compatibility. Use COL_POSITION.
    */
    const POSITION = 'credit_note_status.position';
    /**
    * the column legacy name for the created_at field
    * @deprecated Legacy constant for compatibility. Use COL_CREATED_AT.
    */
    const CREATED_AT = 'credit_note_status.created_at';
    /**
    * the column legacy name for the updated_at field
    * @deprecated Legacy constant for compatibility. Use COL_UPDATED_AT.
    */
    const UPDATED_AT = 'credit_note_status.updated_at';
    /**
     * the column name for the id field
     */
    const COL_ID = 'credit_note_status.id';
    /**
     * the column name for the code field
     */
    const COL_CODE = 'credit_note_status.code';
    /**
     * the column name for the color field
     */
    const COL_COLOR = 'credit_note_status.color';
    /**
     * the column name for the invoiced field
     */
    const COL_INVOICED = 'credit_note_status.invoiced';
    /**
     * the column name for the used field
     */
    const COL_USED = 'credit_note_status.used';
    /**
     * the column name for the position field
     */
    const COL_POSITION = 'credit_note_status.position';
    /**
     * the column name for the created_at field
     */
    const COL_CREATED_AT = 'credit_note_status.created_at';
    /**
     * the column name for the updated_at field
     */
    const COL_UPDATED_AT = 'credit_note_status.updated_at';

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
        self::TYPE_CAMELNAME     => array('id', 'code', 'color', 'invoiced', 'used', 'position', 'createdAt', 'updatedAt', ),
        self::TYPE_COLNAME       => array(CreditNoteStatusTableMap::COL_ID, CreditNoteStatusTableMap::COL_CODE, CreditNoteStatusTableMap::COL_COLOR, CreditNoteStatusTableMap::COL_INVOICED, CreditNoteStatusTableMap::COL_USED, CreditNoteStatusTableMap::COL_POSITION, CreditNoteStatusTableMap::COL_CREATED_AT, CreditNoteStatusTableMap::COL_UPDATED_AT, ),
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
        self::TYPE_CAMELNAME     => array('id' => 0, 'code' => 1, 'color' => 2, 'invoiced' => 3, 'used' => 4, 'position' => 5, 'createdAt' => 6, 'updatedAt' => 7, ),
        self::TYPE_COLNAME       => array(CreditNoteStatusTableMap::COL_ID => 0, CreditNoteStatusTableMap::COL_CODE => 1, CreditNoteStatusTableMap::COL_COLOR => 2, CreditNoteStatusTableMap::COL_INVOICED => 3, CreditNoteStatusTableMap::COL_USED => 4, CreditNoteStatusTableMap::COL_POSITION => 5, CreditNoteStatusTableMap::COL_CREATED_AT => 6, CreditNoteStatusTableMap::COL_UPDATED_AT => 7, ),
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
        $this->setIdentifierQuoting(true);
        $this->setClassName('\\CreditNote\\Model\\CreditNoteStatus');
        $this->setPackage('CreditNote.Model');
        $this->setUseIdGenerator(true);
        // columns
        $this->addPrimaryKey('id', 'Id', 'INTEGER', true, null, null);
        $this->addColumn('code', 'Code', 'VARCHAR', false, 45, null);
        $this->addColumn('color', 'Color', 'CHAR', false, 7, null);
        $this->addColumn('invoiced', 'Invoiced', 'BOOLEAN', true, 1, false);
        $this->addColumn('used', 'Used', 'BOOLEAN', true, 1, false);
        $this->addColumn('position', 'Position', 'INTEGER', false, 11, null);
        $this->addColumn('created_at', 'CreatedAt', 'TIMESTAMP', false, null, null);
        $this->addColumn('updated_at', 'UpdatedAt', 'TIMESTAMP', false, null, null);
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
        $this->addRelation('CreditNote', '\\CreditNote\\Model\\CreditNote', RelationMap::ONE_TO_MANY, array (
  0 =>
  array (
    0 => ':status_id',
    1 => ':id',
  ),
), 'RESTRICT', 'RESTRICT', 'CreditNotes', false);
        $this->addRelation('CreditNoteStatusFlowRelatedByFromStatusId', '\\CreditNote\\Model\\CreditNoteStatusFlow', RelationMap::ONE_TO_MANY, array (
  0 =>
  array (
    0 => ':from_status_id',
    1 => ':id',
  ),
), 'CASCADE', 'RESTRICT', 'CreditNoteStatusFlowsRelatedByFromStatusId', false);
        $this->addRelation('CreditNoteStatusFlowRelatedByToStatusId', '\\CreditNote\\Model\\CreditNoteStatusFlow', RelationMap::ONE_TO_MANY, array (
  0 =>
  array (
    0 => ':to_status_id',
    1 => ':id',
  ),
), 'CASCADE', 'RESTRICT', 'CreditNoteStatusFlowsRelatedByToStatusId', false);
        $this->addRelation('CreditNoteStatusI18n', '\\CreditNote\\Model\\CreditNoteStatusI18n', RelationMap::ONE_TO_MANY, array (
  0 =>
  array (
    0 => ':id',
    1 => ':id',
  ),
), 'CASCADE', null, 'CreditNoteStatusI18ns', false);
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
            'i18n' => array('i18n_table' => '%TABLE%_i18n', 'i18n_phpname' => '%PHPNAME%I18n', 'i18n_columns' => 'title, description, chapo, postscriptum', 'i18n_pk_column' => '', 'locale_column' => 'locale', 'locale_length' => '5', 'default_locale' => '', 'locale_alias' => '', ),
            'timestampable' => array('create_column' => 'created_at', 'update_column' => 'updated_at', 'disable_created_at' => 'false', 'disable_updated_at' => 'false', ),
        );
    } // getBehaviors()
    /**
     * Method to invalidate the instance pool of all tables related to credit_note_status     * by a foreign key with ON DELETE CASCADE
     */
    public static function clearRelatedInstancePool()
    {
        // Invalidate objects in related instance pools,
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
        return $withPrefix ? CreditNoteStatusTableMap::CLASS_DEFAULT : CreditNoteStatusTableMap::OM_CLASS;
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
     * @return array           (CreditNoteStatus object, last column rank)
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
            /** @var CreditNoteStatus $obj */
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
     *                         rethrown wrapped into a PropelException.
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
                /** @var CreditNoteStatus $obj */
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
     *                         rethrown wrapped into a PropelException.
     */
    public static function addSelectColumns(Criteria $criteria, $alias = null)
    {
        if (null === $alias) {
            $criteria->addSelectColumn(CreditNoteStatusTableMap::COL_ID);
            $criteria->addSelectColumn(CreditNoteStatusTableMap::COL_CODE);
            $criteria->addSelectColumn(CreditNoteStatusTableMap::COL_COLOR);
            $criteria->addSelectColumn(CreditNoteStatusTableMap::COL_INVOICED);
            $criteria->addSelectColumn(CreditNoteStatusTableMap::COL_USED);
            $criteria->addSelectColumn(CreditNoteStatusTableMap::COL_POSITION);
            $criteria->addSelectColumn(CreditNoteStatusTableMap::COL_CREATED_AT);
            $criteria->addSelectColumn(CreditNoteStatusTableMap::COL_UPDATED_AT);
        } else {
            $criteria->addSelectColumn($alias . '.id');
            $criteria->addSelectColumn($alias . '.code');
            $criteria->addSelectColumn($alias . '.color');
            $criteria->addSelectColumn($alias . '.invoiced');
            $criteria->addSelectColumn($alias . '.used');
            $criteria->addSelectColumn($alias . '.position');
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
     * @param  ConnectionInterface $con the connection to use
     * @return int             The number of affected rows (if supported by underlying database driver).  This includes CASCADE-related rows
     *                         if supported by native driver or if emulated using Propel.
     * @throws PropelException Any exceptions caught during processing will be
     *                         rethrown wrapped into a PropelException.
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
            $criteria->add(CreditNoteStatusTableMap::COL_ID, (array) $values, Criteria::IN);
        }

        $query = CreditNoteStatusQuery::create()->mergeWith($criteria);

        if ($values instanceof Criteria) {
            CreditNoteStatusTableMap::clearInstancePool();
        } elseif (!\is_object($values)) { // it's a primary key, or an array of pks
            foreach ((array) $values as $singleval) {
                CreditNoteStatusTableMap::removeInstanceFromPool($singleval);
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
     *                         rethrown wrapped into a PropelException.
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

        if ($criteria->containsKey(CreditNoteStatusTableMap::COL_ID) && $criteria->keyContainsValue(CreditNoteStatusTableMap::COL_ID) ) {
            throw new PropelException('Cannot insert a value for auto-increment primary key ('.CreditNoteStatusTableMap::COL_ID.')');
        }


        // Set the correct dbName
        $query = CreditNoteStatusQuery::create()->mergeWith($criteria);

        // use transaction because $criteria could contain info
        // for more than one table (I guess, conceivably)
        return $con->transaction(function () use ($con, $query) {
            return $query->doInsert($con);
        });
    }

} // CreditNoteStatusTableMap
// This is the static code needed to register the TableMap for this table with the main Propel class.
//
CreditNoteStatusTableMap::buildTableMap();
