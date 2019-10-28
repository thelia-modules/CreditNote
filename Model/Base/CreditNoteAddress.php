<?php

namespace CreditNote\Model\Base;

use \DateTime;
use \Exception;
use \PDO;
use CreditNote\Model\CreditNote as ChildCreditNote;
use CreditNote\Model\CreditNoteAddress as ChildCreditNoteAddress;
use CreditNote\Model\CreditNoteAddressQuery as ChildCreditNoteAddressQuery;
use CreditNote\Model\CreditNoteQuery as ChildCreditNoteQuery;
use CreditNote\Model\Event\CreditNoteAddressEvent;
use CreditNote\Model\Map\CreditNoteAddressTableMap;
use CreditNote\Model\Map\CreditNoteTableMap;
use Propel\Runtime\Propel;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\ActiveRecord\ActiveRecordInterface;
use Propel\Runtime\Collection\Collection;
use Propel\Runtime\Collection\ObjectCollection;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\Exception\BadMethodCallException;
use Propel\Runtime\Exception\LogicException;
use Propel\Runtime\Exception\PropelException;
use Propel\Runtime\Map\TableMap;
use Propel\Runtime\Parser\AbstractParser;
use Propel\Runtime\Util\PropelDateTime;
use Thelia\Model\Country;
use Thelia\Model\CountryQuery;
use Thelia\Model\CustomerTitle;
use Thelia\Model\CustomerTitleQuery;
use Thelia\Model\State;
use Thelia\Model\StateQuery;

/**
 * Base class that represents a row from the 'credit_note_address' table.
 *
 *
 *
 * @package    propel.generator.CreditNote.Model.Base
 */
abstract class CreditNoteAddress implements ActiveRecordInterface
{
    /**
     * TableMap class name
     */
    const TABLE_MAP = '\\CreditNote\\Model\\Map\\CreditNoteAddressTableMap';


    /**
     * attribute to determine if this object has previously been saved.
     * @var boolean
     */
    protected $new = true;

    /**
     * attribute to determine whether this object has been deleted.
     * @var boolean
     */
    protected $deleted = false;

    /**
     * The columns that have been modified in current object.
     * Tracking modified columns allows us to only update modified columns.
     * @var array
     */
    protected $modifiedColumns = array();

    /**
     * The (virtual) columns that are added at runtime
     * The formatters can add supplementary columns based on a resultset
     * @var array
     */
    protected $virtualColumns = array();

    /**
     * The value for the id field.
     *
     * @var        int
     */
    protected $id;

    /**
     * The value for the customer_title_id field.
     *
     * @var        int
     */
    protected $customer_title_id;

    /**
     * The value for the company field.
     *
     * @var        string
     */
    protected $company;

    /**
     * The value for the firstname field.
     *
     * @var        string
     */
    protected $firstname;

    /**
     * The value for the lastname field.
     *
     * @var        string
     */
    protected $lastname;

    /**
     * The value for the address1 field.
     *
     * @var        string
     */
    protected $address1;

    /**
     * The value for the address2 field.
     *
     * @var        string
     */
    protected $address2;

    /**
     * The value for the address3 field.
     *
     * @var        string
     */
    protected $address3;

    /**
     * The value for the zipcode field.
     *
     * @var        string
     */
    protected $zipcode;

    /**
     * The value for the city field.
     *
     * @var        string
     */
    protected $city;

    /**
     * The value for the phone field.
     *
     * @var        string
     */
    protected $phone;

    /**
     * The value for the cellphone field.
     *
     * @var        string
     */
    protected $cellphone;

    /**
     * The value for the country_id field.
     *
     * @var        int
     */
    protected $country_id;

    /**
     * The value for the state_id field.
     *
     * @var        int
     */
    protected $state_id;

    /**
     * The value for the created_at field.
     *
     * @var        DateTime
     */
    protected $created_at;

    /**
     * The value for the updated_at field.
     *
     * @var        DateTime
     */
    protected $updated_at;

    /**
     * @var        CustomerTitle
     */
    protected $aCustomerTitle;

    /**
     * @var        Country
     */
    protected $aCountry;

    /**
     * @var        State
     */
    protected $aState;

    /**
     * @var        ObjectCollection|ChildCreditNote[] Collection to store aggregation of ChildCreditNote objects.
     */
    protected $collCreditNotes;
    protected $collCreditNotesPartial;

    /**
     * Flag to prevent endless save loop, if this object is referenced
     * by another object which falls in this transaction.
     *
     * @var boolean
     */
    protected $alreadyInSave = false;

    /**
     * An array of objects scheduled for deletion.
     * @var ObjectCollection|ChildCreditNote[]
     */
    protected $creditNotesScheduledForDeletion = null;

    /**
     * Initializes internal state of CreditNote\Model\Base\CreditNoteAddress object.
     */
    public function __construct()
    {
    }

    /**
     * Returns whether the object has been modified.
     *
     * @return boolean True if the object has been modified.
     */
    public function isModified()
    {
        return !!$this->modifiedColumns;
    }

    /**
     * Has specified column been modified?
     *
     * @param  string  $col column fully qualified name (TableMap::TYPE_COLNAME), e.g. Book::AUTHOR_ID
     * @return boolean True if $col has been modified.
     */
    public function isColumnModified($col)
    {
        return $this->modifiedColumns && isset($this->modifiedColumns[$col]);
    }

    /**
     * Get the columns that have been modified in this object.
     * @return array A unique list of the modified column names for this object.
     */
    public function getModifiedColumns()
    {
        return $this->modifiedColumns ? array_keys($this->modifiedColumns) : [];
    }

    /**
     * Returns whether the object has ever been saved.  This will
     * be false, if the object was retrieved from storage or was created
     * and then saved.
     *
     * @return boolean true, if the object has never been persisted.
     */
    public function isNew()
    {
        return $this->new;
    }

    /**
     * Setter for the isNew attribute.  This method will be called
     * by Propel-generated children and objects.
     *
     * @param boolean $b the state of the object.
     */
    public function setNew($b)
    {
        $this->new = (boolean) $b;
    }

    /**
     * Whether this object has been deleted.
     * @return boolean The deleted state of this object.
     */
    public function isDeleted()
    {
        return $this->deleted;
    }

    /**
     * Specify whether this object has been deleted.
     * @param  boolean $b The deleted state of this object.
     * @return void
     */
    public function setDeleted($b)
    {
        $this->deleted = (boolean) $b;
    }

    /**
     * Sets the modified state for the object to be false.
     * @param  string $col If supplied, only the specified column is reset.
     * @return void
     */
    public function resetModified($col = null)
    {
        if (null !== $col) {
            if (isset($this->modifiedColumns[$col])) {
                unset($this->modifiedColumns[$col]);
            }
        } else {
            $this->modifiedColumns = array();
        }
    }

    /**
     * Compares this with another <code>CreditNoteAddress</code> instance.  If
     * <code>obj</code> is an instance of <code>CreditNoteAddress</code>, delegates to
     * <code>equals(CreditNoteAddress)</code>.  Otherwise, returns <code>false</code>.
     *
     * @param  mixed   $obj The object to compare to.
     * @return boolean Whether equal to the object specified.
     */
    public function equals($obj)
    {
        if (!$obj instanceof static) {
            return false;
        }

        if ($this === $obj) {
            return true;
        }

        if (null === $this->getPrimaryKey() || null === $obj->getPrimaryKey()) {
            return false;
        }

        return $this->getPrimaryKey() === $obj->getPrimaryKey();
    }

    /**
     * Get the associative array of the virtual columns in this object
     *
     * @return array
     */
    public function getVirtualColumns()
    {
        return $this->virtualColumns;
    }

    /**
     * Checks the existence of a virtual column in this object
     *
     * @param  string  $name The virtual column name
     * @return boolean
     */
    public function hasVirtualColumn($name)
    {
        return array_key_exists($name, $this->virtualColumns);
    }

    /**
     * Get the value of a virtual column in this object
     *
     * @param  string $name The virtual column name
     * @return mixed
     *
     * @throws PropelException
     */
    public function getVirtualColumn($name)
    {
        if (!$this->hasVirtualColumn($name)) {
            throw new PropelException(sprintf('Cannot get value of inexistent virtual column %s.', $name));
        }

        return $this->virtualColumns[$name];
    }

    /**
     * Set the value of a virtual column in this object
     *
     * @param string $name  The virtual column name
     * @param mixed  $value The value to give to the virtual column
     *
     * @return $this|CreditNoteAddress The current object, for fluid interface
     */
    public function setVirtualColumn($name, $value)
    {
        $this->virtualColumns[$name] = $value;

        return $this;
    }

    /**
     * Logs a message using Propel::log().
     *
     * @param  string  $msg
     * @param  int     $priority One of the Propel::LOG_* logging levels
     * @return boolean
     */
    protected function log($msg, $priority = Propel::LOG_INFO)
    {
        return Propel::log(\get_class($this) . ': ' . $msg, $priority);
    }

    /**
     * Export the current object properties to a string, using a given parser format
     * <code>
     * $book = BookQuery::create()->findPk(9012);
     * echo $book->exportTo('JSON');
     *  => {"Id":9012,"Title":"Don Juan","ISBN":"0140422161","Price":12.99,"PublisherId":1234,"AuthorId":5678}');
     * </code>
     *
     * @param  mixed   $parser                 A AbstractParser instance, or a format name ('XML', 'YAML', 'JSON', 'CSV')
     * @param  boolean $includeLazyLoadColumns (optional) Whether to include lazy load(ed) columns. Defaults to TRUE.
     * @return string  The exported data
     */
    public function exportTo($parser, $includeLazyLoadColumns = true)
    {
        if (!$parser instanceof AbstractParser) {
            $parser = AbstractParser::getParser($parser);
        }

        return $parser->fromArray($this->toArray(TableMap::TYPE_PHPNAME, $includeLazyLoadColumns, array(), true));
    }

    /**
     * Clean up internal collections prior to serializing
     * Avoids recursive loops that turn into segmentation faults when serializing
     */
    public function __sleep()
    {
        $this->clearAllReferences();

        $cls = new \ReflectionClass($this);
        $propertyNames = [];
        $serializableProperties = array_diff($cls->getProperties(), $cls->getProperties(\ReflectionProperty::IS_STATIC));

        foreach($serializableProperties as $property) {
            $propertyNames[] = $property->getName();
        }

        return $propertyNames;
    }

