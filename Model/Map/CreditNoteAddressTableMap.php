<?php

namespace CreditNote\Model\Map;

use CreditNote\Model\CreditNoteAddress;
use CreditNote\Model\CreditNoteAddressQuery;
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
 * This class defines the structure of the 'credit_note_address' table.
 *
 *
 *
 * This map class is used by Propel to do runtime db structure discovery.
 * For example, the createSelectSql() method checks the type of a given column used in an
 * ORDER BY clause to know whether it needs to apply SQL to make the ORDER BY case-insensitive
 * (i.e. if it's a text column type).
 *
 */
class CreditNoteAddressTableMap extends TableMap
{
    use InstancePoolTrait;
    use TableMapTrait;

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'CreditNote.Model.Map.CreditNoteAddressTableMap';

    /**
     * The default database name for this class
     */
    const DATABASE_NAME = 'thelia';

    /**
     * The table name for this class
     */
    const TABLE_NAME = 'credit_note_address';

    /**
     * The related Propel class for this table
     */
    const OM_CLASS = '\\CreditNote\\Model\\CreditNoteAddress';

    /**
     * A class that can be returned by this tableMap
     */
    const CLASS_DEFAULT = 'CreditNote.Model.CreditNoteAddress';

    /**
     * The total number of columns
     */
    const NUM_COLUMNS = 16;

    /**
     * The number of lazy-loaded columns
     */
    const NUM_LAZY_LOAD_COLUMNS = 0;

    /**
     * The number of columns to hydrate (NUM_COLUMNS - NUM_LAZY_LOAD_COLUMNS)
     */
    const NUM_HYDRATE_COLUMNS = 16;

    /**
    * the column legacy name for the id field
    * @deprecated Legacy constant for compatibility. Use COL_ID.
    */
    const ID = 'credit_note_address.id';
    /**
    * the column legacy name for the customer_title_id field
    * @deprecated Legacy constant for compatibility. Use COL_CUSTOMER_TITLE_ID.
    */
    const CUSTOMER_TITLE_ID = 'credit_note_address.customer_title_id';
    /**
    * the column legacy name for the company field
    * @deprecated Legacy constant for compatibility. Use COL_COMPANY.
    */
    const COMPANY = 'credit_note_address.company';
    /**
    * the column legacy name for the firstname field
    * @deprecated Legacy constant for compatibility. Use COL_FIRSTNAME.
    */
    const FIRSTNAME = 'credit_note_address.firstname';
    /**
    * the column legacy name for the lastname field
    * @deprecated Legacy constant for compatibility. Use COL_LASTNAME.
    */
    const LASTNAME = 'credit_note_address.lastname';
    /**
    * the column legacy name for the address1 field
    * @deprecated Legacy constant for compatibility. Use COL_ADDRESS1.
    */
    const ADDRESS1 = 'credit_note_address.address1';
    /**
    * the column legacy name for the address2 field
    * @deprecated Legacy constant for compatibility. Use COL_ADDRESS2.
    */
    const ADDRESS2 = 'credit_note_address.address2';
    /**
    * the column legacy name for the address3 field
    * @deprecated Legacy constant for compatibility. Use COL_ADDRESS3.
    */
    const ADDRESS3 = 'credit_note_address.address3';
    /**
    * the column legacy name for the zipcode field
    * @deprecated Legacy constant for compatibility. Use COL_ZIPCODE.
    */
    const ZIPCODE = 'credit_note_address.zipcode';
    /**
    * the column legacy name for the city field
    * @deprecated Legacy constant for compatibility. Use COL_CITY.
    */
    const CITY = 'credit_note_address.city';
    /**
    * the column legacy name for the phone field
    * @deprecated Legacy constant for compatibility. Use COL_PHONE.
    */
    const PHONE = 'credit_note_address.phone';
    /**
    * the column legacy name for the cellphone field
    * @deprecated Legacy constant for compatibility. Use COL_CELLPHONE.
    */
    const CELLPHONE = 'credit_note_address.cellphone';
    /**
    * the column legacy name for the country_id field
    * @deprecated Legacy constant for compatibility. Use COL_COUNTRY_ID.
    */
    const COUNTRY_ID = 'credit_note_address.country_id';
    /**
    * the column legacy name for the state_id field
    * @deprecated Legacy constant for compatibility. Use COL_STATE_ID.
    */
    const STATE_ID = 'credit_note_address.state_id';
    /**
    * the column legacy name for the created_at field
    * @deprecated Legacy constant for compatibility. Use COL_CREATED_AT.
    */
    const CREATED_AT = 'credit_note_address.created_at';
    /**
    * the column legacy name for the updated_at field
    * @deprecated Legacy constant for compatibility. Use COL_UPDATED_AT.
    */
    const UPDATED_AT = 'credit_note_address.updated_at';
    /**
     * the column name for the id field
     */
    const COL_ID = 'credit_note_address.id';
    /**
     * the column name for the customer_title_id field
     */
    const COL_CUSTOMER_TITLE_ID = 'credit_note_address.customer_title_id';
    /**
     * the column name for the company field
     */
    const COL_COMPANY = 'credit_note_address.company';
    /**
     * the column name for the firstname field
     */
    const COL_FIRSTNAME = 'credit_note_address.firstname';
    /**
     * the column name for the lastname field
     */
    const COL_LASTNAME = 'credit_note_address.lastname';
    /**
     * the column name for the address1 field
     */
    const COL_ADDRESS1 = 'credit_note_address.address1';
    /**
     * the column name for the address2 field
     */
    const COL_ADDRESS2 = 'credit_note_address.address2';
    /**
     * the column name for the address3 field
     */
    const COL_ADDRESS3 = 'credit_note_address.address3';
    /**
     * the column name for the zipcode field
     */
    const COL_ZIPCODE = 'credit_note_address.zipcode';
    /**
     * the column name for the city field
     */
    const COL_CITY = 'credit_note_address.city';
    /**
     * the column name for the phone field
     */
    const COL_PHONE = 'credit_note_address.phone';
    /**
     * the column name for the cellphone field
     */
    const COL_CELLPHONE = 'credit_note_address.cellphone';
    /**
     * the column name for the country_id field
     */
    const COL_COUNTRY_ID = 'credit_note_address.country_id';
    /**
     * the column name for the state_id field
     */
    const COL_STATE_ID = 'credit_note_address.state_id';
    /**
     * the column name for the created_at field
     */
    const COL_CREATED_AT = 'credit_note_address.created_at';
    /**
     * the column name for the updated_at field
     */
    const COL_UPDATED_AT = 'credit_note_address.updated_at';

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
        self::TYPE_PHPNAME       => array('Id', 'CustomerTitleId', 'Company', 'Firstname', 'Lastname', 'Address1', 'Address2', 'Address3', 'Zipcode', 'City', 'Phone', 'Cellphone', 'CountryId', 'StateId', 'CreatedAt', 'UpdatedAt', ),
        self::TYPE_CAMELNAME     => array('id', 'customerTitleId', 'company', 'firstname', 'lastname', 'address1', 'address2', 'address3', 'zipcode', 'city', 'phone', 'cellphone', 'countryId', 'stateId', 'createdAt', 'updatedAt', ),
        self::TYPE_COLNAME       => array(CreditNoteAddressTableMap::COL_ID, CreditNoteAddressTableMap::COL_CUSTOMER_TITLE_ID, CreditNoteAddressTableMap::COL_COMPANY, CreditNoteAddressTableMap::COL_FIRSTNAME, CreditNoteAddressTableMap::COL_LASTNAME, CreditNoteAddressTableMap::COL_ADDRESS1, CreditNoteAddressTableMap::COL_ADDRESS2, CreditNoteAddressTableMap::COL_ADDRESS3, CreditNoteAddressTableMap::COL_ZIPCODE, CreditNoteAddressTableMap::COL_CITY, CreditNoteAddressTableMap::COL_PHONE, CreditNoteAddressTableMap::COL_CELLPHONE, CreditNoteAddressTableMap::COL_COUNTRY_ID, CreditNoteAddressTableMap::COL_STATE_ID, CreditNoteAddressTableMap::COL_CREATED_AT, CreditNoteAddressTableMap::COL_UPDATED_AT, ),
        self::TYPE_FIELDNAME     => array('id', 'customer_title_id', 'company', 'firstname', 'lastname', 'address1', 'address2', 'address3', 'zipcode', 'city', 'phone', 'cellphone', 'country_id', 'state_id', 'created_at', 'updated_at', ),
        self::TYPE_NUM           => array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, )
    );

    /**
     * holds an array of keys for quick access to the fieldnames array
     *
     * first dimension keys are the type constants
     * e.g. self::$fieldKeys[self::TYPE_PHPNAME]['Id'] = 0
     */
    protected static $fieldKeys = array (
        self::TYPE_PHPNAME       => array('Id' => 0, 'CustomerTitleId' => 1, 'Company' => 2, 'Firstname' => 3, 'Lastname' => 4, 'Address1' => 5, 'Address2' => 6, 'Address3' => 7, 'Zipcode' => 8, 'City' => 9, 'Phone' => 10, 'Cellphone' => 11, 'CountryId' => 12, 'StateId' => 13, 'CreatedAt' => 14, 'UpdatedAt' => 15, ),
        self::TYPE_CAMELNAME     => array('id' => 0, 'customerTitleId' => 1, 'company' => 2, 'firstname' => 3, 'lastname' => 4, 'address1' => 5, 'address2' => 6, 'address3' => 7, 'zipcode' => 8, 'city' => 9, 'phone' => 10, 'cellphone' => 11, 'countryId' => 12, 'stateId' => 13, 'createdAt' => 14, 'updatedAt' => 15, ),
        self::TYPE_COLNAME       => array(CreditNoteAddressTableMap::COL_ID => 0, CreditNoteAddressTableMap::COL_CUSTOMER_TITLE_ID => 1, CreditNoteAddressTableMap::COL_COMPANY => 2, CreditNoteAddressTableMap::COL_FIRSTNAME => 3, CreditNoteAddressTableMap::COL_LASTNAME => 4, CreditNoteAddressTableMap::COL_ADDRESS1 => 5, CreditNoteAddressTableMap::COL_ADDRESS2 => 6, CreditNoteAddressTableMap::COL_ADDRESS3 => 7, CreditNoteAddressTableMap::COL_ZIPCODE => 8, CreditNoteAddressTableMap::COL_CITY => 9, CreditNoteAddressTableMap::COL_PHONE => 10, CreditNoteAddressTableMap::COL_CELLPHONE => 11, CreditNoteAddressTableMap::COL_COUNTRY_ID => 12, CreditNoteAddressTableMap::COL_STATE_ID => 13, CreditNoteAddressTableMap::COL_CREATED_AT => 14, CreditNoteAddressTableMap::COL_UPDATED_AT => 15, ),
        self::TYPE_FIELDNAME     => array('id' => 0, 'customer_title_id' => 1, 'company' => 2, 'firstname' => 3, 'lastname' => 4, 'address1' => 5, 'address2' => 6, 'address3' => 7, 'zipcode' => 8, 'city' => 9, 'phone' => 10, 'cellphone' => 11, 'country_id' => 12, 'state_id' => 13, 'created_at' => 14, 'updated_at' => 15, ),
        self::TYPE_NUM           => array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, )
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
        $this->setName('credit_note_address');
        $this->setPhpName('CreditNoteAddress');
        $this->setIdentifierQuoting(true);
        $this->setClassName('\\CreditNote\\Model\\CreditNoteAddress');
        $this->setPackage('CreditNote.Model');
        $this->setUseIdGenerator(true);
        // columns
        $this->addPrimaryKey('id', 'Id', 'INTEGER', true, null, null);
        $this->addForeignKey('customer_title_id', 'CustomerTitleId', 'INTEGER', 'customer_title', 'id', false, null, null);
        $this->addColumn('company', 'Company', 'VARCHAR', false, 255, null);
        $this->addColumn('firstname', 'Firstname', 'VARCHAR', true, 255, null);
        $this->addColumn('lastname', 'Lastname', 'VARCHAR', true, 255, null);
        $this->addColumn('address1', 'Address1', 'VARCHAR', true, 255, null);
        $this->addColumn('address2', 'Address2', 'VARCHAR', false, 255, null);
        $this->addColumn('address3', 'Address3', 'VARCHAR', false, 255, null);
        $this->addColumn('zipcode', 'Zipcode', 'VARCHAR', true, 10, null);
        $this->addColumn('city', 'City', 'VARCHAR', true, 255, null);
        $this->addColumn('phone', 'Phone', 'VARCHAR', false, 20, null);
        $this->addColumn('cellphone', 'Cellphone', 'VARCHAR', false, 20, null);
        $this->addForeignKey('country_id', 'CountryId', 'INTEGER', 'country', 'id', true, null, null);
        $this->addForeignKey('state_id', 'StateId', 'INTEGER', 'state', 'id', false, null, null);
        $this->addColumn('created_at', 'CreatedAt', 'TIMESTAMP', false, null, null);
        $this->addColumn('updated_at', 'UpdatedAt', 'TIMESTAMP', false, null, null);
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
        $this->addRelation('CustomerTitle', '\\Thelia\\Model\\CustomerTitle', RelationMap::MANY_TO_ONE, array (
  0 =>
  array (
    0 => ':customer_title_id',
    1 => ':id',
  ),
), 'RESTRICT', 'RESTRICT', null, false);
        $this->addRelation('Country', '\\Thelia\\Model\\Country', RelationMap::MANY_TO_ONE, array (
  0 =>
  array (
    0 => ':country_id',
    1 => ':id',
  ),
), 'RESTRICT', 'RESTRICT', null, false);
        $this->addRelation('State', '\\Thelia\\Model\\State', RelationMap::MANY_TO_ONE, array (
  0 =>
  array (
    0 => ':state_id',
    1 => ':id',
  ),
), 'RESTRICT', 'RESTRICT', null, false);
        $this->addRelation('CreditNote', '\\CreditNote\\Model\\CreditNote', RelationMap::ONE_TO_MANY, array (
  0 =>
  array (
    0 => ':invoice_address_id',
    1 => ':id',
  ),
), 'RESTRICT', 'RESTRICT', 'CreditNotes', false);
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
        return $withPrefix ? CreditNoteAddressTableMap::CLASS_DEFAULT : CreditNoteAddressTableMap::OM_CLASS;
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
     * @return array           (CreditNoteAddress object, last column rank)
     */
    public static function populateObject($row, $offset = 0, $indexType = TableMap::TYPE_NUM)
    {
        $key = CreditNoteAddressTableMap::getPrimaryKeyHashFromRow($row, $offset, $indexType);
        if (null !== ($obj = CreditNoteAddressTableMap::getInstanceFromPool($key))) {
            // We no longer rehydrate the object, since this can cause data loss.
            // See http://www.propelorm.org/ticket/509
            // $obj->hydrate($row, $offset, true); // rehydrate
            $col = $offset + CreditNoteAddressTableMap::NUM_HYDRATE_COLUMNS;
        } else {
            $cls = CreditNoteAddressTableMap::OM_CLASS;
            /** @var CreditNoteAddress $obj */
            $obj = new $cls();
            $col = $obj->hydrate($row, $offset, false, $indexType);
            CreditNoteAddressTableMap::addInstanceToPool($obj, $key);
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
            $key = CreditNoteAddressTableMap::getPrimaryKeyHashFromRow($row, 0, $dataFetcher->getIndexType());
            if (null !== ($obj = CreditNoteAddressTableMap::getInstanceFromPool($key))) {
                // We no longer rehydrate the object, since this can cause data loss.
                // See http://www.propelorm.org/ticket/509
                // $obj->hydrate($row, 0, true); // rehydrate
                $results[] = $obj;
            } else {
                /** @var CreditNoteAddress $obj */
                $obj = new $cls();
                $obj->hydrate($row);
                $results[] = $obj;
                CreditNoteAddressTableMap::addInstanceToPool($obj, $key);
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
            $criteria->addSelectColumn(CreditNoteAddressTableMap::COL_ID);
            $criteria->addSelectColumn(CreditNoteAddressTableMap::COL_CUSTOMER_TITLE_ID);
            $criteria->addSelectColumn(CreditNoteAddressTableMap::COL_COMPANY);
            $criteria->addSelectColumn(CreditNoteAddressTableMap::COL_FIRSTNAME);
            $criteria->addSelectColumn(CreditNoteAddressTableMap::COL_LASTNAME);
            $criteria->addSelectColumn(CreditNoteAddressTableMap::COL_ADDRESS1);
            $criteria->addSelectColumn(CreditNoteAddressTableMap::COL_ADDRESS2);
            $criteria->addSelectColumn(CreditNoteAddressTableMap::COL_ADDRESS3);
            $criteria->addSelectColumn(CreditNoteAddressTableMap::COL_ZIPCODE);
            $criteria->addSelectColumn(CreditNoteAddressTableMap::COL_CITY);
            $criteria->addSelectColumn(CreditNoteAddressTableMap::COL_PHONE);
            $criteria->addSelectColumn(CreditNoteAddressTableMap::COL_CELLPHONE);
            $criteria->addSelectColumn(CreditNoteAddressTableMap::COL_COUNTRY_ID);
            $criteria->addSelectColumn(CreditNoteAddressTableMap::COL_STATE_ID);
            $criteria->addSelectColumn(CreditNoteAddressTableMap::COL_CREATED_AT);
            $criteria->addSelectColumn(CreditNoteAddressTableMap::COL_UPDATED_AT);
        } else {
            $criteria->addSelectColumn($alias . '.id');
            $criteria->addSelectColumn($alias . '.customer_title_id');
            $criteria->addSelectColumn($alias . '.company');
            $criteria->addSelectColumn($alias . '.firstname');
            $criteria->addSelectColumn($alias . '.lastname');
            $criteria->addSelectColumn($alias . '.address1');
            $criteria->addSelectColumn($alias . '.address2');
            $criteria->addSelectColumn($alias . '.address3');
            $criteria->addSelectColumn($alias . '.zipcode');
            $criteria->addSelectColumn($alias . '.city');
            $criteria->addSelectColumn($alias . '.phone');
            $criteria->addSelectColumn($alias . '.cellphone');
            $criteria->addSelectColumn($alias . '.country_id');
            $criteria->addSelectColumn($alias . '.state_id');
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
        return Propel::getServiceContainer()->getDatabaseMap(CreditNoteAddressTableMap::DATABASE_NAME)->getTable(CreditNoteAddressTableMap::TABLE_NAME);
    }

    /**
     * Add a TableMap instance to the database for this tableMap class.
     */
    public static function buildTableMap()
    {
        $dbMap = Propel::getServiceContainer()->getDatabaseMap(CreditNoteAddressTableMap::DATABASE_NAME);
        if (!$dbMap->hasTable(CreditNoteAddressTableMap::TABLE_NAME)) {
            $dbMap->addTableObject(new CreditNoteAddressTableMap());
        }
    }

    /**
     * Performs a DELETE on the database, given a CreditNoteAddress or Criteria object OR a primary key value.
     *
     * @param mixed               $values Criteria or CreditNoteAddress object or primary key or array of primary keys
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
            $con = Propel::getServiceContainer()->getWriteConnection(CreditNoteAddressTableMap::DATABASE_NAME);
        }

        if ($values instanceof Criteria) {
            // rename for clarity
            $criteria = $values;
        } elseif ($values instanceof \CreditNote\Model\CreditNoteAddress) { // it's a model object
            // create criteria based on pk values
            $criteria = $values->buildPkeyCriteria();
        } else { // it's a primary key, or an array of pks
            $criteria = new Criteria(CreditNoteAddressTableMap::DATABASE_NAME);
            $criteria->add(CreditNoteAddressTableMap::COL_ID, (array) $values, Criteria::IN);
        }

        $query = CreditNoteAddressQuery::create()->mergeWith($criteria);

        if ($values instanceof Criteria) {
            CreditNoteAddressTableMap::clearInstancePool();
        } elseif (!\is_object($values)) { // it's a primary key, or an array of pks
            foreach ((array) $values as $singleval) {
                CreditNoteAddressTableMap::removeInstanceFromPool($singleval);
            }
        }

        return $query->delete($con);
    }

    /**
     * Deletes all rows from the credit_note_address table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public static function doDeleteAll(ConnectionInterface $con = null)
    {
        return CreditNoteAddressQuery::create()->doDeleteAll($con);
    }

    /**
     * Performs an INSERT on the database, given a CreditNoteAddress or Criteria object.
     *
     * @param mixed               $criteria Criteria or CreditNoteAddress object containing data that is used to create the INSERT statement.
     * @param ConnectionInterface $con the ConnectionInterface connection to use
     * @return mixed           The new primary key.
     * @throws PropelException Any exceptions caught during processing will be
     *                         rethrown wrapped into a PropelException.
     */
    public static function doInsert($criteria, ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(CreditNoteAddressTableMap::DATABASE_NAME);
        }

        if ($criteria instanceof Criteria) {
            $criteria = clone $criteria; // rename for clarity
        } else {
            $criteria = $criteria->buildCriteria(); // build Criteria from CreditNoteAddress object
        }

        if ($criteria->containsKey(CreditNoteAddressTableMap::COL_ID) && $criteria->keyContainsValue(CreditNoteAddressTableMap::COL_ID) ) {
            throw new PropelException('Cannot insert a value for auto-increment primary key ('.CreditNoteAddressTableMap::COL_ID.')');
        }


        // Set the correct dbName
        $query = CreditNoteAddressQuery::create()->mergeWith($criteria);

        // use transaction because $criteria could contain info
        // for more than one table (I guess, conceivably)
        return $con->transaction(function () use ($con, $query) {
            return $query->doInsert($con);
        });
    }

} // CreditNoteAddressTableMap
// This is the static code needed to register the TableMap for this table with the main Propel class.
//
CreditNoteAddressTableMap::buildTableMap();
