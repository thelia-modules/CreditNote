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
     * the column name for the ID field
     */
    const ID = 'credit_note_detail.ID';

    /**
     * the column name for the CREDIT_NOTE_ID field
     */
    const CREDIT_NOTE_ID = 'credit_note_detail.CREDIT_NOTE_ID';

    /**
     * the column name for the PRICE field
     */
    const PRICE = 'credit_note_detail.PRICE';

    /**
     * the column name for the PRICE_WITH_TAX field
     */
    const PRICE_WITH_TAX = 'credit_note_detail.PRICE_WITH_TAX';

    /**
     * the column name for the TAX_RULE_ID field
     */
    const TAX_RULE_ID = 'credit_note_detail.TAX_RULE_ID';

    /**
     * the column name for the ORDER_PRODUCT_ID field
     */
    const ORDER_PRODUCT_ID = 'credit_note_detail.ORDER_PRODUCT_ID';

    /**
     * the column name for the TYPE field
     */
    const TYPE = 'credit_note_detail.TYPE';

    /**
     * the column name for the QUANTITY field
     */
    const QUANTITY = 'credit_note_detail.QUANTITY';

    /**
     * the column name for the TITLE field
     */
    const TITLE = 'credit_note_detail.TITLE';

    /**
     * the column name for the CREATED_AT field
     */
    const CREATED_AT = 'credit_note_detail.CREATED_AT';

    /**
     * the column name for the UPDATED_AT field
     */
    const UPDATED_AT = 'credit_note_detail.UPDATED_AT';

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
        self::TYPE_STUDLYPHPNAME => array('id', 'creditNoteId', 'price', 'priceWithTax', 'taxRuleId', 'orderProductId', 'type', 'quantity', 'title', 'createdAt', 'updatedAt', ),
        self::TYPE_COLNAME       => array(CreditNoteDetailTableMap::ID, CreditNoteDetailTableMap::CREDIT_NOTE_ID, CreditNoteDetailTableMap::PRICE, CreditNoteDetailTableMap::PRICE_WITH_TAX, CreditNoteDetailTableMap::TAX_RULE_ID, CreditNoteDetailTableMap::ORDER_PRODUCT_ID, CreditNoteDetailTableMap::TYPE, CreditNoteDetailTableMap::QUANTITY, CreditNoteDetailTableMap::TITLE, CreditNoteDetailTableMap::CREATED_AT, CreditNoteDetailTableMap::UPDATED_AT, ),
        self::TYPE_RAW_COLNAME   => array('ID', 'CREDIT_NOTE_ID', 'PRICE', 'PRICE_WITH_TAX', 'TAX_RULE_ID', 'ORDER_PRODUCT_ID', 'TYPE', 'QUANTITY', 'TITLE', 'CREATED_AT', 'UPDATED_AT', ),
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
        self::TYPE_STUDLYPHPNAME => array('id' => 0, 'creditNoteId' => 1, 'price' => 2, 'priceWithTax' => 3, 'taxRuleId' => 4, 'orderProductId' => 5, 'type' => 6, 'quantity' => 7, 'title' => 8, 'createdAt' => 9, 'updatedAt' => 10, ),
        self::TYPE_COLNAME       => array(CreditNoteDetailTableMap::ID => 0, CreditNoteDetailTableMap::CREDIT_NOTE_ID => 1, CreditNoteDetailTableMap::PRICE => 2, CreditNoteDetailTableMap::PRICE_WITH_TAX => 3, CreditNoteDetailTableMap::TAX_RULE_ID => 4, CreditNoteDetailTableMap::ORDER_PRODUCT_ID => 5, CreditNoteDetailTableMap::TYPE => 6, CreditNoteDetailTableMap::QUANTITY => 7, CreditNoteDetailTableMap::TITLE => 8, CreditNoteDetailTableMap::CREATED_AT => 9, CreditNoteDetailTableMap::UPDATED_AT => 10, ),
        self::TYPE_RAW_COLNAME   => array('ID' => 0, 'CREDIT_NOTE_ID' => 1, 'PRICE' => 2, 'PRICE_WITH_TAX' => 3, 'TAX_RULE_ID' => 4, 'ORDER_PRODUCT_ID' => 5, 'TYPE' => 6, 'QUANTITY' => 7, 'TITLE' => 8, 'CREATED_AT' => 9, 'UPDATED_AT' => 10, ),
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
        $this->setClassName('\\CreditNote\\Model\\CreditNoteDetail');
        $this->setPackage('CreditNote.Model');
        $this->setUseIdGenerator(true);
        // columns
        $this->addPrimaryKey('ID', 'Id', 'INTEGER', true, null, null);
        $this->addForeignKey('CREDIT_NOTE_ID', 'CreditNoteId', 'INTEGER', 'credit_note', 'ID', true, null, null);
        $this->addColumn('PRICE', 'Price', 'DECIMAL', false, 16, 0);
        $this->addColumn('PRICE_WITH_TAX', 'PriceWithTax', 'DECIMAL', false, 16, 0);
        $this->addForeignKey('TAX_RULE_ID', 'TaxRuleId', 'INTEGER', 'tax_rule', 'ID', false, null, null);
        $this->addForeignKey('ORDER_PRODUCT_ID', 'OrderProductId', 'INTEGER', 'order_product', 'ID', false, null, null);
        $this->addColumn('TYPE', 'Type', 'VARCHAR', false, 55, null);
        $this->addColumn('QUANTITY', 'Quantity', 'INTEGER', true, null, 0);
        $this->addColumn('TITLE', 'Title', 'VARCHAR', false, 255, null);
        $this->addColumn('CREATED_AT', 'CreatedAt', 'TIMESTAMP', false, null, null);
        $this->addColumn('UPDATED_AT', 'UpdatedAt', 'TIMESTAMP', false, null, null);
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
        $this->addRelation('CreditNote', '\\CreditNote\\Model\\CreditNote', RelationMap::MANY_TO_ONE, array('credit_note_id' => 'id', ), 'CASCADE', 'RESTRICT');
        $this->addRelation('OrderProduct', '\\Thelia\\Model\\OrderProduct', RelationMap::MANY_TO_ONE, array('order_product_id' => 'id', ), 'RESTRICT', 'RESTRICT');
        $this->addRelation('TaxRule', '\\Thelia\\Model\\TaxRule', RelationMap::MANY_TO_ONE, array('tax_rule_id' => 'id', ), 'RESTRICT', 'RESTRICT');
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
        return $withPrefix ? CreditNoteDetailTableMap::CLASS_DEFAULT : CreditNoteDetailTableMap::OM_CLASS;
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
     * @return array (CreditNoteDetail object, last column rank)
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
     *         rethrown wrapped into a PropelException.
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
     *         rethrown wrapped into a PropelException.
     */
    public static function addSelectColumns(Criteria $criteria, $alias = null)
    {
        if (null === $alias) {
            $criteria->addSelectColumn(CreditNoteDetailTableMap::ID);
            $criteria->addSelectColumn(CreditNoteDetailTableMap::CREDIT_NOTE_ID);
            $criteria->addSelectColumn(CreditNoteDetailTableMap::PRICE);
            $criteria->addSelectColumn(CreditNoteDetailTableMap::PRICE_WITH_TAX);
            $criteria->addSelectColumn(CreditNoteDetailTableMap::TAX_RULE_ID);
            $criteria->addSelectColumn(CreditNoteDetailTableMap::ORDER_PRODUCT_ID);
            $criteria->addSelectColumn(CreditNoteDetailTableMap::TYPE);
            $criteria->addSelectColumn(CreditNoteDetailTableMap::QUANTITY);
            $criteria->addSelectColumn(CreditNoteDetailTableMap::TITLE);
            $criteria->addSelectColumn(CreditNoteDetailTableMap::CREATED_AT);
            $criteria->addSelectColumn(CreditNoteDetailTableMap::UPDATED_AT);
        } else {
            $criteria->addSelectColumn($alias . '.ID');
            $criteria->addSelectColumn($alias . '.CREDIT_NOTE_ID');
            $criteria->addSelectColumn($alias . '.PRICE');
            $criteria->addSelectColumn($alias . '.PRICE_WITH_TAX');
            $criteria->addSelectColumn($alias . '.TAX_RULE_ID');
            $criteria->addSelectColumn($alias . '.ORDER_PRODUCT_ID');
            $criteria->addSelectColumn($alias . '.TYPE');
            $criteria->addSelectColumn($alias . '.QUANTITY');
            $criteria->addSelectColumn($alias . '.TITLE');
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
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).  This includes CASCADE-related rows
     *                if supported by native driver or if emulated using Propel.
     * @throws PropelException Any exceptions caught during processing will be
     *         rethrown wrapped into a PropelException.
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
            $criteria->add(CreditNoteDetailTableMap::ID, (array) $values, Criteria::IN);
        }

        $query = CreditNoteDetailQuery::create()->mergeWith($criteria);

        if ($values instanceof Criteria) { CreditNoteDetailTableMap::clearInstancePool();
        } elseif (!is_object($values)) { // it's a primary key, or an array of pks
            foreach ((array) $values as $singleval) { CreditNoteDetailTableMap::removeInstanceFromPool($singleval);
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
     *         rethrown wrapped into a PropelException.
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

        if ($criteria->containsKey(CreditNoteDetailTableMap::ID) && $criteria->keyContainsValue(CreditNoteDetailTableMap::ID) ) {
            throw new PropelException('Cannot insert a value for auto-increment primary key ('.CreditNoteDetailTableMap::ID.')');
        }


        // Set the correct dbName
        $query = CreditNoteDetailQuery::create()->mergeWith($criteria);

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

} // CreditNoteDetailTableMap
// This is the static code needed to register the TableMap for this table with the main Propel class.
//
CreditNoteDetailTableMap::buildTableMap();