    /**
     * Get the [id] column value.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get the [customer_title_id] column value.
     *
     * @return int
     */
    public function getCustomerTitleId()
    {
        return $this->customer_title_id;
    }

    /**
     * Get the [company] column value.
     *
     * @return string
     */
    public function getCompany()
    {
        return $this->company;
    }

    /**
     * Get the [firstname] column value.
     *
     * @return string
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * Get the [lastname] column value.
     *
     * @return string
     */
    public function getLastname()
    {
        return $this->lastname;
    }

    /**
     * Get the [address1] column value.
     *
     * @return string
     */
    public function getAddress1()
    {
        return $this->address1;
    }

    /**
     * Get the [address2] column value.
     *
     * @return string
     */
    public function getAddress2()
    {
        return $this->address2;
    }

    /**
     * Get the [address3] column value.
     *
     * @return string
     */
    public function getAddress3()
    {
        return $this->address3;
    }

    /**
     * Get the [zipcode] column value.
     *
     * @return string
     */
    public function getZipcode()
    {
        return $this->zipcode;
    }

    /**
     * Get the [city] column value.
     *
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Get the [phone] column value.
     *
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Get the [cellphone] column value.
     *
     * @return string
     */
    public function getCellphone()
    {
        return $this->cellphone;
    }

    /**
     * Get the [country_id] column value.
     *
     * @return int
     */
    public function getCountryId()
    {
        return $this->country_id;
    }

    /**
     * Get the [state_id] column value.
     *
     * @return int
     */
    public function getStateId()
    {
        return $this->state_id;
    }

    /**
     * Get the [optionally formatted] temporal [created_at] column value.
     *
     *
     * @param      string|null $format The date/time format string (either date()-style or strftime()-style).
     *                            If format is NULL, then the raw DateTime object will be returned.
     *
     * @return string|DateTime Formatted date/time value as string or DateTime object (if format is NULL), NULL if column is NULL, and 0 if column value is 0000-00-00 00:00:00
     *
     * @throws PropelException - if unable to parse/validate the date/time value.
     */
    public function getCreatedAt($format = NULL)
    {
        if ($format === null) {
            return $this->created_at;
        } else {
            return $this->created_at instanceof \DateTimeInterface ? $this->created_at->format($format) : null;
        }
    }

    /**
     * Get the [optionally formatted] temporal [updated_at] column value.
     *
     *
     * @param      string|null $format The date/time format string (either date()-style or strftime()-style).
     *                            If format is NULL, then the raw DateTime object will be returned.
     *
     * @return string|DateTime Formatted date/time value as string or DateTime object (if format is NULL), NULL if column is NULL, and 0 if column value is 0000-00-00 00:00:00
     *
     * @throws PropelException - if unable to parse/validate the date/time value.
     */
    public function getUpdatedAt($format = NULL)
    {
        if ($format === null) {
            return $this->updated_at;
        } else {
            return $this->updated_at instanceof \DateTimeInterface ? $this->updated_at->format($format) : null;
        }
    }

    /**
     * Set the value of [id] column.
     *
     * @param int $v new value
     * @return $this|\CreditNote\Model\CreditNoteAddress The current object (for fluent API support)
     */
    public function setId($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->id !== $v) {
            $this->id = $v;
            $this->modifiedColumns[CreditNoteAddressTableMap::COL_ID] = true;
        }

        return $this;
    } // setId()

    /**
     * Set the value of [customer_title_id] column.
     *
     * @param int $v new value
     * @return $this|\CreditNote\Model\CreditNoteAddress The current object (for fluent API support)
     */
    public function setCustomerTitleId($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->customer_title_id !== $v) {
            $this->customer_title_id = $v;
            $this->modifiedColumns[CreditNoteAddressTableMap::COL_CUSTOMER_TITLE_ID] = true;
        }

        if ($this->aCustomerTitle !== null && $this->aCustomerTitle->getId() !== $v) {
            $this->aCustomerTitle = null;
        }

        return $this;
    } // setCustomerTitleId()

    /**
     * Set the value of [company] column.
     *
     * @param string $v new value
     * @return $this|\CreditNote\Model\CreditNoteAddress The current object (for fluent API support)
     */
    public function setCompany($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->company !== $v) {
            $this->company = $v;
            $this->modifiedColumns[CreditNoteAddressTableMap::COL_COMPANY] = true;
        }

        return $this;
    } // setCompany()

    /**
     * Set the value of [firstname] column.
     *
     * @param string $v new value
     * @return $this|\CreditNote\Model\CreditNoteAddress The current object (for fluent API support)
     */
    public function setFirstname($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->firstname !== $v) {
            $this->firstname = $v;
            $this->modifiedColumns[CreditNoteAddressTableMap::COL_FIRSTNAME] = true;
        }

        return $this;
    } // setFirstname()

    /**
     * Set the value of [lastname] column.
     *
     * @param string $v new value
     * @return $this|\CreditNote\Model\CreditNoteAddress The current object (for fluent API support)
     */
    public function setLastname($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->lastname !== $v) {
            $this->lastname = $v;
            $this->modifiedColumns[CreditNoteAddressTableMap::COL_LASTNAME] = true;
        }

        return $this;
    } // setLastname()

    /**
     * Set the value of [address1] column.
     *
     * @param string $v new value
     * @return $this|\CreditNote\Model\CreditNoteAddress The current object (for fluent API support)
     */
    public function setAddress1($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->address1 !== $v) {
            $this->address1 = $v;
            $this->modifiedColumns[CreditNoteAddressTableMap::COL_ADDRESS1] = true;
        }

        return $this;
    } // setAddress1()

    /**
     * Set the value of [address2] column.
     *
     * @param string $v new value
     * @return $this|\CreditNote\Model\CreditNoteAddress The current object (for fluent API support)
     */
    public function setAddress2($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->address2 !== $v) {
            $this->address2 = $v;
            $this->modifiedColumns[CreditNoteAddressTableMap::COL_ADDRESS2] = true;
        }

        return $this;
    } // setAddress2()

    /**
     * Set the value of [address3] column.
     *
     * @param string $v new value
     * @return $this|\CreditNote\Model\CreditNoteAddress The current object (for fluent API support)
     */
    public function setAddress3($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->address3 !== $v) {
            $this->address3 = $v;
            $this->modifiedColumns[CreditNoteAddressTableMap::COL_ADDRESS3] = true;
        }

        return $this;
    } // setAddress3()

    /**
     * Set the value of [zipcode] column.
     *
     * @param string $v new value
     * @return $this|\CreditNote\Model\CreditNoteAddress The current object (for fluent API support)
     */
    public function setZipcode($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->zipcode !== $v) {
            $this->zipcode = $v;
            $this->modifiedColumns[CreditNoteAddressTableMap::COL_ZIPCODE] = true;
        }

        return $this;
    } // setZipcode()

    /**
     * Set the value of [city] column.
     *
     * @param string $v new value
     * @return $this|\CreditNote\Model\CreditNoteAddress The current object (for fluent API support)
     */
    public function setCity($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->city !== $v) {
            $this->city = $v;
            $this->modifiedColumns[CreditNoteAddressTableMap::COL_CITY] = true;
        }

        return $this;
    } // setCity()

    /**
     * Set the value of [phone] column.
     *
     * @param string $v new value
     * @return $this|\CreditNote\Model\CreditNoteAddress The current object (for fluent API support)
     */
    public function setPhone($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->phone !== $v) {
            $this->phone = $v;
            $this->modifiedColumns[CreditNoteAddressTableMap::COL_PHONE] = true;
        }

        return $this;
    } // setPhone()

    /**
     * Set the value of [cellphone] column.
     *
     * @param string $v new value
     * @return $this|\CreditNote\Model\CreditNoteAddress The current object (for fluent API support)
     */
    public function setCellphone($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->cellphone !== $v) {
            $this->cellphone = $v;
            $this->modifiedColumns[CreditNoteAddressTableMap::COL_CELLPHONE] = true;
        }

        return $this;
    } // setCellphone()

    /**
     * Set the value of [country_id] column.
     *
     * @param int $v new value
     * @return $this|\CreditNote\Model\CreditNoteAddress The current object (for fluent API support)
     */
    public function setCountryId($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->country_id !== $v) {
            $this->country_id = $v;
            $this->modifiedColumns[CreditNoteAddressTableMap::COL_COUNTRY_ID] = true;
        }

        if ($this->aCountry !== null && $this->aCountry->getId() !== $v) {
            $this->aCountry = null;
        }

        return $this;
    } // setCountryId()

    /**
     * Set the value of [state_id] column.
     *
     * @param int $v new value
     * @return $this|\CreditNote\Model\CreditNoteAddress The current object (for fluent API support)
     */
    public function setStateId($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->state_id !== $v) {
            $this->state_id = $v;
            $this->modifiedColumns[CreditNoteAddressTableMap::COL_STATE_ID] = true;
        }

        if ($this->aState !== null && $this->aState->getId() !== $v) {
            $this->aState = null;
        }

        return $this;
    } // setStateId()

    /**
     * Sets the value of [created_at] column to a normalized version of the date/time value specified.
     *
     * @param  mixed $v string, integer (timestamp), or \DateTimeInterface value.
     *               Empty strings are treated as NULL.
     * @return $this|\CreditNote\Model\CreditNoteAddress The current object (for fluent API support)
     */
    public function setCreatedAt($v)
    {
        $dt = PropelDateTime::newInstance($v, null, 'DateTime');
        if ($this->created_at !== null || $dt !== null) {
            if ($this->created_at === null || $dt === null || $dt->format("Y-m-d H:i:s.u") !== $this->created_at->format("Y-m-d H:i:s.u")) {
                $this->created_at = $dt === null ? null : clone $dt;
                $this->modifiedColumns[CreditNoteAddressTableMap::COL_CREATED_AT] = true;
            }
        } // if either are not null

        return $this;
    } // setCreatedAt()

    /**
     * Sets the value of [updated_at] column to a normalized version of the date/time value specified.
     *
     * @param  mixed $v string, integer (timestamp), or \DateTimeInterface value.
     *               Empty strings are treated as NULL.
     * @return $this|\CreditNote\Model\CreditNoteAddress The current object (for fluent API support)
     */
    public function setUpdatedAt($v)
    {
        $dt = PropelDateTime::newInstance($v, null, 'DateTime');
        if ($this->updated_at !== null || $dt !== null) {
            if ($this->updated_at === null || $dt === null || $dt->format("Y-m-d H:i:s.u") !== $this->updated_at->format("Y-m-d H:i:s.u")) {
                $this->updated_at = $dt === null ? null : clone $dt;
                $this->modifiedColumns[CreditNoteAddressTableMap::COL_UPDATED_AT] = true;
            }
        } // if either are not null

        return $this;
    } // setUpdatedAt()

    /**
     * Indicates whether the columns in this object are only set to default values.
     *
     * This method can be used in conjunction with isModified() to indicate whether an object is both
     * modified _and_ has some values set which are non-default.
     *
     * @return boolean Whether the columns in this object are only been set with default values.
     */
    public function hasOnlyDefaultValues()
    {
        // otherwise, everything was equal, so return TRUE
        return true;
    } // hasOnlyDefaultValues()

    /**
     * Hydrates (populates) the object variables with values from the database resultset.
     *
     * An offset (0-based "start column") is specified so that objects can be hydrated
     * with a subset of the columns in the resultset rows.  This is needed, for example,
     * for results of JOIN queries where the resultset row includes columns from two or
     * more tables.
     *
     * @param array   $row       The row returned by DataFetcher->fetch().
     * @param int     $startcol  0-based offset column which indicates which restultset column to start with.
     * @param boolean $rehydrate Whether this object is being re-hydrated from the database.
     * @param string  $indexType The index type of $row. Mostly DataFetcher->getIndexType().
                                  One of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_CAMELNAME
     *                            TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM.
     *
     * @return int             next starting column
     * @throws PropelException - Any caught Exception will be rewrapped as a PropelException.
     */
    public function hydrate($row, $startcol = 0, $rehydrate = false, $indexType = TableMap::TYPE_NUM)
    {
        try {

            $col = $row[TableMap::TYPE_NUM == $indexType ? 0 + $startcol : CreditNoteAddressTableMap::translateFieldName('Id', TableMap::TYPE_PHPNAME, $indexType)];
            $this->id = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 1 + $startcol : CreditNoteAddressTableMap::translateFieldName('CustomerTitleId', TableMap::TYPE_PHPNAME, $indexType)];
            $this->customer_title_id = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 2 + $startcol : CreditNoteAddressTableMap::translateFieldName('Company', TableMap::TYPE_PHPNAME, $indexType)];
            $this->company = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 3 + $startcol : CreditNoteAddressTableMap::translateFieldName('Firstname', TableMap::TYPE_PHPNAME, $indexType)];
            $this->firstname = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 4 + $startcol : CreditNoteAddressTableMap::translateFieldName('Lastname', TableMap::TYPE_PHPNAME, $indexType)];
            $this->lastname = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 5 + $startcol : CreditNoteAddressTableMap::translateFieldName('Address1', TableMap::TYPE_PHPNAME, $indexType)];
            $this->address1 = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 6 + $startcol : CreditNoteAddressTableMap::translateFieldName('Address2', TableMap::TYPE_PHPNAME, $indexType)];
            $this->address2 = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 7 + $startcol : CreditNoteAddressTableMap::translateFieldName('Address3', TableMap::TYPE_PHPNAME, $indexType)];
            $this->address3 = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 8 + $startcol : CreditNoteAddressTableMap::translateFieldName('Zipcode', TableMap::TYPE_PHPNAME, $indexType)];
            $this->zipcode = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 9 + $startcol : CreditNoteAddressTableMap::translateFieldName('City', TableMap::TYPE_PHPNAME, $indexType)];
            $this->city = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 10 + $startcol : CreditNoteAddressTableMap::translateFieldName('Phone', TableMap::TYPE_PHPNAME, $indexType)];
            $this->phone = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 11 + $startcol : CreditNoteAddressTableMap::translateFieldName('Cellphone', TableMap::TYPE_PHPNAME, $indexType)];
            $this->cellphone = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 12 + $startcol : CreditNoteAddressTableMap::translateFieldName('CountryId', TableMap::TYPE_PHPNAME, $indexType)];
            $this->country_id = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 13 + $startcol : CreditNoteAddressTableMap::translateFieldName('StateId', TableMap::TYPE_PHPNAME, $indexType)];
            $this->state_id = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 14 + $startcol : CreditNoteAddressTableMap::translateFieldName('CreatedAt', TableMap::TYPE_PHPNAME, $indexType)];
            if ($col === '0000-00-00 00:00:00') {
                $col = null;
            }
            $this->created_at = (null !== $col) ? PropelDateTime::newInstance($col, null, 'DateTime') : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 15 + $startcol : CreditNoteAddressTableMap::translateFieldName('UpdatedAt', TableMap::TYPE_PHPNAME, $indexType)];
            if ($col === '0000-00-00 00:00:00') {
                $col = null;
            }
            $this->updated_at = (null !== $col) ? PropelDateTime::newInstance($col, null, 'DateTime') : null;
            $this->resetModified();

            $this->setNew(false);

            if ($rehydrate) {
                $this->ensureConsistency();
            }

            return $startcol + 16; // 16 = CreditNoteAddressTableMap::NUM_HYDRATE_COLUMNS.

        } catch (Exception $e) {
            throw new PropelException(sprintf('Error populating %s object', '\\CreditNote\\Model\\CreditNoteAddress'), 0, $e);
        }
    }

    /**
     * Checks and repairs the internal consistency of the object.
     *
     * This method is executed after an already-instantiated object is re-hydrated
     * from the database.  It exists to check any foreign keys to make sure that
     * the objects related to the current object are correct based on foreign key.
     *
     * You can override this method in the stub class, but you should always invoke
     * the base method from the overridden method (i.e. parent::ensureConsistency()),
     * in case your model changes.
     *
     * @throws PropelException
     */
    public function ensureConsistency()
    {
        if ($this->aCustomerTitle !== null && $this->customer_title_id !== $this->aCustomerTitle->getId()) {
            $this->aCustomerTitle = null;
        }
        if ($this->aCountry !== null && $this->country_id !== $this->aCountry->getId()) {
            $this->aCountry = null;
        }
        if ($this->aState !== null && $this->state_id !== $this->aState->getId()) {
            $this->aState = null;
        }
    } // ensureConsistency

    /**
     * Reloads this object from datastore based on primary key and (optionally) resets all associated objects.
     *
     * This will only work if the object has been saved and has a valid primary key set.
     *
     * @param      boolean $deep (optional) Whether to also de-associated any related objects.
     * @param      ConnectionInterface $con (optional) The ConnectionInterface connection to use.
     * @return void
     * @throws PropelException - if this object is deleted, unsaved or doesn't have pk match in db
     */
    public function reload($deep = false, ConnectionInterface $con = null)
    {
        if ($this->isDeleted()) {
            throw new PropelException("Cannot reload a deleted object.");
        }

        if ($this->isNew()) {
            throw new PropelException("Cannot reload an unsaved object.");
        }

        if ($con === null) {
            $con = Propel::getServiceContainer()->getReadConnection(CreditNoteAddressTableMap::DATABASE_NAME);
        }

        // We don't need to alter the object instance pool; we're just modifying this instance
        // already in the pool.

        $dataFetcher = ChildCreditNoteAddressQuery::create(null, $this->buildPkeyCriteria())->setFormatter(ModelCriteria::FORMAT_STATEMENT)->find($con);
        $row = $dataFetcher->fetch();
        $dataFetcher->close();
        if (!$row) {
            throw new PropelException('Cannot find matching row in the database to reload object values.');
        }
        $this->hydrate($row, 0, true, $dataFetcher->getIndexType()); // rehydrate

        if ($deep) {  // also de-associate any related objects?

            $this->aCustomerTitle = null;
            $this->aCountry = null;
            $this->aState = null;
            $this->collCreditNotes = null;

        } // if (deep)
    }

    /**
     * Removes this object from datastore and sets delete attribute.
     *
     * @param      ConnectionInterface $con
     * @return void
     * @throws PropelException
     * @see CreditNoteAddress::setDeleted()
     * @see CreditNoteAddress::isDeleted()
     */
    public function delete(ConnectionInterface $con = null)
    {
        if ($this->isDeleted()) {
            throw new PropelException("This object has already been deleted.");
        }

        if ($con === null) {
            $con = Propel::getServiceContainer()->getWriteConnection(CreditNoteAddressTableMap::DATABASE_NAME);
        }

        $con->transaction(function () use ($con) {
            $deleteQuery = ChildCreditNoteAddressQuery::create()
                ->filterByPrimaryKey($this->getPrimaryKey());
            $ret = $this->preDelete($con);
            if ($ret) {
                $deleteQuery->delete($con);
                $this->postDelete($con);
                $this->setDeleted(true);
            }
        });
    }

    /**
     * Persists this object to the database.
     *
     * If the object is new, it inserts it; otherwise an update is performed.
     * All modified related objects will also be persisted in the doSave()
     * method.  This method wraps all precipitate database operations in a
     * single transaction.
     *
     * @param      ConnectionInterface $con
     * @return int             The number of rows affected by this insert/update and any referring fk objects' save() operations.
     * @throws PropelException
     * @see doSave()
     */
    public function save(ConnectionInterface $con = null)
    {
        if ($this->isDeleted()) {
            throw new PropelException("You cannot save an object that has been deleted.");
        }

        if ($this->alreadyInSave) {
            return 0;
        }

        if ($con === null) {
            $con = Propel::getServiceContainer()->getWriteConnection(CreditNoteAddressTableMap::DATABASE_NAME);
        }

        return $con->transaction(function () use ($con) {
            $ret = $this->preSave($con);
            $isInsert = $this->isNew();
            if ($isInsert) {
                $ret = $ret && $this->preInsert($con);
                // timestampable behavior
                $time = time();
                $highPrecision = \Propel\Runtime\Util\PropelDateTime::createHighPrecision();
                if (!$this->isColumnModified(CreditNoteAddressTableMap::COL_CREATED_AT)) {
                    $this->setCreatedAt($highPrecision);
                }
                if (!$this->isColumnModified(CreditNoteAddressTableMap::COL_UPDATED_AT)) {
                    $this->setUpdatedAt($highPrecision);
                }
            } else {
                $ret = $ret && $this->preUpdate($con);
                // timestampable behavior
                if ($this->isModified() && !$this->isColumnModified(CreditNoteAddressTableMap::COL_UPDATED_AT)) {
                    $this->setUpdatedAt(\Propel\Runtime\Util\PropelDateTime::createHighPrecision());
                }
            }
            if ($ret) {
                $affectedRows = $this->doSave($con);
                if ($isInsert) {
                    $this->postInsert($con);
                } else {
                    $this->postUpdate($con);
                }
                $this->postSave($con);
                CreditNoteAddressTableMap::addInstanceToPool($this);
            } else {
                $affectedRows = 0;
            }

            return $affectedRows;
        });
    }

    /**
     * Performs the work of inserting or updating the row in the database.
     *
     * If the object is new, it inserts it; otherwise an update is performed.
     * All related objects are also updated in this method.
     *
     * @param      ConnectionInterface $con
     * @return int             The number of rows affected by this insert/update and any referring fk objects' save() operations.
     * @throws PropelException
     * @see save()
     */
    protected function doSave(ConnectionInterface $con)
    {
        $affectedRows = 0; // initialize var to track total num of affected rows
        if (!$this->alreadyInSave) {
            $this->alreadyInSave = true;

            // We call the save method on the following object(s) if they
            // were passed to this object by their corresponding set
            // method.  This object relates to these object(s) by a
            // foreign key reference.

            if ($this->aCustomerTitle !== null) {
                if ($this->aCustomerTitle->isModified() || $this->aCustomerTitle->isNew()) {
                    $affectedRows += $this->aCustomerTitle->save($con);
                }
                $this->setCustomerTitle($this->aCustomerTitle);
            }

            if ($this->aCountry !== null) {
                if ($this->aCountry->isModified() || $this->aCountry->isNew()) {
                    $affectedRows += $this->aCountry->save($con);
                }
                $this->setCountry($this->aCountry);
            }

            if ($this->aState !== null) {
                if ($this->aState->isModified() || $this->aState->isNew()) {
                    $affectedRows += $this->aState->save($con);
                }
                $this->setState($this->aState);
            }

            if ($this->isNew() || $this->isModified()) {
                // persist changes
                if ($this->isNew()) {
                    $this->doInsert($con);
                    $affectedRows += 1;
                } else {
                    $affectedRows += $this->doUpdate($con);
                }
                $this->resetModified();
            }

            if ($this->creditNotesScheduledForDeletion !== null) {
                if (!$this->creditNotesScheduledForDeletion->isEmpty()) {
                    \CreditNote\Model\CreditNoteQuery::create()
                        ->filterByPrimaryKeys($this->creditNotesScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->creditNotesScheduledForDeletion = null;
                }
            }

            if ($this->collCreditNotes !== null) {
                foreach ($this->collCreditNotes as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            $this->alreadyInSave = false;

        }

        return $affectedRows;
    } // doSave()

    /**
     * Insert the row in the database.
     *
     * @param      ConnectionInterface $con
     *
     * @throws PropelException
     * @see doSave()
     */
    protected function doInsert(ConnectionInterface $con)
    {
        $modifiedColumns = array();
        $index = 0;

        $this->modifiedColumns[CreditNoteAddressTableMap::COL_ID] = true;
        if (null !== $this->id) {
            throw new PropelException('Cannot insert a value for auto-increment primary key (' . CreditNoteAddressTableMap::COL_ID . ')');
        }

         // check the columns in natural order for more readable SQL queries
        if ($this->isColumnModified(CreditNoteAddressTableMap::COL_ID)) {
            $modifiedColumns[':p' . $index++]  = '`id`';
        }
        if ($this->isColumnModified(CreditNoteAddressTableMap::COL_CUSTOMER_TITLE_ID)) {
            $modifiedColumns[':p' . $index++]  = '`customer_title_id`';
        }
        if ($this->isColumnModified(CreditNoteAddressTableMap::COL_COMPANY)) {
            $modifiedColumns[':p' . $index++]  = '`company`';
        }
        if ($this->isColumnModified(CreditNoteAddressTableMap::COL_FIRSTNAME)) {
            $modifiedColumns[':p' . $index++]  = '`firstname`';
        }
        if ($this->isColumnModified(CreditNoteAddressTableMap::COL_LASTNAME)) {
            $modifiedColumns[':p' . $index++]  = '`lastname`';
        }
        if ($this->isColumnModified(CreditNoteAddressTableMap::COL_ADDRESS1)) {
            $modifiedColumns[':p' . $index++]  = '`address1`';
        }
        if ($this->isColumnModified(CreditNoteAddressTableMap::COL_ADDRESS2)) {
            $modifiedColumns[':p' . $index++]  = '`address2`';
        }
        if ($this->isColumnModified(CreditNoteAddressTableMap::COL_ADDRESS3)) {
            $modifiedColumns[':p' . $index++]  = '`address3`';
        }
        if ($this->isColumnModified(CreditNoteAddressTableMap::COL_ZIPCODE)) {
            $modifiedColumns[':p' . $index++]  = '`zipcode`';
        }
        if ($this->isColumnModified(CreditNoteAddressTableMap::COL_CITY)) {
            $modifiedColumns[':p' . $index++]  = '`city`';
        }
        if ($this->isColumnModified(CreditNoteAddressTableMap::COL_PHONE)) {
            $modifiedColumns[':p' . $index++]  = '`phone`';
        }
        if ($this->isColumnModified(CreditNoteAddressTableMap::COL_CELLPHONE)) {
            $modifiedColumns[':p' . $index++]  = '`cellphone`';
        }
        if ($this->isColumnModified(CreditNoteAddressTableMap::COL_COUNTRY_ID)) {
            $modifiedColumns[':p' . $index++]  = '`country_id`';
        }
        if ($this->isColumnModified(CreditNoteAddressTableMap::COL_STATE_ID)) {
            $modifiedColumns[':p' . $index++]  = '`state_id`';
        }
        if ($this->isColumnModified(CreditNoteAddressTableMap::COL_CREATED_AT)) {
            $modifiedColumns[':p' . $index++]  = '`created_at`';
        }
        if ($this->isColumnModified(CreditNoteAddressTableMap::COL_UPDATED_AT)) {
            $modifiedColumns[':p' . $index++]  = '`updated_at`';
        }

        $sql = sprintf(
            'INSERT INTO `credit_note_address` (%s) VALUES (%s)',
            implode(', ', $modifiedColumns),
            implode(', ', array_keys($modifiedColumns))
        );

        try {
            $stmt = $con->prepare($sql);
            foreach ($modifiedColumns as $identifier => $columnName) {
                switch ($columnName) {
                    case '`id`':
                        $stmt->bindValue($identifier, $this->id, PDO::PARAM_INT);
                        break;
                    case '`customer_title_id`':
                        $stmt->bindValue($identifier, $this->customer_title_id, PDO::PARAM_INT);
                        break;
                    case '`company`':
                        $stmt->bindValue($identifier, $this->company, PDO::PARAM_STR);
                        break;
                    case '`firstname`':
                        $stmt->bindValue($identifier, $this->firstname, PDO::PARAM_STR);
                        break;
                    case '`lastname`':
                        $stmt->bindValue($identifier, $this->lastname, PDO::PARAM_STR);
                        break;
                    case '`address1`':
                        $stmt->bindValue($identifier, $this->address1, PDO::PARAM_STR);
                        break;
                    case '`address2`':
                        $stmt->bindValue($identifier, $this->address2, PDO::PARAM_STR);
                        break;
                    case '`address3`':
                        $stmt->bindValue($identifier, $this->address3, PDO::PARAM_STR);
                        break;
                    case '`zipcode`':
                        $stmt->bindValue($identifier, $this->zipcode, PDO::PARAM_STR);
                        break;
                    case '`city`':
                        $stmt->bindValue($identifier, $this->city, PDO::PARAM_STR);
                        break;
                    case '`phone`':
                        $stmt->bindValue($identifier, $this->phone, PDO::PARAM_STR);
                        break;
                    case '`cellphone`':
                        $stmt->bindValue($identifier, $this->cellphone, PDO::PARAM_STR);
                        break;
                    case '`country_id`':
                        $stmt->bindValue($identifier, $this->country_id, PDO::PARAM_INT);
                        break;
                    case '`state_id`':
                        $stmt->bindValue($identifier, $this->state_id, PDO::PARAM_INT);
                        break;
                    case '`created_at`':
                        $stmt->bindValue($identifier, $this->created_at ? $this->created_at->format("Y-m-d H:i:s.u") : null, PDO::PARAM_STR);
                        break;
                    case '`updated_at`':
                        $stmt->bindValue($identifier, $this->updated_at ? $this->updated_at->format("Y-m-d H:i:s.u") : null, PDO::PARAM_STR);
                        break;
                }
            }
            $stmt->execute();
        } catch (Exception $e) {
            Propel::log($e->getMessage(), Propel::LOG_ERR);
            throw new PropelException(sprintf('Unable to execute INSERT statement [%s]', $sql), 0, $e);
        }

        try {
            $pk = $con->lastInsertId();
        } catch (Exception $e) {
            throw new PropelException('Unable to get autoincrement id.', 0, $e);
        }
        $this->setId($pk);

        $this->setNew(false);
    }

    /**
     * Update the row in the database.
     *
     * @param      ConnectionInterface $con
     *
     * @return Integer Number of updated rows
     * @see doSave()
     */
    protected function doUpdate(ConnectionInterface $con)
    {
        $selectCriteria = $this->buildPkeyCriteria();
        $valuesCriteria = $this->buildCriteria();

        return $selectCriteria->doUpdate($valuesCriteria, $con);
    }

    /**
     * Retrieves a field from the object by name passed in as a string.
     *
     * @param      string $name name
     * @param      string $type The type of fieldname the $name is of:
     *                     one of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_CAMELNAME
     *                     TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM.
     *                     Defaults to TableMap::TYPE_PHPNAME.
     * @return mixed Value of field.
     */
    public function getByName($name, $type = TableMap::TYPE_PHPNAME)
    {
        $pos = CreditNoteAddressTableMap::translateFieldName($name, $type, TableMap::TYPE_NUM);
        $field = $this->getByPosition($pos);

        return $field;
    }

    /**
     * Retrieves a field from the object by Position as specified in the xml schema.
     * Zero-based.
     *
     * @param      int $pos position in xml schema
     * @return mixed Value of field at $pos
     */
    public function getByPosition($pos)
    {
        switch ($pos) {
            case 0:
                return $this->getId();
                break;
            case 1:
                return $this->getCustomerTitleId();
                break;
            case 2:
                return $this->getCompany();
                break;
            case 3:
                return $this->getFirstname();
                break;
            case 4:
                return $this->getLastname();
                break;
            case 5:
                return $this->getAddress1();
                break;
            case 6:
                return $this->getAddress2();
                break;
            case 7:
                return $this->getAddress3();
                break;
            case 8:
                return $this->getZipcode();
                break;
            case 9:
                return $this->getCity();
                break;
            case 10:
                return $this->getPhone();
                break;
            case 11:
                return $this->getCellphone();
                break;
            case 12:
                return $this->getCountryId();
                break;
            case 13:
                return $this->getStateId();
                break;
            case 14:
                return $this->getCreatedAt();
                break;
            case 15:
                return $this->getUpdatedAt();
                break;
            default:
                return null;
                break;
        } // switch()
    }

    /**
     * Exports the object as an array.
     *
     * You can specify the key type of the array by passing one of the class
     * type constants.
     *
     * @param     string  $keyType (optional) One of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_CAMELNAME,
     *                    TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM.
     *                    Defaults to TableMap::TYPE_PHPNAME.
     * @param     boolean $includeLazyLoadColumns (optional) Whether to include lazy loaded columns. Defaults to TRUE.
     * @param     array $alreadyDumpedObjects List of objects to skip to avoid recursion
     * @param     boolean $includeForeignObjects (optional) Whether to include hydrated related objects. Default to FALSE.
     *
     * @return array an associative array containing the field names (as keys) and field values
     */
    public function toArray($keyType = TableMap::TYPE_PHPNAME, $includeLazyLoadColumns = true, $alreadyDumpedObjects = array(), $includeForeignObjects = false)
    {

        if (isset($alreadyDumpedObjects['CreditNoteAddress'][$this->hashCode()])) {
            return '*RECURSION*';
        }
        $alreadyDumpedObjects['CreditNoteAddress'][$this->hashCode()] = true;
        $keys = CreditNoteAddressTableMap::getFieldNames($keyType);
        $result = array(
            $keys[0] => $this->getId(),
            $keys[1] => $this->getCustomerTitleId(),
            $keys[2] => $this->getCompany(),
            $keys[3] => $this->getFirstname(),
            $keys[4] => $this->getLastname(),
            $keys[5] => $this->getAddress1(),
            $keys[6] => $this->getAddress2(),
            $keys[7] => $this->getAddress3(),
            $keys[8] => $this->getZipcode(),
            $keys[9] => $this->getCity(),
            $keys[10] => $this->getPhone(),
            $keys[11] => $this->getCellphone(),
            $keys[12] => $this->getCountryId(),
            $keys[13] => $this->getStateId(),
            $keys[14] => $this->getCreatedAt(),
            $keys[15] => $this->getUpdatedAt(),
        );
        if ($result[$keys[14]] instanceof \DateTimeInterface) {
            $result[$keys[14]] = $result[$keys[14]]->format('c');
        }

        if ($result[$keys[15]] instanceof \DateTimeInterface) {
            $result[$keys[15]] = $result[$keys[15]]->format('c');
        }

        $virtualColumns = $this->virtualColumns;
        foreach ($virtualColumns as $key => $virtualColumn) {
            $result[$key] = $virtualColumn;
        }

        if ($includeForeignObjects) {
            if (null !== $this->aCustomerTitle) {

                switch ($keyType) {
                    case TableMap::TYPE_CAMELNAME:
                        $key = 'customerTitle';
                        break;
                    case TableMap::TYPE_FIELDNAME:
                        $key = 'customer_title';
                        break;
                    default:
                        $key = 'CustomerTitle';
                }

                $result[$key] = $this->aCustomerTitle->toArray($keyType, $includeLazyLoadColumns,  $alreadyDumpedObjects, true);
            }
            if (null !== $this->aCountry) {

                switch ($keyType) {
                    case TableMap::TYPE_CAMELNAME:
                        $key = 'country';
                        break;
                    case TableMap::TYPE_FIELDNAME:
                        $key = 'country';
                        break;
                    default:
                        $key = 'Country';
                }

                $result[$key] = $this->aCountry->toArray($keyType, $includeLazyLoadColumns,  $alreadyDumpedObjects, true);
            }
            if (null !== $this->aState) {

                switch ($keyType) {
                    case TableMap::TYPE_CAMELNAME:
                        $key = 'state';
                        break;
                    case TableMap::TYPE_FIELDNAME:
                        $key = 'state';
                        break;
                    default:
                        $key = 'State';
                }

                $result[$key] = $this->aState->toArray($keyType, $includeLazyLoadColumns,  $alreadyDumpedObjects, true);
            }
            if (null !== $this->collCreditNotes) {

                switch ($keyType) {
                    case TableMap::TYPE_CAMELNAME:
                        $key = 'creditNotes';
                        break;
                    case TableMap::TYPE_FIELDNAME:
                        $key = 'credit_notes';
                        break;
                    default:
                        $key = 'CreditNotes';
                }

                $result[$key] = $this->collCreditNotes->toArray(null, false, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
        }

        return $result;
    }

    /**
     * Sets a field from the object by name passed in as a string.
     *
     * @param  string $name
     * @param  mixed  $value field value
     * @param  string $type The type of fieldname the $name is of:
     *                one of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_CAMELNAME
     *                TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM.
     *                Defaults to TableMap::TYPE_PHPNAME.
     * @return $this|\CreditNote\Model\CreditNoteAddress
     */
    public function setByName($name, $value, $type = TableMap::TYPE_PHPNAME)
    {
        $pos = CreditNoteAddressTableMap::translateFieldName($name, $type, TableMap::TYPE_NUM);

        return $this->setByPosition($pos, $value);
    }

    /**
     * Sets a field from the object by Position as specified in the xml schema.
     * Zero-based.
     *
     * @param  int $pos position in xml schema
     * @param  mixed $value field value
     * @return $this|\CreditNote\Model\CreditNoteAddress
     */
    public function setByPosition($pos, $value)
    {
        switch ($pos) {
            case 0:
                $this->setId($value);
                break;
            case 1:
                $this->setCustomerTitleId($value);
                break;
            case 2:
                $this->setCompany($value);
                break;
            case 3:
                $this->setFirstname($value);
                break;
            case 4:
                $this->setLastname($value);
                break;
            case 5:
                $this->setAddress1($value);
                break;
            case 6:
                $this->setAddress2($value);
                break;
            case 7:
                $this->setAddress3($value);
                break;
            case 8:
                $this->setZipcode($value);
                break;
            case 9:
                $this->setCity($value);
                break;
            case 10:
                $this->setPhone($value);
                break;
            case 11:
                $this->setCellphone($value);
                break;
            case 12:
                $this->setCountryId($value);
                break;
            case 13:
                $this->setStateId($value);
                break;
            case 14:
                $this->setCreatedAt($value);
                break;
            case 15:
                $this->setUpdatedAt($value);
                break;
        } // switch()

        return $this;
    }

    /**
     * Populates the object using an array.
     *
     * This is particularly useful when populating an object from one of the
     * request arrays (e.g. $_POST).  This method goes through the column
     * names, checking to see whether a matching key exists in populated
     * array. If so the setByName() method is called for that column.
     *
     * You can specify the key type of the array by additionally passing one
     * of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_CAMELNAME,
     * TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM.
     * The default key type is the column's TableMap::TYPE_PHPNAME.
     *
     * @param      array  $arr     An array to populate the object from.
     * @param      string $keyType The type of keys the array uses.
     * @return void
     */
    public function fromArray($arr, $keyType = TableMap::TYPE_PHPNAME)
    {
        $keys = CreditNoteAddressTableMap::getFieldNames($keyType);

        if (array_key_exists($keys[0], $arr)) {
            $this->setId($arr[$keys[0]]);
        }
        if (array_key_exists($keys[1], $arr)) {
            $this->setCustomerTitleId($arr[$keys[1]]);
        }
        if (array_key_exists($keys[2], $arr)) {
            $this->setCompany($arr[$keys[2]]);
        }
        if (array_key_exists($keys[3], $arr)) {
            $this->setFirstname($arr[$keys[3]]);
        }
        if (array_key_exists($keys[4], $arr)) {
            $this->setLastname($arr[$keys[4]]);
        }
        if (array_key_exists($keys[5], $arr)) {
            $this->setAddress1($arr[$keys[5]]);
        }
        if (array_key_exists($keys[6], $arr)) {
            $this->setAddress2($arr[$keys[6]]);
        }
        if (array_key_exists($keys[7], $arr)) {
            $this->setAddress3($arr[$keys[7]]);
        }
        if (array_key_exists($keys[8], $arr)) {
            $this->setZipcode($arr[$keys[8]]);
        }
        if (array_key_exists($keys[9], $arr)) {
            $this->setCity($arr[$keys[9]]);
        }
        if (array_key_exists($keys[10], $arr)) {
            $this->setPhone($arr[$keys[10]]);
        }
        if (array_key_exists($keys[11], $arr)) {
            $this->setCellphone($arr[$keys[11]]);
        }
        if (array_key_exists($keys[12], $arr)) {
            $this->setCountryId($arr[$keys[12]]);
        }
        if (array_key_exists($keys[13], $arr)) {
            $this->setStateId($arr[$keys[13]]);
        }
        if (array_key_exists($keys[14], $arr)) {
            $this->setCreatedAt($arr[$keys[14]]);
        }
        if (array_key_exists($keys[15], $arr)) {
            $this->setUpdatedAt($arr[$keys[15]]);
        }
    }

     /**
     * Populate the current object from a string, using a given parser format
     * <code>
     * $book = new Book();
     * $book->importFrom('JSON', '{"Id":9012,"Title":"Don Juan","ISBN":"0140422161","Price":12.99,"PublisherId":1234,"AuthorId":5678}');
     * </code>
     *
     * You can specify the key type of the array by additionally passing one
     * of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_CAMELNAME,
     * TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM.
     * The default key type is the column's TableMap::TYPE_PHPNAME.
     *
     * @param mixed $parser A AbstractParser instance,
     *                       or a format name ('XML', 'YAML', 'JSON', 'CSV')
     * @param string $data The source data to import from
     * @param string $keyType The type of keys the array uses.
     *
     * @return $this|\CreditNote\Model\CreditNoteAddress The current object, for fluid interface
     */
    public function importFrom($parser, $data, $keyType = TableMap::TYPE_PHPNAME)
    {
        if (!$parser instanceof AbstractParser) {
            $parser = AbstractParser::getParser($parser);
        }

        $this->fromArray($parser->toArray($data), $keyType);

        return $this;
    }

    /**
     * Build a Criteria object containing the values of all modified columns in this object.
     *
     * @return Criteria The Criteria object containing all modified values.
     */
    public function buildCriteria()
    {
        $criteria = new Criteria(CreditNoteAddressTableMap::DATABASE_NAME);

        if ($this->isColumnModified(CreditNoteAddressTableMap::COL_ID)) {
            $criteria->add(CreditNoteAddressTableMap::COL_ID, $this->id);
        }
        if ($this->isColumnModified(CreditNoteAddressTableMap::COL_CUSTOMER_TITLE_ID)) {
            $criteria->add(CreditNoteAddressTableMap::COL_CUSTOMER_TITLE_ID, $this->customer_title_id);
        }
        if ($this->isColumnModified(CreditNoteAddressTableMap::COL_COMPANY)) {
            $criteria->add(CreditNoteAddressTableMap::COL_COMPANY, $this->company);
        }
        if ($this->isColumnModified(CreditNoteAddressTableMap::COL_FIRSTNAME)) {
            $criteria->add(CreditNoteAddressTableMap::COL_FIRSTNAME, $this->firstname);
        }
        if ($this->isColumnModified(CreditNoteAddressTableMap::COL_LASTNAME)) {
            $criteria->add(CreditNoteAddressTableMap::COL_LASTNAME, $this->lastname);
        }
        if ($this->isColumnModified(CreditNoteAddressTableMap::COL_ADDRESS1)) {
            $criteria->add(CreditNoteAddressTableMap::COL_ADDRESS1, $this->address1);
        }
        if ($this->isColumnModified(CreditNoteAddressTableMap::COL_ADDRESS2)) {
            $criteria->add(CreditNoteAddressTableMap::COL_ADDRESS2, $this->address2);
        }
        if ($this->isColumnModified(CreditNoteAddressTableMap::COL_ADDRESS3)) {
            $criteria->add(CreditNoteAddressTableMap::COL_ADDRESS3, $this->address3);
        }
        if ($this->isColumnModified(CreditNoteAddressTableMap::COL_ZIPCODE)) {
            $criteria->add(CreditNoteAddressTableMap::COL_ZIPCODE, $this->zipcode);
        }
        if ($this->isColumnModified(CreditNoteAddressTableMap::COL_CITY)) {
            $criteria->add(CreditNoteAddressTableMap::COL_CITY, $this->city);
        }
        if ($this->isColumnModified(CreditNoteAddressTableMap::COL_PHONE)) {
            $criteria->add(CreditNoteAddressTableMap::COL_PHONE, $this->phone);
        }
        if ($this->isColumnModified(CreditNoteAddressTableMap::COL_CELLPHONE)) {
            $criteria->add(CreditNoteAddressTableMap::COL_CELLPHONE, $this->cellphone);
        }
        if ($this->isColumnModified(CreditNoteAddressTableMap::COL_COUNTRY_ID)) {
            $criteria->add(CreditNoteAddressTableMap::COL_COUNTRY_ID, $this->country_id);
        }
        if ($this->isColumnModified(CreditNoteAddressTableMap::COL_STATE_ID)) {
            $criteria->add(CreditNoteAddressTableMap::COL_STATE_ID, $this->state_id);
        }
        if ($this->isColumnModified(CreditNoteAddressTableMap::COL_CREATED_AT)) {
            $criteria->add(CreditNoteAddressTableMap::COL_CREATED_AT, $this->created_at);
        }
        if ($this->isColumnModified(CreditNoteAddressTableMap::COL_UPDATED_AT)) {
            $criteria->add(CreditNoteAddressTableMap::COL_UPDATED_AT, $this->updated_at);
        }

        return $criteria;
    }

    /**
     * Builds a Criteria object containing the primary key for this object.
     *
     * Unlike buildCriteria() this method includes the primary key values regardless
     * of whether or not they have been modified.
     *
     * @throws LogicException if no primary key is defined
     *
     * @return Criteria The Criteria object containing value(s) for primary key(s).
     */
    public function buildPkeyCriteria()
    {
        $criteria = ChildCreditNoteAddressQuery::create();
        $criteria->add(CreditNoteAddressTableMap::COL_ID, $this->id);

        return $criteria;
    }

    /**
     * If the primary key is not null, return the hashcode of the
     * primary key. Otherwise, return the hash code of the object.
     *
     * @return int Hashcode
     */
    public function hashCode()
    {
        $validPk = null !== $this->getId();

        $validPrimaryKeyFKs = 0;
        $primaryKeyFKs = [];

        if ($validPk) {
            return crc32(json_encode($this->getPrimaryKey(), JSON_UNESCAPED_UNICODE));
        } elseif ($validPrimaryKeyFKs) {
            return crc32(json_encode($primaryKeyFKs, JSON_UNESCAPED_UNICODE));
        }

        return spl_object_hash($this);
    }

    /**
     * Returns the primary key for this object (row).
     * @return int
     */
    public function getPrimaryKey()
    {
        return $this->getId();
    }

    /**
     * Generic method to set the primary key (id column).
     *
     * @param       int $key Primary key.
     * @return void
     */
    public function setPrimaryKey($key)
    {
        $this->setId($key);
    }

    /**
     * Returns true if the primary key for this object is null.
     * @return boolean
     */
    public function isPrimaryKeyNull()
    {
        return null === $this->getId();
    }

    /**
     * Sets contents of passed object to values from current object.
     *
     * If desired, this method can also make copies of all associated (fkey referrers)
     * objects.
     *
     * @param      object $copyObj An object of \CreditNote\Model\CreditNoteAddress (or compatible) type.
     * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @param      boolean $makeNew Whether to reset autoincrement PKs and make the object new.
     * @throws PropelException
     */
    public function copyInto($copyObj, $deepCopy = false, $makeNew = true)
    {
        $copyObj->setCustomerTitleId($this->getCustomerTitleId());
        $copyObj->setCompany($this->getCompany());
        $copyObj->setFirstname($this->getFirstname());
        $copyObj->setLastname($this->getLastname());
        $copyObj->setAddress1($this->getAddress1());
        $copyObj->setAddress2($this->getAddress2());
        $copyObj->setAddress3($this->getAddress3());
        $copyObj->setZipcode($this->getZipcode());
        $copyObj->setCity($this->getCity());
        $copyObj->setPhone($this->getPhone());
        $copyObj->setCellphone($this->getCellphone());
        $copyObj->setCountryId($this->getCountryId());
        $copyObj->setStateId($this->getStateId());
        $copyObj->setCreatedAt($this->getCreatedAt());
        $copyObj->setUpdatedAt($this->getUpdatedAt());

        if ($deepCopy) {
            // important: temporarily setNew(false) because this affects the behavior of
            // the getter/setter methods for fkey referrer objects.
            $copyObj->setNew(false);

            foreach ($this->getCreditNotes() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addCreditNote($relObj->copy($deepCopy));
                }
            }

        } // if ($deepCopy)

        if ($makeNew) {
            $copyObj->setNew(true);
            $copyObj->setId(NULL); // this is a auto-increment column, so set to default value
        }
    }

    /**
     * Makes a copy of this object that will be inserted as a new row in table when saved.
     * It creates a new object filling in the simple attributes, but skipping any primary
     * keys that are defined for the table.
     *
     * If desired, this method can also make copies of all associated (fkey referrers)
     * objects.
     *
     * @param  boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @return \CreditNote\Model\CreditNoteAddress Clone of current object.
     * @throws PropelException
     */
    public function copy($deepCopy = false)
    {
        // we use \get_class(), because this might be a subclass
        $clazz = \get_class($this);
        $copyObj = new $clazz();
        $this->copyInto($copyObj, $deepCopy);

        return $copyObj;
    }

    /**
     * Declares an association between this object and a CustomerTitle object.
     *
     * @param  CustomerTitle $v
     * @return $this|\CreditNote\Model\CreditNoteAddress The current object (for fluent API support)
     * @throws PropelException
     */
    public function setCustomerTitle(CustomerTitle $v = null)
    {
        if ($v === null) {
            $this->setCustomerTitleId(NULL);
        } else {
            $this->setCustomerTitleId($v->getId());
        }

        $this->aCustomerTitle = $v;

        // Add binding for other direction of this n:n relationship.
        // If this object has already been added to the CustomerTitle object, it will not be re-added.
        if ($v !== null) {
            $v->addCreditNoteAddress($this);
        }


        return $this;
    }


    /**
     * Get the associated CustomerTitle object
     *
     * @param  ConnectionInterface $con Optional Connection object.
     * @return CustomerTitle The associated CustomerTitle object.
     * @throws PropelException
     */
    public function getCustomerTitle(ConnectionInterface $con = null)
    {
        if ($this->aCustomerTitle === null && ($this->customer_title_id != 0)) {
            $this->aCustomerTitle = CustomerTitleQuery::create()->findPk($this->customer_title_id, $con);
            /* The following can be used additionally to
                guarantee the related object contains a reference
                to this object.  This level of coupling may, however, be
                undesirable since it could result in an only partially populated collection
                in the referenced object.
                $this->aCustomerTitle->addCreditNoteAddresses($this);
             */
        }

        return $this->aCustomerTitle;
    }

    /**
     * Declares an association between this object and a Country object.
     *
     * @param  Country $v
     * @return $this|\CreditNote\Model\CreditNoteAddress The current object (for fluent API support)
     * @throws PropelException
     */
    public function setCountry(Country $v = null)
    {
        if ($v === null) {
            $this->setCountryId(NULL);
        } else {
            $this->setCountryId($v->getId());
        }

        $this->aCountry = $v;

        // Add binding for other direction of this n:n relationship.
        // If this object has already been added to the Country object, it will not be re-added.
        if ($v !== null) {
            $v->addCreditNoteAddress($this);
        }


        return $this;
    }


    /**
     * Get the associated Country object
     *
     * @param  ConnectionInterface $con Optional Connection object.
     * @return Country The associated Country object.
     * @throws PropelException
     */
    public function getCountry(ConnectionInterface $con = null)
    {
        if ($this->aCountry === null && ($this->country_id != 0)) {
            $this->aCountry = CountryQuery::create()->findPk($this->country_id, $con);
            /* The following can be used additionally to
                guarantee the related object contains a reference
                to this object.  This level of coupling may, however, be
                undesirable since it could result in an only partially populated collection
                in the referenced object.
                $this->aCountry->addCreditNoteAddresses($this);
             */
        }

        return $this->aCountry;
    }

    /**
     * Declares an association between this object and a State object.
     *
     * @param  State $v
     * @return $this|\CreditNote\Model\CreditNoteAddress The current object (for fluent API support)
     * @throws PropelException
     */
    public function setState(State $v = null)
    {
        if ($v === null) {
            $this->setStateId(NULL);
        } else {
            $this->setStateId($v->getId());
        }

        $this->aState = $v;

        // Add binding for other direction of this n:n relationship.
        // If this object has already been added to the State object, it will not be re-added.
        if ($v !== null) {
            $v->addCreditNoteAddress($this);
        }


        return $this;
    }


    /**
     * Get the associated State object
     *
     * @param  ConnectionInterface $con Optional Connection object.
     * @return State The associated State object.
     * @throws PropelException
     */
    public function getState(ConnectionInterface $con = null)
    {
        if ($this->aState === null && ($this->state_id != 0)) {
            $this->aState = StateQuery::create()->findPk($this->state_id, $con);
            /* The following can be used additionally to
                guarantee the related object contains a reference
                to this object.  This level of coupling may, however, be
                undesirable since it could result in an only partially populated collection
                in the referenced object.
                $this->aState->addCreditNoteAddresses($this);
             */
        }

        return $this->aState;
    }


    /**
     * Initializes a collection based on the name of a relation.
     * Avoids crafting an 'init[$relationName]s' method name
     * that wouldn't work when StandardEnglishPluralizer is used.
     *
     * @param      string $relationName The name of the relation to initialize
     * @return void
     */
    public function initRelation($relationName)
    {
        if ('CreditNote' == $relationName) {
            $this->initCreditNotes();
            return;
        }
    }

    /**
     * Clears out the collCreditNotes collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return void
     * @see        addCreditNotes()
     */
    public function clearCreditNotes()
    {
        $this->collCreditNotes = null; // important to set this to NULL since that means it is uninitialized
    }

    /**
     * Reset is the collCreditNotes collection loaded partially.
     */
    public function resetPartialCreditNotes($v = true)
    {
        $this->collCreditNotesPartial = $v;
    }

    /**
     * Initializes the collCreditNotes collection.
     *
     * By default this just sets the collCreditNotes collection to an empty array (like clearcollCreditNotes());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param      boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initCreditNotes($overrideExisting = true)
    {
        if (null !== $this->collCreditNotes && !$overrideExisting) {
            return;
        }

        $collectionClassName = CreditNoteTableMap::getTableMap()->getCollectionClassName();

        $this->collCreditNotes = new $collectionClassName;
        $this->collCreditNotes->setModel('\CreditNote\Model\CreditNote');
    }

    /**
     * Gets an array of ChildCreditNote objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this ChildCreditNoteAddress is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @return ObjectCollection|ChildCreditNote[] List of ChildCreditNote objects
     * @throws PropelException
     */
    public function getCreditNotes($criteria = null, ConnectionInterface $con = null)
    {
        $partial = $this->collCreditNotesPartial && !$this->isNew();
        if (null === $this->collCreditNotes || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collCreditNotes) {
                // return empty collection
                $this->initCreditNotes();
            } else {
                $collCreditNotes = ChildCreditNoteQuery::create(null, $criteria)
                    ->filterByCreditNoteAddress($this)
                    ->find($con);

                if (null !== $criteria) {
                    if (false !== $this->collCreditNotesPartial && count($collCreditNotes)) {
                        $this->initCreditNotes(false);

                        foreach ($collCreditNotes as $obj) {
                            if (false == $this->collCreditNotes->contains($obj)) {
                                $this->collCreditNotes->append($obj);
                            }
                        }

                        $this->collCreditNotesPartial = true;
                    }

                    return $collCreditNotes;
                }

                if ($partial && $this->collCreditNotes) {
                    foreach ($this->collCreditNotes as $obj) {
                        if ($obj->isNew()) {
                            $collCreditNotes[] = $obj;
                        }
                    }
                }

                $this->collCreditNotes = $collCreditNotes;
                $this->collCreditNotesPartial = false;
            }
        }

        return $this->collCreditNotes;
    }

    /**
     * Sets a collection of ChildCreditNote objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param      Collection $creditNotes A Propel collection.
     * @param      ConnectionInterface $con Optional connection object
     * @return $this|ChildCreditNoteAddress The current object (for fluent API support)
     */
    public function setCreditNotes(Collection $creditNotes, ConnectionInterface $con = null)
    {
        /** @var ChildCreditNote[] $creditNotesToDelete */
        $creditNotesToDelete = $this->getCreditNotes(new Criteria(), $con)->diff($creditNotes);


        $this->creditNotesScheduledForDeletion = $creditNotesToDelete;

        foreach ($creditNotesToDelete as $creditNoteRemoved) {
            $creditNoteRemoved->setCreditNoteAddress(null);
        }

        $this->collCreditNotes = null;
        foreach ($creditNotes as $creditNote) {
            $this->addCreditNote($creditNote);
        }

        $this->collCreditNotes = $creditNotes;
        $this->collCreditNotesPartial = false;

        return $this;
    }

    /**
     * Returns the number of related CreditNote objects.
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct
     * @param      ConnectionInterface $con
     * @return int             Count of related CreditNote objects.
     * @throws PropelException
     */
    public function countCreditNotes(Criteria $criteria = null, $distinct = false, ConnectionInterface $con = null)
    {
        $partial = $this->collCreditNotesPartial && !$this->isNew();
        if (null === $this->collCreditNotes || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collCreditNotes) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getCreditNotes());
            }

            $query = ChildCreditNoteQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByCreditNoteAddress($this)
                ->count($con);
        }

        return count($this->collCreditNotes);
    }

    /**
     * Method called to associate a ChildCreditNote object to this object
     * through the ChildCreditNote foreign key attribute.
     *
     * @param  ChildCreditNote $l ChildCreditNote
     * @return $this|\CreditNote\Model\CreditNoteAddress The current object (for fluent API support)
     */
    public function addCreditNote(ChildCreditNote $l)
    {
        if ($this->collCreditNotes === null) {
            $this->initCreditNotes();
            $this->collCreditNotesPartial = true;
        }

        if (!$this->collCreditNotes->contains($l)) {
            $this->doAddCreditNote($l);

            if ($this->creditNotesScheduledForDeletion and $this->creditNotesScheduledForDeletion->contains($l)) {
                $this->creditNotesScheduledForDeletion->remove($this->creditNotesScheduledForDeletion->search($l));
            }
        }

        return $this;
    }

    /**
     * @param ChildCreditNote $creditNote The ChildCreditNote object to add.
     */
    protected function doAddCreditNote($creditNote)
    {
        $this->collCreditNotes[]= $creditNote;
        $creditNote->setCreditNoteAddress($this);
    }

    /**
     * @param  ChildCreditNote $creditNote The ChildCreditNote object to remove.
     * @return $this|ChildCreditNoteAddress The current object (for fluent API support)
     */
    public function removeCreditNote($creditNote)
    {
        if ($this->getCreditNotes()->contains($creditNote)) {
            $pos = $this->collCreditNotes->search($creditNote);
            $this->collCreditNotes->remove($pos);
            if (null === $this->creditNotesScheduledForDeletion) {
                $this->creditNotesScheduledForDeletion = clone $this->collCreditNotes;
                $this->creditNotesScheduledForDeletion->clear();
            }
            $this->creditNotesScheduledForDeletion[]= clone $creditNote;
            $creditNote->setCreditNoteAddress(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this CreditNoteAddress is new, it will return
     * an empty collection; or if this CreditNoteAddress has previously
     * been saved, it will retrieve related CreditNotes from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in CreditNoteAddress.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @param      string $joinBehavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return ObjectCollection|ChildCreditNote[] List of ChildCreditNote objects
     */
    public function getCreditNotesJoinOrder(Criteria $criteria = null, $con = null, $joinBehavior = Criteria::LEFT_JOIN)
    {
        $query = ChildCreditNoteQuery::create(null, $criteria);
        $query->joinWith('Order', $joinBehavior);

        return $this->getCreditNotes($query, $con);
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this CreditNoteAddress is new, it will return
     * an empty collection; or if this CreditNoteAddress has previously
     * been saved, it will retrieve related CreditNotes from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in CreditNoteAddress.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @param      string $joinBehavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return ObjectCollection|ChildCreditNote[] List of ChildCreditNote objects
     */
    public function getCreditNotesJoinCustomer(Criteria $criteria = null, $con = null, $joinBehavior = Criteria::LEFT_JOIN)
    {
        $query = ChildCreditNoteQuery::create(null, $criteria);
        $query->joinWith('Customer', $joinBehavior);

        return $this->getCreditNotes($query, $con);
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this CreditNoteAddress is new, it will return
     * an empty collection; or if this CreditNoteAddress has previously
     * been saved, it will retrieve related CreditNotes from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in CreditNoteAddress.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @param      string $joinBehavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return ObjectCollection|ChildCreditNote[] List of ChildCreditNote objects
     */
    public function getCreditNotesJoinCreditNoteRelatedByParentId(Criteria $criteria = null, $con = null, $joinBehavior = Criteria::LEFT_JOIN)
    {
        $query = ChildCreditNoteQuery::create(null, $criteria);
        $query->joinWith('CreditNoteRelatedByParentId', $joinBehavior);

        return $this->getCreditNotes($query, $con);
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this CreditNoteAddress is new, it will return
     * an empty collection; or if this CreditNoteAddress has previously
     * been saved, it will retrieve related CreditNotes from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in CreditNoteAddress.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @param      string $joinBehavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return ObjectCollection|ChildCreditNote[] List of ChildCreditNote objects
     */
    public function getCreditNotesJoinCreditNoteType(Criteria $criteria = null, $con = null, $joinBehavior = Criteria::LEFT_JOIN)
    {
        $query = ChildCreditNoteQuery::create(null, $criteria);
        $query->joinWith('CreditNoteType', $joinBehavior);

        return $this->getCreditNotes($query, $con);
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this CreditNoteAddress is new, it will return
     * an empty collection; or if this CreditNoteAddress has previously
     * been saved, it will retrieve related CreditNotes from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in CreditNoteAddress.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @param      string $joinBehavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return ObjectCollection|ChildCreditNote[] List of ChildCreditNote objects
     */
    public function getCreditNotesJoinCreditNoteStatus(Criteria $criteria = null, $con = null, $joinBehavior = Criteria::LEFT_JOIN)
    {
        $query = ChildCreditNoteQuery::create(null, $criteria);
        $query->joinWith('CreditNoteStatus', $joinBehavior);

        return $this->getCreditNotes($query, $con);
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this CreditNoteAddress is new, it will return
     * an empty collection; or if this CreditNoteAddress has previously
     * been saved, it will retrieve related CreditNotes from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in CreditNoteAddress.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @param      string $joinBehavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return ObjectCollection|ChildCreditNote[] List of ChildCreditNote objects
     */
    public function getCreditNotesJoinCurrency(Criteria $criteria = null, $con = null, $joinBehavior = Criteria::LEFT_JOIN)
    {
        $query = ChildCreditNoteQuery::create(null, $criteria);
        $query->joinWith('Currency', $joinBehavior);

        return $this->getCreditNotes($query, $con);
    }

    /**
     * Clears the current object, sets all attributes to their default values and removes
     * outgoing references as well as back-references (from other objects to this one. Results probably in a database
     * change of those foreign objects when you call `save` there).
     */
    public function clear()
    {
        if (null !== $this->aCustomerTitle) {
            $this->aCustomerTitle->removeCreditNoteAddress($this);
        }
        if (null !== $this->aCountry) {
            $this->aCountry->removeCreditNoteAddress($this);
        }
        if (null !== $this->aState) {
            $this->aState->removeCreditNoteAddress($this);
        }
        $this->id = null;
        $this->customer_title_id = null;
        $this->company = null;
        $this->firstname = null;
        $this->lastname = null;
        $this->address1 = null;
        $this->address2 = null;
        $this->address3 = null;
        $this->zipcode = null;
        $this->city = null;
        $this->phone = null;
        $this->cellphone = null;
        $this->country_id = null;
        $this->state_id = null;
        $this->created_at = null;
        $this->updated_at = null;
        $this->alreadyInSave = false;
        $this->clearAllReferences();
        $this->resetModified();
        $this->setNew(true);
        $this->setDeleted(false);
    }

    /**
     * Resets all references and back-references to other model objects or collections of model objects.
     *
     * This method is used to reset all php object references (not the actual reference in the database).
     * Necessary for object serialisation.
     *
     * @param      boolean $deep Whether to also clear the references on all referrer objects.
     */
    public function clearAllReferences($deep = false)
    {
        if ($deep) {
            if ($this->collCreditNotes) {
                foreach ($this->collCreditNotes as $o) {
                    $o->clearAllReferences($deep);
                }
            }
        } // if ($deep)

        $this->collCreditNotes = null;
        $this->aCustomerTitle = null;
        $this->aCountry = null;
        $this->aState = null;
    }

    /**
     * Return the string representation of this object
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->exportTo(CreditNoteAddressTableMap::DEFAULT_STRING_FORMAT);
    }

    // timestampable behavior

    /**
     * Mark the current object so that the update date doesn't get updated during next save
     *
     * @return     $this|ChildCreditNoteAddress The current object (for fluent API support)
     */
    public function keepUpdateDateUnchanged()
    {
        $this->modifiedColumns[CreditNoteAddressTableMap::COL_UPDATED_AT] = true;

        return $this;
    }

    /**
     * Code to be run before persisting the object
     * @param  ConnectionInterface $con
     * @return boolean
     */
    public function preSave(ConnectionInterface $con = null)
    {
        if (is_callable('parent::preSave')) {
            return parent::preSave($con);
        }

        if (null !== $con
            && method_exists($con, 'getEventDispatcher')
            && null !== $con->getEventDispatcher()
        ) {
            $event = new CreditNoteAddressEvent($this);

            $con->getEventDispatcher()
                ->dispatch(
                    CreditNoteAddressEvent::PRE_SAVE,
                    $event
                );

            return !$event->isPropagationStopped();
        }

        return true;
    }

    /**
     * Code to be run after persisting the object
     * @param ConnectionInterface $con
     */
    public function postSave(ConnectionInterface $con = null)
    {
        if (is_callable('parent::postSave')) {
            parent::postSave($con);
        }

        if (null !== $con
            && method_exists($con, 'getEventDispatcher')
            && null !== $con->getEventDispatcher()
        ) {
            $con->getEventDispatcher()
                ->dispatch(
                    CreditNoteAddressEvent::POST_SAVE,
                    new CreditNoteAddressEvent($this)
                );
        }
    }

    /**
     * Code to be run before inserting to database
     * @param  ConnectionInterface $con
     * @return boolean
     */
    public function preInsert(ConnectionInterface $con = null)
    {
        if (is_callable('parent::preInsert')) {
            return parent::preInsert($con);
        }

        if (null !== $con
            && method_exists($con, 'getEventDispatcher')
            && null !== $con->getEventDispatcher()
        ) {
            $event = new CreditNoteAddressEvent($this);
            $con->getEventDispatcher()
                ->dispatch(
                    CreditNoteAddressEvent::PRE_INSERT,
                    $event
                );

            return !$event->isPropagationStopped();
        }

        return true;
    }

    /**
     * Code to be run after inserting to database
     * @param ConnectionInterface $con
     */
    public function postInsert(ConnectionInterface $con = null)
    {
        if (is_callable('parent::postInsert')) {
            parent::postInsert($con);
        }

        if (null !== $con
            && method_exists($con, 'getEventDispatcher')
            && null !== $con->getEventDispatcher()
        ) {
            $con->getEventDispatcher()
                ->dispatch(
                    CreditNoteAddressEvent::POST_INSERT,
                    new CreditNoteAddressEvent($this)
                );
        }
    }

    /**
     * Code to be run before updating the object in database
     * @param  ConnectionInterface $con
     * @return boolean
     */
    public function preUpdate(ConnectionInterface $con = null)
    {
        if (is_callable('parent::preUpdate')) {
            return parent::preUpdate($con);
        }

        if (null !== $con
            && method_exists($con, 'getEventDispatcher')
            && null !== $con->getEventDispatcher()
        ) {
            $event = new CreditNoteAddressEvent($this);

            $con->getEventDispatcher()
                ->dispatch(
                    CreditNoteAddressEvent::PRE_UPDATE,
                    $event
                );

            return !$event->isPropagationStopped();
        }

        return true;
    }

    /**
     * Code to be run after updating the object in database
     * @param ConnectionInterface $con
     */
    public function postUpdate(ConnectionInterface $con = null)
    {
        if (is_callable('parent::postUpdate')) {
            parent::postUpdate($con);
        }

        if (null !== $con
            && method_exists($con, 'getEventDispatcher')
            && null !== $con->getEventDispatcher()
        ) {
            $con->getEventDispatcher()
                ->dispatch(
                    CreditNoteAddressEvent::POST_UPDATE,
                    new CreditNoteAddressEvent($this)
                );
        }
    }

    /**
     * Code to be run before deleting the object in database
     * @param  ConnectionInterface $con
     * @return boolean
     */
    public function preDelete(ConnectionInterface $con = null)
    {
        if (is_callable('parent::preDelete')) {
            return parent::preDelete($con);
        }

        if (null !== $con
            && method_exists($con, 'getEventDispatcher')
            && null !== $con->getEventDispatcher()
        ) {
            $event = new CreditNoteAddressEvent($this);

            $con->getEventDispatcher()
                ->dispatch(
                    CreditNoteAddressEvent::PRE_DELETE,
                    $event
                );

            return !$event->isPropagationStopped();
        }

        return true;
    }

    /**
     * Code to be run after deleting the object in database
     * @param ConnectionInterface $con
     */
    public function postDelete(ConnectionInterface $con = null)
    {
        if (is_callable('parent::postDelete')) {
            parent::postDelete($con);
        }

        if (null !== $con
            && method_exists($con, 'getEventDispatcher')
            && null !== $con->getEventDispatcher()
        ) {
            $con->getEventDispatcher()
                ->dispatch(
                    CreditNoteAddressEvent::POST_DELETE,
                    new CreditNoteAddressEvent($this)
                );
        }
    }


    /**
     * Derived method to catches calls to undefined methods.
     *
     * Provides magic import/export method support (fromXML()/toXML(), fromYAML()/toYAML(), etc.).
     * Allows to define default __call() behavior if you overwrite __call()
     *
     * @param string $name
     * @param mixed  $params
     *
     * @return array|string
     */
    public function __call($name, $params)
    {
        if (0 === strpos($name, 'get')) {
            $virtualColumn = substr($name, 3);
            if ($this->hasVirtualColumn($virtualColumn)) {
                return $this->getVirtualColumn($virtualColumn);
            }

            $virtualColumn = lcfirst($virtualColumn);
            if ($this->hasVirtualColumn($virtualColumn)) {
                return $this->getVirtualColumn($virtualColumn);
            }
        }

        if (0 === strpos($name, 'from')) {
            $format = substr($name, 4);

            return $this->importFrom($format, reset($params));
        }

        if (0 === strpos($name, 'to')) {
            $format = substr($name, 2);
            $includeLazyLoadColumns = isset($params[0]) ? $params[0] : true;

            return $this->exportTo($format, $includeLazyLoadColumns);
        }

        throw new BadMethodCallException(sprintf('Call to undefined method: %s.', $name));
    }

}
