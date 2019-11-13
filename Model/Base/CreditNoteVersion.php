<?php

namespace CreditNote\Model\Base;

use \DateTime;
use \Exception;
use \PDO;
use CreditNote\Model\CreditNote as ChildCreditNote;
use CreditNote\Model\CreditNoteQuery as ChildCreditNoteQuery;
use CreditNote\Model\CreditNoteVersionQuery as ChildCreditNoteVersionQuery;
use CreditNote\Model\Map\CreditNoteVersionTableMap;
use Propel\Runtime\Propel;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\ActiveRecord\ActiveRecordInterface;
use Propel\Runtime\Collection\Collection;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\Exception\BadMethodCallException;
use Propel\Runtime\Exception\PropelException;
use Propel\Runtime\Map\TableMap;
use Propel\Runtime\Parser\AbstractParser;
use Propel\Runtime\Util\PropelDateTime;

abstract class CreditNoteVersion implements ActiveRecordInterface
{
    /**
     * TableMap class name
     */
    const TABLE_MAP = '\\CreditNote\\Model\\Map\\CreditNoteVersionTableMap';


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
     * @var        int
     */
    protected $id;

    /**
     * The value for the ref field.
     * @var        string
     */
    protected $ref;

    /**
     * The value for the invoice_ref field.
     * @var        string
     */
    protected $invoice_ref;

    /**
     * The value for the invoice_address_id field.
     * @var        int
     */
    protected $invoice_address_id;

    /**
     * The value for the invoice_date field.
     * @var        string
     */
    protected $invoice_date;

    /**
     * The value for the order_id field.
     * @var        int
     */
    protected $order_id;

    /**
     * The value for the customer_id field.
     * @var        int
     */
    protected $customer_id;

    /**
     * The value for the parent_id field.
     * @var        int
     */
    protected $parent_id;

    /**
     * The value for the type_id field.
     * @var        int
     */
    protected $type_id;

    /**
     * The value for the status_id field.
     * @var        int
     */
    protected $status_id;

    /**
     * The value for the currency_id field.
     * @var        int
     */
    protected $currency_id;

    /**
     * The value for the currency_rate field.
     * @var        double
     */
    protected $currency_rate;

    /**
     * The value for the total_price field.
     * Note: this column has a database default value of: '0.000000'
     * @var        string
     */
    protected $total_price;

    /**
     * The value for the total_price_with_tax field.
     * Note: this column has a database default value of: '0.000000'
     * @var        string
     */
    protected $total_price_with_tax;

    /**
     * The value for the discount_without_tax field.
     * Note: this column has a database default value of: '0.000000'
     * @var        string
     */
    protected $discount_without_tax;

    /**
     * The value for the discount_with_tax field.
     * Note: this column has a database default value of: '0.000000'
     * @var        string
     */
    protected $discount_with_tax;

    /**
     * The value for the allow_partial_use field.
     * Note: this column has a database default value of: true
     * @var        boolean
     */
    protected $allow_partial_use;

    /**
     * The value for the created_at field.
     * @var        string
     */
    protected $created_at;

    /**
     * The value for the updated_at field.
     * @var        string
     */
    protected $updated_at;

    /**
     * The value for the version field.
     * Note: this column has a database default value of: 0
     * @var        int
     */
    protected $version;

    /**
     * The value for the version_created_at field.
     * @var        string
     */
    protected $version_created_at;

    /**
     * The value for the version_created_by field.
     * @var        string
     */
    protected $version_created_by;

    /**
     * The value for the order_id_version field.
     * Note: this column has a database default value of: 0
     * @var        int
     */
    protected $order_id_version;

    /**
     * The value for the customer_id_version field.
     * Note: this column has a database default value of: 0
     * @var        int
     */
    protected $customer_id_version;

    /**
     * The value for the parent_id_version field.
     * Note: this column has a database default value of: 0
     * @var        int
     */
    protected $parent_id_version;

    /**
     * The value for the credit_note_ids field.
     * @var        array
     */
    protected $credit_note_ids;

    /**
     * The unserialized $credit_note_ids value - i.e. the persisted object.
     * This is necessary to avoid repeated calls to unserialize() at runtime.
     * @var object
     */
    protected $credit_note_ids_unserialized;

    /**
     * The value for the credit_note_versions field.
     * @var        array
     */
    protected $credit_note_versions;

    /**
     * The unserialized $credit_note_versions value - i.e. the persisted object.
     * This is necessary to avoid repeated calls to unserialize() at runtime.
     * @var object
     */
    protected $credit_note_versions_unserialized;

    /**
     * @var        CreditNote
     */
    protected $aCreditNote;

    /**
     * Flag to prevent endless save loop, if this object is referenced
     * by another object which falls in this transaction.
     *
     * @var boolean
     */
    protected $alreadyInSave = false;

    /**
     * Applies default values to this object.
     * This method should be called from the object's constructor (or
     * equivalent initialization method).
     * @see __construct()
     */
    public function applyDefaultValues()
    {
        $this->total_price = '0.000000';
        $this->total_price_with_tax = '0.000000';
        $this->discount_without_tax = '0.000000';
        $this->discount_with_tax = '0.000000';
        $this->allow_partial_use = true;
        $this->version = 0;
        $this->order_id_version = 0;
        $this->customer_id_version = 0;
        $this->parent_id_version = 0;
    }

    /**
     * Initializes internal state of CreditNote\Model\Base\CreditNoteVersion object.
     * @see applyDefaults()
     */
    public function __construct()
    {
        $this->applyDefaultValues();
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
        $this->new = (Boolean) $b;
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
        $this->deleted = (Boolean) $b;
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
     * Compares this with another <code>CreditNoteVersion</code> instance.  If
     * <code>obj</code> is an instance of <code>CreditNoteVersion</code>, delegates to
     * <code>equals(CreditNoteVersion)</code>.  Otherwise, returns <code>false</code>.
     *
     * @param  mixed   $obj The object to compare to.
     * @return boolean Whether equal to the object specified.
     */
    public function equals($obj)
    {
        $thisclazz = get_class($this);
        if (!is_object($obj) || !($obj instanceof $thisclazz)) {
            return false;
        }

        if ($this === $obj) {
            return true;
        }

        if (null === $this->getPrimaryKey()
            || null === $obj->getPrimaryKey())  {
            return false;
        }

        return $this->getPrimaryKey() === $obj->getPrimaryKey();
    }

    /**
     * If the primary key is not null, return the hashcode of the
     * primary key. Otherwise, return the hash code of the object.
     *
     * @return int Hashcode
     */
    public function hashCode()
    {
        if (null !== $this->getPrimaryKey()) {
            return crc32(serialize($this->getPrimaryKey()));
        }

        return crc32(serialize(clone $this));
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
     * @return CreditNoteVersion The current object, for fluid interface
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
        return Propel::log(get_class($this) . ': ' . $msg, $priority);
    }

    /**
     * Populate the current object from a string, using a given parser format
     * <code>
     * $book = new Book();
     * $book->importFrom('JSON', '{"Id":9012,"Title":"Don Juan","ISBN":"0140422161","Price":12.99,"PublisherId":1234,"AuthorId":5678}');
     * </code>
     *
     * @param mixed $parser A AbstractParser instance,
     *                       or a format name ('XML', 'YAML', 'JSON', 'CSV')
     * @param string $data The source data to import from
     *
     * @return CreditNoteVersion The current object, for fluid interface
     */
    public function importFrom($parser, $data)
    {
        if (!$parser instanceof AbstractParser) {
            $parser = AbstractParser::getParser($parser);
        }

        $this->fromArray($parser->toArray($data), TableMap::TYPE_PHPNAME);

        return $this;
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

        return array_keys(get_object_vars($this));
    }

    /**
     * Get the [id] column value.
     *
     * @return   int
     */
    public function getId()
    {

        return $this->id;
    }

    /**
     * Get the [ref] column value.
     *
     * @return   string
     */
    public function getRef()
    {

        return $this->ref;
    }

    /**
     * Get the [invoice_ref] column value.
     *
     * @return   string
     */
    public function getInvoiceRef()
    {

        return $this->invoice_ref;
    }

    /**
     * Get the [invoice_address_id] column value.
     *
     * @return   int
     */
    public function getInvoiceAddressId()
    {

        return $this->invoice_address_id;
    }

    /**
     * Get the [optionally formatted] temporal [invoice_date] column value.
     *
     *
     * @param      string $format The date/time format string (either date()-style or strftime()-style).
     *                            If format is NULL, then the raw \DateTime object will be returned.
     *
     * @return mixed Formatted date/time value as string or \DateTime object (if format is NULL), NULL if column is NULL, and 0 if column value is 0000-00-00 00:00:00
     *
     * @throws PropelException - if unable to parse/validate the date/time value.
     */
    public function getInvoiceDate($format = NULL)
    {
        if ($format === null) {
            return $this->invoice_date;
        } else {
            return $this->invoice_date instanceof \DateTime ? $this->invoice_date->format($format) : null;
        }
    }

    /**
     * Get the [order_id] column value.
     *
     * @return   int
     */
    public function getOrderId()
    {

        return $this->order_id;
    }

    /**
     * Get the [customer_id] column value.
     *
     * @return   int
     */
    public function getCustomerId()
    {

        return $this->customer_id;
    }

    /**
     * Get the [parent_id] column value.
     *
     * @return   int
     */
    public function getParentId()
    {

        return $this->parent_id;
    }

    /**
     * Get the [type_id] column value.
     *
     * @return   int
     */
    public function getTypeId()
    {

        return $this->type_id;
    }

    /**
     * Get the [status_id] column value.
     *
     * @return   int
     */
    public function getStatusId()
    {

        return $this->status_id;
    }

    /**
     * Get the [currency_id] column value.
     *
     * @return   int
     */
    public function getCurrencyId()
    {

        return $this->currency_id;
    }

    /**
     * Get the [currency_rate] column value.
     *
     * @return   double
     */
    public function getCurrencyRate()
    {

        return $this->currency_rate;
    }

    /**
     * Get the [total_price] column value.
     *
     * @return   string
     */
    public function getTotalPrice()
    {

        return $this->total_price;
    }

    /**
     * Get the [total_price_with_tax] column value.
     *
     * @return   string
     */
    public function getTotalPriceWithTax()
    {

        return $this->total_price_with_tax;
    }

    /**
     * Get the [discount_without_tax] column value.
     *
     * @return   string
     */
    public function getDiscountWithoutTax()
    {

        return $this->discount_without_tax;
    }

    /**
     * Get the [discount_with_tax] column value.
     *
     * @return   string
     */
    public function getDiscountWithTax()
    {

        return $this->discount_with_tax;
    }

    /**
     * Get the [allow_partial_use] column value.
     *
     * @return   boolean
     */
    public function getAllowPartialUse()
    {

        return $this->allow_partial_use;
    }

    /**
     * Get the [optionally formatted] temporal [created_at] column value.
     *
     *
     * @param      string $format The date/time format string (either date()-style or strftime()-style).
     *                            If format is NULL, then the raw \DateTime object will be returned.
     *
     * @return mixed Formatted date/time value as string or \DateTime object (if format is NULL), NULL if column is NULL, and 0 if column value is 0000-00-00 00:00:00
     *
     * @throws PropelException - if unable to parse/validate the date/time value.
     */
    public function getCreatedAt($format = NULL)
    {
        if ($format === null) {
            return $this->created_at;
        } else {
            return $this->created_at instanceof \DateTime ? $this->created_at->format($format) : null;
        }
    }

    /**
     * Get the [optionally formatted] temporal [updated_at] column value.
     *
     *
     * @param      string $format The date/time format string (either date()-style or strftime()-style).
     *                            If format is NULL, then the raw \DateTime object will be returned.
     *
     * @return mixed Formatted date/time value as string or \DateTime object (if format is NULL), NULL if column is NULL, and 0 if column value is 0000-00-00 00:00:00
     *
     * @throws PropelException - if unable to parse/validate the date/time value.
     */
    public function getUpdatedAt($format = NULL)
    {
        if ($format === null) {
            return $this->updated_at;
        } else {
            return $this->updated_at instanceof \DateTime ? $this->updated_at->format($format) : null;
        }
    }

    /**
     * Get the [version] column value.
     *
     * @return   int
     */
    public function getVersion()
    {

        return $this->version;
    }

    /**
     * Get the [optionally formatted] temporal [version_created_at] column value.
     *
     *
     * @param      string $format The date/time format string (either date()-style or strftime()-style).
     *                            If format is NULL, then the raw \DateTime object will be returned.
     *
     * @return mixed Formatted date/time value as string or \DateTime object (if format is NULL), NULL if column is NULL, and 0 if column value is 0000-00-00 00:00:00
     *
     * @throws PropelException - if unable to parse/validate the date/time value.
     */
    public function getVersionCreatedAt($format = NULL)
    {
        if ($format === null) {
            return $this->version_created_at;
        } else {
            return $this->version_created_at instanceof \DateTime ? $this->version_created_at->format($format) : null;
        }
    }

    /**
     * Get the [version_created_by] column value.
     *
     * @return   string
     */
    public function getVersionCreatedBy()
    {

        return $this->version_created_by;
    }

    /**
     * Get the [order_id_version] column value.
     *
     * @return   int
     */
    public function getOrderIdVersion()
    {

        return $this->order_id_version;
    }

    /**
     * Get the [customer_id_version] column value.
     *
     * @return   int
     */
    public function getCustomerIdVersion()
    {

        return $this->customer_id_version;
    }

    /**
     * Get the [parent_id_version] column value.
     *
     * @return   int
     */
    public function getParentIdVersion()
    {

        return $this->parent_id_version;
    }

    /**
     * Get the [credit_note_ids] column value.
     *
     * @return   array
     */
    public function getCreditNoteIds()
    {
        if (null === $this->credit_note_ids_unserialized) {
            $this->credit_note_ids_unserialized = array();
        }
        if (!$this->credit_note_ids_unserialized && null !== $this->credit_note_ids) {
            $credit_note_ids_unserialized = substr($this->credit_note_ids, 2, -2);
            $this->credit_note_ids_unserialized = $credit_note_ids_unserialized ? explode(' | ', $credit_note_ids_unserialized) : array();
        }

        return $this->credit_note_ids_unserialized;
    }

    /**
     * Test the presence of a value in the [credit_note_ids] array column value.
     * @param      mixed $value
     *
     * @return boolean
     */
    public function hasCreditNoteId($value)
    {
        return in_array($value, $this->getCreditNoteIds());
    } // hasCreditNoteId()

    /**
     * Get the [credit_note_versions] column value.
     *
     * @return   array
     */
    public function getCreditNoteVersions()
    {
        if (null === $this->credit_note_versions_unserialized) {
            $this->credit_note_versions_unserialized = array();
        }
        if (!$this->credit_note_versions_unserialized && null !== $this->credit_note_versions) {
            $credit_note_versions_unserialized = substr($this->credit_note_versions, 2, -2);
            $this->credit_note_versions_unserialized = $credit_note_versions_unserialized ? explode(' | ', $credit_note_versions_unserialized) : array();
        }

        return $this->credit_note_versions_unserialized;
    }

    /**
     * Test the presence of a value in the [credit_note_versions] array column value.
     * @param      mixed $value
     *
     * @return boolean
     */
    public function hasCreditNoteVersion($value)
    {
        return in_array($value, $this->getCreditNoteVersions());
    } // hasCreditNoteVersion()

    /**
     * Set the value of [id] column.
     *
     * @param      int $v new value
     * @return   \CreditNote\Model\CreditNoteVersion The current object (for fluent API support)
     */
    public function setId($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->id !== $v) {
            $this->id = $v;
            $this->modifiedColumns[CreditNoteVersionTableMap::ID] = true;
        }

        if ($this->aCreditNote !== null && $this->aCreditNote->getId() !== $v) {
            $this->aCreditNote = null;
        }


        return $this;
    } // setId()

    /**
     * Set the value of [ref] column.
     *
     * @param      string $v new value
     * @return   \CreditNote\Model\CreditNoteVersion The current object (for fluent API support)
     */
    public function setRef($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->ref !== $v) {
            $this->ref = $v;
            $this->modifiedColumns[CreditNoteVersionTableMap::REF] = true;
        }


        return $this;
    } // setRef()

    /**
     * Set the value of [invoice_ref] column.
     *
     * @param      string $v new value
     * @return   \CreditNote\Model\CreditNoteVersion The current object (for fluent API support)
     */
    public function setInvoiceRef($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->invoice_ref !== $v) {
            $this->invoice_ref = $v;
            $this->modifiedColumns[CreditNoteVersionTableMap::INVOICE_REF] = true;
        }


        return $this;
    } // setInvoiceRef()

    /**
     * Set the value of [invoice_address_id] column.
     *
     * @param      int $v new value
     * @return   \CreditNote\Model\CreditNoteVersion The current object (for fluent API support)
     */
    public function setInvoiceAddressId($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->invoice_address_id !== $v) {
            $this->invoice_address_id = $v;
            $this->modifiedColumns[CreditNoteVersionTableMap::INVOICE_ADDRESS_ID] = true;
        }


        return $this;
    } // setInvoiceAddressId()

    /**
     * Sets the value of [invoice_date] column to a normalized version of the date/time value specified.
     *
     * @param      mixed $v string, integer (timestamp), or \DateTime value.
     *               Empty strings are treated as NULL.
     * @return   \CreditNote\Model\CreditNoteVersion The current object (for fluent API support)
     */
    public function setInvoiceDate($v)
    {
        $dt = PropelDateTime::newInstance($v, null, '\DateTime');
        if ($this->invoice_date !== null || $dt !== null) {
            if ($dt !== $this->invoice_date) {
                $this->invoice_date = $dt;
                $this->modifiedColumns[CreditNoteVersionTableMap::INVOICE_DATE] = true;
            }
        } // if either are not null


        return $this;
    } // setInvoiceDate()

    /**
     * Set the value of [order_id] column.
     *
     * @param      int $v new value
     * @return   \CreditNote\Model\CreditNoteVersion The current object (for fluent API support)
     */
    public function setOrderId($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->order_id !== $v) {
            $this->order_id = $v;
            $this->modifiedColumns[CreditNoteVersionTableMap::ORDER_ID] = true;
        }


        return $this;
    } // setOrderId()

    /**
     * Set the value of [customer_id] column.
     *
     * @param      int $v new value
     * @return   \CreditNote\Model\CreditNoteVersion The current object (for fluent API support)
     */
    public function setCustomerId($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->customer_id !== $v) {
            $this->customer_id = $v;
            $this->modifiedColumns[CreditNoteVersionTableMap::CUSTOMER_ID] = true;
        }


        return $this;
    } // setCustomerId()

    /**
     * Set the value of [parent_id] column.
     *
     * @param      int $v new value
     * @return   \CreditNote\Model\CreditNoteVersion The current object (for fluent API support)
     */
    public function setParentId($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->parent_id !== $v) {
            $this->parent_id = $v;
            $this->modifiedColumns[CreditNoteVersionTableMap::PARENT_ID] = true;
        }


        return $this;
    } // setParentId()

    /**
     * Set the value of [type_id] column.
     *
     * @param      int $v new value
     * @return   \CreditNote\Model\CreditNoteVersion The current object (for fluent API support)
     */
    public function setTypeId($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->type_id !== $v) {
            $this->type_id = $v;
            $this->modifiedColumns[CreditNoteVersionTableMap::TYPE_ID] = true;
        }


        return $this;
    } // setTypeId()

    /**
     * Set the value of [status_id] column.
     *
     * @param      int $v new value
     * @return   \CreditNote\Model\CreditNoteVersion The current object (for fluent API support)
     */
    public function setStatusId($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->status_id !== $v) {
            $this->status_id = $v;
            $this->modifiedColumns[CreditNoteVersionTableMap::STATUS_ID] = true;
        }


        return $this;
    } // setStatusId()

    /**
     * Set the value of [currency_id] column.
     *
     * @param      int $v new value
     * @return   \CreditNote\Model\CreditNoteVersion The current object (for fluent API support)
     */
    public function setCurrencyId($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->currency_id !== $v) {
            $this->currency_id = $v;
            $this->modifiedColumns[CreditNoteVersionTableMap::CURRENCY_ID] = true;
        }


        return $this;
    } // setCurrencyId()

    /**
     * Set the value of [currency_rate] column.
     *
     * @param      double $v new value
     * @return   \CreditNote\Model\CreditNoteVersion The current object (for fluent API support)
     */
    public function setCurrencyRate($v)
    {
        if ($v !== null) {
            $v = (double) $v;
        }

        if ($this->currency_rate !== $v) {
            $this->currency_rate = $v;
            $this->modifiedColumns[CreditNoteVersionTableMap::CURRENCY_RATE] = true;
        }


        return $this;
    } // setCurrencyRate()

    /**
     * Set the value of [total_price] column.
     *
     * @param      string $v new value
     * @return   \CreditNote\Model\CreditNoteVersion The current object (for fluent API support)
     */
    public function setTotalPrice($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->total_price !== $v) {
            $this->total_price = $v;
            $this->modifiedColumns[CreditNoteVersionTableMap::TOTAL_PRICE] = true;
        }


        return $this;
    } // setTotalPrice()

    /**
     * Set the value of [total_price_with_tax] column.
     *
     * @param      string $v new value
     * @return   \CreditNote\Model\CreditNoteVersion The current object (for fluent API support)
     */
    public function setTotalPriceWithTax($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->total_price_with_tax !== $v) {
            $this->total_price_with_tax = $v;
            $this->modifiedColumns[CreditNoteVersionTableMap::TOTAL_PRICE_WITH_TAX] = true;
        }


        return $this;
    } // setTotalPriceWithTax()

    /**
     * Set the value of [discount_without_tax] column.
     *
     * @param      string $v new value
     * @return   \CreditNote\Model\CreditNoteVersion The current object (for fluent API support)
     */
    public function setDiscountWithoutTax($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->discount_without_tax !== $v) {
            $this->discount_without_tax = $v;
            $this->modifiedColumns[CreditNoteVersionTableMap::DISCOUNT_WITHOUT_TAX] = true;
        }


        return $this;
    } // setDiscountWithoutTax()

    /**
     * Set the value of [discount_with_tax] column.
     *
     * @param      string $v new value
     * @return   \CreditNote\Model\CreditNoteVersion The current object (for fluent API support)
     */
    public function setDiscountWithTax($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->discount_with_tax !== $v) {
            $this->discount_with_tax = $v;
            $this->modifiedColumns[CreditNoteVersionTableMap::DISCOUNT_WITH_TAX] = true;
        }


        return $this;
    } // setDiscountWithTax()

    /**
     * Sets the value of the [allow_partial_use] column.
     * Non-boolean arguments are converted using the following rules:
     *   * 1, '1', 'true',  'on',  and 'yes' are converted to boolean true
     *   * 0, '0', 'false', 'off', and 'no'  are converted to boolean false
     * Check on string values is case insensitive (so 'FaLsE' is seen as 'false').
     *
     * @param      boolean|integer|string $v The new value
     * @return   \CreditNote\Model\CreditNoteVersion The current object (for fluent API support)
     */
    public function setAllowPartialUse($v)
    {
        if ($v !== null) {
            if (is_string($v)) {
                $v = in_array(strtolower($v), array('false', 'off', '-', 'no', 'n', '0', '')) ? false : true;
            } else {
                $v = (boolean) $v;
            }
        }

        if ($this->allow_partial_use !== $v) {
            $this->allow_partial_use = $v;
            $this->modifiedColumns[CreditNoteVersionTableMap::ALLOW_PARTIAL_USE] = true;
        }


        return $this;
    } // setAllowPartialUse()

    /**
     * Sets the value of [created_at] column to a normalized version of the date/time value specified.
     *
     * @param      mixed $v string, integer (timestamp), or \DateTime value.
     *               Empty strings are treated as NULL.
     * @return   \CreditNote\Model\CreditNoteVersion The current object (for fluent API support)
     */
    public function setCreatedAt($v)
    {
        $dt = PropelDateTime::newInstance($v, null, '\DateTime');
        if ($this->created_at !== null || $dt !== null) {
            if ($dt !== $this->created_at) {
                $this->created_at = $dt;
                $this->modifiedColumns[CreditNoteVersionTableMap::CREATED_AT] = true;
            }
        } // if either are not null


        return $this;
    } // setCreatedAt()

    /**
     * Sets the value of [updated_at] column to a normalized version of the date/time value specified.
     *
     * @param      mixed $v string, integer (timestamp), or \DateTime value.
     *               Empty strings are treated as NULL.
     * @return   \CreditNote\Model\CreditNoteVersion The current object (for fluent API support)
     */
    public function setUpdatedAt($v)
    {
        $dt = PropelDateTime::newInstance($v, null, '\DateTime');
        if ($this->updated_at !== null || $dt !== null) {
            if ($dt !== $this->updated_at) {
                $this->updated_at = $dt;
                $this->modifiedColumns[CreditNoteVersionTableMap::UPDATED_AT] = true;
            }
        } // if either are not null


        return $this;
    } // setUpdatedAt()

    /**
     * Set the value of [version] column.
     *
     * @param      int $v new value
     * @return   \CreditNote\Model\CreditNoteVersion The current object (for fluent API support)
     */
    public function setVersion($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->version !== $v) {
            $this->version = $v;
            $this->modifiedColumns[CreditNoteVersionTableMap::VERSION] = true;
        }


        return $this;
    } // setVersion()

    /**
     * Sets the value of [version_created_at] column to a normalized version of the date/time value specified.
     *
     * @param      mixed $v string, integer (timestamp), or \DateTime value.
     *               Empty strings are treated as NULL.
     * @return   \CreditNote\Model\CreditNoteVersion The current object (for fluent API support)
     */
    public function setVersionCreatedAt($v)
    {
        $dt = PropelDateTime::newInstance($v, null, '\DateTime');
        if ($this->version_created_at !== null || $dt !== null) {
            if ($dt !== $this->version_created_at) {
                $this->version_created_at = $dt;
                $this->modifiedColumns[CreditNoteVersionTableMap::VERSION_CREATED_AT] = true;
            }
        } // if either are not null


        return $this;
    } // setVersionCreatedAt()

    /**
     * Set the value of [version_created_by] column.
     *
     * @param      string $v new value
     * @return   \CreditNote\Model\CreditNoteVersion The current object (for fluent API support)
     */
    public function setVersionCreatedBy($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->version_created_by !== $v) {
            $this->version_created_by = $v;
            $this->modifiedColumns[CreditNoteVersionTableMap::VERSION_CREATED_BY] = true;
        }


        return $this;
    } // setVersionCreatedBy()

    /**
     * Set the value of [order_id_version] column.
     *
     * @param      int $v new value
     * @return   \CreditNote\Model\CreditNoteVersion The current object (for fluent API support)
     */
    public function setOrderIdVersion($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->order_id_version !== $v) {
            $this->order_id_version = $v;
            $this->modifiedColumns[CreditNoteVersionTableMap::ORDER_ID_VERSION] = true;
        }


        return $this;
    } // setOrderIdVersion()

    /**
     * Set the value of [customer_id_version] column.
     *
     * @param      int $v new value
     * @return   \CreditNote\Model\CreditNoteVersion The current object (for fluent API support)
     */
    public function setCustomerIdVersion($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->customer_id_version !== $v) {
            $this->customer_id_version = $v;
            $this->modifiedColumns[CreditNoteVersionTableMap::CUSTOMER_ID_VERSION] = true;
        }


        return $this;
    } // setCustomerIdVersion()

    /**
     * Set the value of [parent_id_version] column.
     *
     * @param      int $v new value
     * @return   \CreditNote\Model\CreditNoteVersion The current object (for fluent API support)
     */
    public function setParentIdVersion($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->parent_id_version !== $v) {
            $this->parent_id_version = $v;
            $this->modifiedColumns[CreditNoteVersionTableMap::PARENT_ID_VERSION] = true;
        }


        return $this;
    } // setParentIdVersion()

    /**
     * Set the value of [credit_note_ids] column.
     *
     * @param      array $v new value
     * @return   \CreditNote\Model\CreditNoteVersion The current object (for fluent API support)
     */
    public function setCreditNoteIds($v)
    {
        if ($this->credit_note_ids_unserialized !== $v) {
            $this->credit_note_ids_unserialized = $v;
            $this->credit_note_ids = '| ' . implode(' | ', $v) . ' |';
            $this->modifiedColumns[CreditNoteVersionTableMap::CREDIT_NOTE_IDS] = true;
        }


        return $this;
    } // setCreditNoteIds()

    /**
     * Adds a value to the [credit_note_ids] array column value.
     * @param      mixed $value
     *
     * @return   \CreditNote\Model\CreditNoteVersion The current object (for fluent API support)
     */
    public function addCreditNoteId($value)
    {
        $currentArray = $this->getCreditNoteIds();
        $currentArray []= $value;
        $this->setCreditNoteIds($currentArray);

        return $this;
    } // addCreditNoteId()

    /**
     * Removes a value from the [credit_note_ids] array column value.
     * @param      mixed $value
     *
     * @return   \CreditNote\Model\CreditNoteVersion The current object (for fluent API support)
     */
    public function removeCreditNoteId($value)
    {
        $targetArray = array();
        foreach ($this->getCreditNoteIds() as $element) {
            if ($element != $value) {
                $targetArray []= $element;
            }
        }
        $this->setCreditNoteIds($targetArray);

        return $this;
    } // removeCreditNoteId()

    /**
     * Set the value of [credit_note_versions] column.
     *
     * @param      array $v new value
     * @return   \CreditNote\Model\CreditNoteVersion The current object (for fluent API support)
     */
    public function setCreditNoteVersions($v)
    {
        if ($this->credit_note_versions_unserialized !== $v) {
            $this->credit_note_versions_unserialized = $v;
            $this->credit_note_versions = '| ' . implode(' | ', $v) . ' |';
            $this->modifiedColumns[CreditNoteVersionTableMap::CREDIT_NOTE_VERSIONS] = true;
        }


        return $this;
    } // setCreditNoteVersions()

    /**
     * Adds a value to the [credit_note_versions] array column value.
     * @param      mixed $value
     *
     * @return   \CreditNote\Model\CreditNoteVersion The current object (for fluent API support)
     */
    public function addCreditNoteVersion($value)
    {
        $currentArray = $this->getCreditNoteVersions();
        $currentArray []= $value;
        $this->setCreditNoteVersions($currentArray);

        return $this;
    } // addCreditNoteVersion()

    /**
     * Removes a value from the [credit_note_versions] array column value.
     * @param      mixed $value
     *
     * @return   \CreditNote\Model\CreditNoteVersion The current object (for fluent API support)
     */
    public function removeCreditNoteVersion($value)
    {
        $targetArray = array();
        foreach ($this->getCreditNoteVersions() as $element) {
            if ($element != $value) {
                $targetArray []= $element;
            }
        }
        $this->setCreditNoteVersions($targetArray);

        return $this;
    } // removeCreditNoteVersion()

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
            if ($this->total_price !== '0.000000') {
                return false;
            }

            if ($this->total_price_with_tax !== '0.000000') {
                return false;
            }

            if ($this->discount_without_tax !== '0.000000') {
                return false;
            }

            if ($this->discount_with_tax !== '0.000000') {
                return false;
            }

            if ($this->allow_partial_use !== true) {
                return false;
            }

            if ($this->version !== 0) {
                return false;
            }

            if ($this->order_id_version !== 0) {
                return false;
            }

            if ($this->customer_id_version !== 0) {
                return false;
            }

            if ($this->parent_id_version !== 0) {
                return false;
            }

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
                                  One of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_STUDLYPHPNAME
     *                            TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM.
     *
     * @return int             next starting column
     * @throws PropelException - Any caught Exception will be rewrapped as a PropelException.
     */
    public function hydrate($row, $startcol = 0, $rehydrate = false, $indexType = TableMap::TYPE_NUM)
    {
        try {


            $col = $row[TableMap::TYPE_NUM == $indexType ? 0 + $startcol : CreditNoteVersionTableMap::translateFieldName('Id', TableMap::TYPE_PHPNAME, $indexType)];
            $this->id = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 1 + $startcol : CreditNoteVersionTableMap::translateFieldName('Ref', TableMap::TYPE_PHPNAME, $indexType)];
            $this->ref = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 2 + $startcol : CreditNoteVersionTableMap::translateFieldName('InvoiceRef', TableMap::TYPE_PHPNAME, $indexType)];
            $this->invoice_ref = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 3 + $startcol : CreditNoteVersionTableMap::translateFieldName('InvoiceAddressId', TableMap::TYPE_PHPNAME, $indexType)];
            $this->invoice_address_id = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 4 + $startcol : CreditNoteVersionTableMap::translateFieldName('InvoiceDate', TableMap::TYPE_PHPNAME, $indexType)];
            if ($col === '0000-00-00 00:00:00') {
                $col = null;
            }
            $this->invoice_date = (null !== $col) ? PropelDateTime::newInstance($col, null, '\DateTime') : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 5 + $startcol : CreditNoteVersionTableMap::translateFieldName('OrderId', TableMap::TYPE_PHPNAME, $indexType)];
            $this->order_id = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 6 + $startcol : CreditNoteVersionTableMap::translateFieldName('CustomerId', TableMap::TYPE_PHPNAME, $indexType)];
            $this->customer_id = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 7 + $startcol : CreditNoteVersionTableMap::translateFieldName('ParentId', TableMap::TYPE_PHPNAME, $indexType)];
            $this->parent_id = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 8 + $startcol : CreditNoteVersionTableMap::translateFieldName('TypeId', TableMap::TYPE_PHPNAME, $indexType)];
            $this->type_id = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 9 + $startcol : CreditNoteVersionTableMap::translateFieldName('StatusId', TableMap::TYPE_PHPNAME, $indexType)];
            $this->status_id = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 10 + $startcol : CreditNoteVersionTableMap::translateFieldName('CurrencyId', TableMap::TYPE_PHPNAME, $indexType)];
            $this->currency_id = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 11 + $startcol : CreditNoteVersionTableMap::translateFieldName('CurrencyRate', TableMap::TYPE_PHPNAME, $indexType)];
            $this->currency_rate = (null !== $col) ? (double) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 12 + $startcol : CreditNoteVersionTableMap::translateFieldName('TotalPrice', TableMap::TYPE_PHPNAME, $indexType)];
            $this->total_price = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 13 + $startcol : CreditNoteVersionTableMap::translateFieldName('TotalPriceWithTax', TableMap::TYPE_PHPNAME, $indexType)];
            $this->total_price_with_tax = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 14 + $startcol : CreditNoteVersionTableMap::translateFieldName('DiscountWithoutTax', TableMap::TYPE_PHPNAME, $indexType)];
            $this->discount_without_tax = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 15 + $startcol : CreditNoteVersionTableMap::translateFieldName('DiscountWithTax', TableMap::TYPE_PHPNAME, $indexType)];
            $this->discount_with_tax = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 16 + $startcol : CreditNoteVersionTableMap::translateFieldName('AllowPartialUse', TableMap::TYPE_PHPNAME, $indexType)];
            $this->allow_partial_use = (null !== $col) ? (boolean) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 17 + $startcol : CreditNoteVersionTableMap::translateFieldName('CreatedAt', TableMap::TYPE_PHPNAME, $indexType)];
            if ($col === '0000-00-00 00:00:00') {
                $col = null;
            }
            $this->created_at = (null !== $col) ? PropelDateTime::newInstance($col, null, '\DateTime') : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 18 + $startcol : CreditNoteVersionTableMap::translateFieldName('UpdatedAt', TableMap::TYPE_PHPNAME, $indexType)];
            if ($col === '0000-00-00 00:00:00') {
                $col = null;
            }
            $this->updated_at = (null !== $col) ? PropelDateTime::newInstance($col, null, '\DateTime') : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 19 + $startcol : CreditNoteVersionTableMap::translateFieldName('Version', TableMap::TYPE_PHPNAME, $indexType)];
            $this->version = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 20 + $startcol : CreditNoteVersionTableMap::translateFieldName('VersionCreatedAt', TableMap::TYPE_PHPNAME, $indexType)];
            if ($col === '0000-00-00 00:00:00') {
                $col = null;
            }
            $this->version_created_at = (null !== $col) ? PropelDateTime::newInstance($col, null, '\DateTime') : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 21 + $startcol : CreditNoteVersionTableMap::translateFieldName('VersionCreatedBy', TableMap::TYPE_PHPNAME, $indexType)];
            $this->version_created_by = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 22 + $startcol : CreditNoteVersionTableMap::translateFieldName('OrderIdVersion', TableMap::TYPE_PHPNAME, $indexType)];
            $this->order_id_version = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 23 + $startcol : CreditNoteVersionTableMap::translateFieldName('CustomerIdVersion', TableMap::TYPE_PHPNAME, $indexType)];
            $this->customer_id_version = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 24 + $startcol : CreditNoteVersionTableMap::translateFieldName('ParentIdVersion', TableMap::TYPE_PHPNAME, $indexType)];
            $this->parent_id_version = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 25 + $startcol : CreditNoteVersionTableMap::translateFieldName('CreditNoteIds', TableMap::TYPE_PHPNAME, $indexType)];
            $this->credit_note_ids = $col;
            $this->credit_note_ids_unserialized = null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 26 + $startcol : CreditNoteVersionTableMap::translateFieldName('CreditNoteVersions', TableMap::TYPE_PHPNAME, $indexType)];
            $this->credit_note_versions = $col;
            $this->credit_note_versions_unserialized = null;
            $this->resetModified();

            $this->setNew(false);

            if ($rehydrate) {
                $this->ensureConsistency();
            }

            return $startcol + 27; // 27 = CreditNoteVersionTableMap::NUM_HYDRATE_COLUMNS.

        } catch (Exception $e) {
            throw new PropelException("Error populating \CreditNote\Model\CreditNoteVersion object", 0, $e);
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
        if ($this->aCreditNote !== null && $this->id !== $this->aCreditNote->getId()) {
            $this->aCreditNote = null;
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
            $con = Propel::getServiceContainer()->getReadConnection(CreditNoteVersionTableMap::DATABASE_NAME);
        }

        // We don't need to alter the object instance pool; we're just modifying this instance
        // already in the pool.

        $dataFetcher = ChildCreditNoteVersionQuery::create(null, $this->buildPkeyCriteria())->setFormatter(ModelCriteria::FORMAT_STATEMENT)->find($con);
        $row = $dataFetcher->fetch();
        $dataFetcher->close();
        if (!$row) {
            throw new PropelException('Cannot find matching row in the database to reload object values.');
        }
        $this->hydrate($row, 0, true, $dataFetcher->getIndexType()); // rehydrate

        if ($deep) {  // also de-associate any related objects?

            $this->aCreditNote = null;
        } // if (deep)
    }

    /**
     * Removes this object from datastore and sets delete attribute.
     *
     * @param      ConnectionInterface $con
     * @return void
     * @throws PropelException
     * @see CreditNoteVersion::setDeleted()
     * @see CreditNoteVersion::isDeleted()
     */
    public function delete(ConnectionInterface $con = null)
    {
        if ($this->isDeleted()) {
            throw new PropelException("This object has already been deleted.");
        }

        if ($con === null) {
            $con = Propel::getServiceContainer()->getWriteConnection(CreditNoteVersionTableMap::DATABASE_NAME);
        }

        $con->beginTransaction();
        try {
            $deleteQuery = ChildCreditNoteVersionQuery::create()
                ->filterByPrimaryKey($this->getPrimaryKey());
            $ret = $this->preDelete($con);
            if ($ret) {
                $deleteQuery->delete($con);
                $this->postDelete($con);
                $con->commit();
                $this->setDeleted(true);
            } else {
                $con->commit();
            }
        } catch (Exception $e) {
            $con->rollBack();
            throw $e;
        }
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

        if ($con === null) {
            $con = Propel::getServiceContainer()->getWriteConnection(CreditNoteVersionTableMap::DATABASE_NAME);
        }

        $con->beginTransaction();
        $isInsert = $this->isNew();
        try {
            $ret = $this->preSave($con);
            if ($isInsert) {
                $ret = $ret && $this->preInsert($con);
            } else {
                $ret = $ret && $this->preUpdate($con);
            }
            if ($ret) {
                $affectedRows = $this->doSave($con);
                if ($isInsert) {
                    $this->postInsert($con);
                } else {
                    $this->postUpdate($con);
                }
                $this->postSave($con);
                CreditNoteVersionTableMap::addInstanceToPool($this);
            } else {
                $affectedRows = 0;
            }
            $con->commit();

            return $affectedRows;
        } catch (Exception $e) {
            $con->rollBack();
            throw $e;
        }
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

            if ($this->aCreditNote !== null) {
                if ($this->aCreditNote->isModified() || $this->aCreditNote->isNew()) {
                    $affectedRows += $this->aCreditNote->save($con);
                }
                $this->setCreditNote($this->aCreditNote);
            }

            if ($this->isNew() || $this->isModified()) {
                // persist changes
                if ($this->isNew()) {
                    $this->doInsert($con);
                } else {
                    $this->doUpdate($con);
                }
                $affectedRows += 1;
                $this->resetModified();
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


         // check the columns in natural order for more readable SQL queries
        if ($this->isColumnModified(CreditNoteVersionTableMap::ID)) {
            $modifiedColumns[':p' . $index++]  = 'ID';
        }
        if ($this->isColumnModified(CreditNoteVersionTableMap::REF)) {
            $modifiedColumns[':p' . $index++]  = 'REF';
        }
        if ($this->isColumnModified(CreditNoteVersionTableMap::INVOICE_REF)) {
            $modifiedColumns[':p' . $index++]  = 'INVOICE_REF';
        }
        if ($this->isColumnModified(CreditNoteVersionTableMap::INVOICE_ADDRESS_ID)) {
            $modifiedColumns[':p' . $index++]  = 'INVOICE_ADDRESS_ID';
        }
        if ($this->isColumnModified(CreditNoteVersionTableMap::INVOICE_DATE)) {
            $modifiedColumns[':p' . $index++]  = 'INVOICE_DATE';
        }
        if ($this->isColumnModified(CreditNoteVersionTableMap::ORDER_ID)) {
            $modifiedColumns[':p' . $index++]  = 'ORDER_ID';
        }
        if ($this->isColumnModified(CreditNoteVersionTableMap::CUSTOMER_ID)) {
            $modifiedColumns[':p' . $index++]  = 'CUSTOMER_ID';
        }
        if ($this->isColumnModified(CreditNoteVersionTableMap::PARENT_ID)) {
            $modifiedColumns[':p' . $index++]  = 'PARENT_ID';
        }
        if ($this->isColumnModified(CreditNoteVersionTableMap::TYPE_ID)) {
            $modifiedColumns[':p' . $index++]  = 'TYPE_ID';
        }
        if ($this->isColumnModified(CreditNoteVersionTableMap::STATUS_ID)) {
            $modifiedColumns[':p' . $index++]  = 'STATUS_ID';
        }
        if ($this->isColumnModified(CreditNoteVersionTableMap::CURRENCY_ID)) {
            $modifiedColumns[':p' . $index++]  = 'CURRENCY_ID';
        }
        if ($this->isColumnModified(CreditNoteVersionTableMap::CURRENCY_RATE)) {
            $modifiedColumns[':p' . $index++]  = 'CURRENCY_RATE';
        }
        if ($this->isColumnModified(CreditNoteVersionTableMap::TOTAL_PRICE)) {
            $modifiedColumns[':p' . $index++]  = 'TOTAL_PRICE';
        }
        if ($this->isColumnModified(CreditNoteVersionTableMap::TOTAL_PRICE_WITH_TAX)) {
            $modifiedColumns[':p' . $index++]  = 'TOTAL_PRICE_WITH_TAX';
        }
        if ($this->isColumnModified(CreditNoteVersionTableMap::DISCOUNT_WITHOUT_TAX)) {
            $modifiedColumns[':p' . $index++]  = 'DISCOUNT_WITHOUT_TAX';
        }
        if ($this->isColumnModified(CreditNoteVersionTableMap::DISCOUNT_WITH_TAX)) {
            $modifiedColumns[':p' . $index++]  = 'DISCOUNT_WITH_TAX';
        }
        if ($this->isColumnModified(CreditNoteVersionTableMap::ALLOW_PARTIAL_USE)) {
            $modifiedColumns[':p' . $index++]  = 'ALLOW_PARTIAL_USE';
        }
        if ($this->isColumnModified(CreditNoteVersionTableMap::CREATED_AT)) {
            $modifiedColumns[':p' . $index++]  = 'CREATED_AT';
        }
        if ($this->isColumnModified(CreditNoteVersionTableMap::UPDATED_AT)) {
            $modifiedColumns[':p' . $index++]  = 'UPDATED_AT';
        }
        if ($this->isColumnModified(CreditNoteVersionTableMap::VERSION)) {
            $modifiedColumns[':p' . $index++]  = 'VERSION';
        }
        if ($this->isColumnModified(CreditNoteVersionTableMap::VERSION_CREATED_AT)) {
            $modifiedColumns[':p' . $index++]  = 'VERSION_CREATED_AT';
        }
        if ($this->isColumnModified(CreditNoteVersionTableMap::VERSION_CREATED_BY)) {
            $modifiedColumns[':p' . $index++]  = 'VERSION_CREATED_BY';
        }
        if ($this->isColumnModified(CreditNoteVersionTableMap::ORDER_ID_VERSION)) {
            $modifiedColumns[':p' . $index++]  = 'ORDER_ID_VERSION';
        }
        if ($this->isColumnModified(CreditNoteVersionTableMap::CUSTOMER_ID_VERSION)) {
            $modifiedColumns[':p' . $index++]  = 'CUSTOMER_ID_VERSION';
        }
        if ($this->isColumnModified(CreditNoteVersionTableMap::PARENT_ID_VERSION)) {
            $modifiedColumns[':p' . $index++]  = 'PARENT_ID_VERSION';
        }
        if ($this->isColumnModified(CreditNoteVersionTableMap::CREDIT_NOTE_IDS)) {
            $modifiedColumns[':p' . $index++]  = 'CREDIT_NOTE_IDS';
        }
        if ($this->isColumnModified(CreditNoteVersionTableMap::CREDIT_NOTE_VERSIONS)) {
            $modifiedColumns[':p' . $index++]  = 'CREDIT_NOTE_VERSIONS';
        }

        $sql = sprintf(
            'INSERT INTO credit_note_version (%s) VALUES (%s)',
            implode(', ', $modifiedColumns),
            implode(', ', array_keys($modifiedColumns))
        );

        try {
            $stmt = $con->prepare($sql);
            foreach ($modifiedColumns as $identifier => $columnName) {
                switch ($columnName) {
                    case 'ID':
                        $stmt->bindValue($identifier, $this->id, PDO::PARAM_INT);
                        break;
                    case 'REF':
                        $stmt->bindValue($identifier, $this->ref, PDO::PARAM_STR);
                        break;
                    case 'INVOICE_REF':
                        $stmt->bindValue($identifier, $this->invoice_ref, PDO::PARAM_STR);
                        break;
                    case 'INVOICE_ADDRESS_ID':
                        $stmt->bindValue($identifier, $this->invoice_address_id, PDO::PARAM_INT);
                        break;
                    case 'INVOICE_DATE':
                        $stmt->bindValue($identifier, $this->invoice_date ? $this->invoice_date->format("Y-m-d H:i:s") : null, PDO::PARAM_STR);
                        break;
                    case 'ORDER_ID':
                        $stmt->bindValue($identifier, $this->order_id, PDO::PARAM_INT);
                        break;
                    case 'CUSTOMER_ID':
                        $stmt->bindValue($identifier, $this->customer_id, PDO::PARAM_INT);
                        break;
                    case 'PARENT_ID':
                        $stmt->bindValue($identifier, $this->parent_id, PDO::PARAM_INT);
                        break;
                    case 'TYPE_ID':
                        $stmt->bindValue($identifier, $this->type_id, PDO::PARAM_INT);
                        break;
                    case 'STATUS_ID':
                        $stmt->bindValue($identifier, $this->status_id, PDO::PARAM_INT);
                        break;
                    case 'CURRENCY_ID':
                        $stmt->bindValue($identifier, $this->currency_id, PDO::PARAM_INT);
                        break;
                    case 'CURRENCY_RATE':
                        $stmt->bindValue($identifier, $this->currency_rate, PDO::PARAM_STR);
                        break;
                    case 'TOTAL_PRICE':
                        $stmt->bindValue($identifier, $this->total_price, PDO::PARAM_STR);
                        break;
                    case 'TOTAL_PRICE_WITH_TAX':
                        $stmt->bindValue($identifier, $this->total_price_with_tax, PDO::PARAM_STR);
                        break;
                    case 'DISCOUNT_WITHOUT_TAX':
                        $stmt->bindValue($identifier, $this->discount_without_tax, PDO::PARAM_STR);
                        break;
                    case 'DISCOUNT_WITH_TAX':
                        $stmt->bindValue($identifier, $this->discount_with_tax, PDO::PARAM_STR);
                        break;
                    case 'ALLOW_PARTIAL_USE':
                        $stmt->bindValue($identifier, (int) $this->allow_partial_use, PDO::PARAM_INT);
                        break;
                    case 'CREATED_AT':
                        $stmt->bindValue($identifier, $this->created_at ? $this->created_at->format("Y-m-d H:i:s") : null, PDO::PARAM_STR);
                        break;
                    case 'UPDATED_AT':
                        $stmt->bindValue($identifier, $this->updated_at ? $this->updated_at->format("Y-m-d H:i:s") : null, PDO::PARAM_STR);
                        break;
                    case 'VERSION':
                        $stmt->bindValue($identifier, $this->version, PDO::PARAM_INT);
                        break;
                    case 'VERSION_CREATED_AT':
                        $stmt->bindValue($identifier, $this->version_created_at ? $this->version_created_at->format("Y-m-d H:i:s") : null, PDO::PARAM_STR);
                        break;
                    case 'VERSION_CREATED_BY':
                        $stmt->bindValue($identifier, $this->version_created_by, PDO::PARAM_STR);
                        break;
                    case 'ORDER_ID_VERSION':
                        $stmt->bindValue($identifier, $this->order_id_version, PDO::PARAM_INT);
                        break;
                    case 'CUSTOMER_ID_VERSION':
                        $stmt->bindValue($identifier, $this->customer_id_version, PDO::PARAM_INT);
                        break;
                    case 'PARENT_ID_VERSION':
                        $stmt->bindValue($identifier, $this->parent_id_version, PDO::PARAM_INT);
                        break;
                    case 'CREDIT_NOTE_IDS':
                        $stmt->bindValue($identifier, $this->credit_note_ids, PDO::PARAM_STR);
                        break;
                    case 'CREDIT_NOTE_VERSIONS':
                        $stmt->bindValue($identifier, $this->credit_note_versions, PDO::PARAM_STR);
                        break;
                }
            }
            $stmt->execute();
        } catch (Exception $e) {
            Propel::log($e->getMessage(), Propel::LOG_ERR);
            throw new PropelException(sprintf('Unable to execute INSERT statement [%s]', $sql), 0, $e);
        }

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
     *                     one of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_STUDLYPHPNAME
     *                     TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM.
     *                     Defaults to TableMap::TYPE_PHPNAME.
     * @return mixed Value of field.
     */
    public function getByName($name, $type = TableMap::TYPE_PHPNAME)
    {
        $pos = CreditNoteVersionTableMap::translateFieldName($name, $type, TableMap::TYPE_NUM);
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
                return $this->getRef();
                break;
            case 2:
                return $this->getInvoiceRef();
                break;
            case 3:
                return $this->getInvoiceAddressId();
                break;
            case 4:
                return $this->getInvoiceDate();
                break;
            case 5:
                return $this->getOrderId();
                break;
            case 6:
                return $this->getCustomerId();
                break;
            case 7:
                return $this->getParentId();
                break;
            case 8:
                return $this->getTypeId();
                break;
            case 9:
                return $this->getStatusId();
                break;
            case 10:
                return $this->getCurrencyId();
                break;
            case 11:
                return $this->getCurrencyRate();
                break;
            case 12:
                return $this->getTotalPrice();
                break;
            case 13:
                return $this->getTotalPriceWithTax();
                break;
            case 14:
                return $this->getDiscountWithoutTax();
                break;
            case 15:
                return $this->getDiscountWithTax();
                break;
            case 16:
                return $this->getAllowPartialUse();
                break;
            case 17:
                return $this->getCreatedAt();
                break;
            case 18:
                return $this->getUpdatedAt();
                break;
            case 19:
                return $this->getVersion();
                break;
            case 20:
                return $this->getVersionCreatedAt();
                break;
            case 21:
                return $this->getVersionCreatedBy();
                break;
            case 22:
                return $this->getOrderIdVersion();
                break;
            case 23:
                return $this->getCustomerIdVersion();
                break;
            case 24:
                return $this->getParentIdVersion();
                break;
            case 25:
                return $this->getCreditNoteIds();
                break;
            case 26:
                return $this->getCreditNoteVersions();
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
     * @param     string  $keyType (optional) One of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_STUDLYPHPNAME,
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
        if (isset($alreadyDumpedObjects['CreditNoteVersion'][serialize($this->getPrimaryKey())])) {
            return '*RECURSION*';
        }
        $alreadyDumpedObjects['CreditNoteVersion'][serialize($this->getPrimaryKey())] = true;
        $keys = CreditNoteVersionTableMap::getFieldNames($keyType);
        $result = array(
            $keys[0] => $this->getId(),
            $keys[1] => $this->getRef(),
            $keys[2] => $this->getInvoiceRef(),
            $keys[3] => $this->getInvoiceAddressId(),
            $keys[4] => $this->getInvoiceDate(),
            $keys[5] => $this->getOrderId(),
            $keys[6] => $this->getCustomerId(),
            $keys[7] => $this->getParentId(),
            $keys[8] => $this->getTypeId(),
            $keys[9] => $this->getStatusId(),
            $keys[10] => $this->getCurrencyId(),
            $keys[11] => $this->getCurrencyRate(),
            $keys[12] => $this->getTotalPrice(),
            $keys[13] => $this->getTotalPriceWithTax(),
            $keys[14] => $this->getDiscountWithoutTax(),
            $keys[15] => $this->getDiscountWithTax(),
            $keys[16] => $this->getAllowPartialUse(),
            $keys[17] => $this->getCreatedAt(),
            $keys[18] => $this->getUpdatedAt(),
            $keys[19] => $this->getVersion(),
            $keys[20] => $this->getVersionCreatedAt(),
            $keys[21] => $this->getVersionCreatedBy(),
            $keys[22] => $this->getOrderIdVersion(),
            $keys[23] => $this->getCustomerIdVersion(),
            $keys[24] => $this->getParentIdVersion(),
            $keys[25] => $this->getCreditNoteIds(),
            $keys[26] => $this->getCreditNoteVersions(),
        );
        $virtualColumns = $this->virtualColumns;
        foreach ($virtualColumns as $key => $virtualColumn) {
            $result[$key] = $virtualColumn;
        }

        if ($includeForeignObjects) {
            if (null !== $this->aCreditNote) {
                $result['CreditNote'] = $this->aCreditNote->toArray($keyType, $includeLazyLoadColumns,  $alreadyDumpedObjects, true);
            }
        }

        return $result;
    }

    /**
     * Sets a field from the object by name passed in as a string.
     *
     * @param      string $name
     * @param      mixed  $value field value
     * @param      string $type The type of fieldname the $name is of:
     *                     one of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_STUDLYPHPNAME
     *                     TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM.
     *                     Defaults to TableMap::TYPE_PHPNAME.
     * @return void
     */
    public function setByName($name, $value, $type = TableMap::TYPE_PHPNAME)
    {
        $pos = CreditNoteVersionTableMap::translateFieldName($name, $type, TableMap::TYPE_NUM);

        return $this->setByPosition($pos, $value);
    }

    /**
     * Sets a field from the object by Position as specified in the xml schema.
     * Zero-based.
     *
     * @param      int $pos position in xml schema
     * @param      mixed $value field value
     * @return void
     */
    public function setByPosition($pos, $value)
    {
        switch ($pos) {
            case 0:
                $this->setId($value);
                break;
            case 1:
                $this->setRef($value);
                break;
            case 2:
                $this->setInvoiceRef($value);
                break;
            case 3:
                $this->setInvoiceAddressId($value);
                break;
            case 4:
                $this->setInvoiceDate($value);
                break;
            case 5:
                $this->setOrderId($value);
                break;
            case 6:
                $this->setCustomerId($value);
                break;
            case 7:
                $this->setParentId($value);
                break;
            case 8:
                $this->setTypeId($value);
                break;
            case 9:
                $this->setStatusId($value);
                break;
            case 10:
                $this->setCurrencyId($value);
                break;
            case 11:
                $this->setCurrencyRate($value);
                break;
            case 12:
                $this->setTotalPrice($value);
                break;
            case 13:
                $this->setTotalPriceWithTax($value);
                break;
            case 14:
                $this->setDiscountWithoutTax($value);
                break;
            case 15:
                $this->setDiscountWithTax($value);
                break;
            case 16:
                $this->setAllowPartialUse($value);
                break;
            case 17:
                $this->setCreatedAt($value);
                break;
            case 18:
                $this->setUpdatedAt($value);
                break;
            case 19:
                $this->setVersion($value);
                break;
            case 20:
                $this->setVersionCreatedAt($value);
                break;
            case 21:
                $this->setVersionCreatedBy($value);
                break;
            case 22:
                $this->setOrderIdVersion($value);
                break;
            case 23:
                $this->setCustomerIdVersion($value);
                break;
            case 24:
                $this->setParentIdVersion($value);
                break;
            case 25:
                if (!is_array($value)) {
                    $v = trim(substr($value, 2, -2));
                    $value = $v ? explode(' | ', $v) : array();
                }
                $this->setCreditNoteIds($value);
                break;
            case 26:
                if (!is_array($value)) {
                    $v = trim(substr($value, 2, -2));
                    $value = $v ? explode(' | ', $v) : array();
                }
                $this->setCreditNoteVersions($value);
                break;
        } // switch()
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
     * of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_STUDLYPHPNAME,
     * TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM.
     * The default key type is the column's TableMap::TYPE_PHPNAME.
     *
     * @param      array  $arr     An array to populate the object from.
     * @param      string $keyType The type of keys the array uses.
     * @return void
     */
    public function fromArray($arr, $keyType = TableMap::TYPE_PHPNAME)
    {
        $keys = CreditNoteVersionTableMap::getFieldNames($keyType);

        if (array_key_exists($keys[0], $arr)) $this->setId($arr[$keys[0]]);
        if (array_key_exists($keys[1], $arr)) $this->setRef($arr[$keys[1]]);
        if (array_key_exists($keys[2], $arr)) $this->setInvoiceRef($arr[$keys[2]]);
        if (array_key_exists($keys[3], $arr)) $this->setInvoiceAddressId($arr[$keys[3]]);
        if (array_key_exists($keys[4], $arr)) $this->setInvoiceDate($arr[$keys[4]]);
        if (array_key_exists($keys[5], $arr)) $this->setOrderId($arr[$keys[5]]);
        if (array_key_exists($keys[6], $arr)) $this->setCustomerId($arr[$keys[6]]);
        if (array_key_exists($keys[7], $arr)) $this->setParentId($arr[$keys[7]]);
        if (array_key_exists($keys[8], $arr)) $this->setTypeId($arr[$keys[8]]);
        if (array_key_exists($keys[9], $arr)) $this->setStatusId($arr[$keys[9]]);
        if (array_key_exists($keys[10], $arr)) $this->setCurrencyId($arr[$keys[10]]);
        if (array_key_exists($keys[11], $arr)) $this->setCurrencyRate($arr[$keys[11]]);
        if (array_key_exists($keys[12], $arr)) $this->setTotalPrice($arr[$keys[12]]);
        if (array_key_exists($keys[13], $arr)) $this->setTotalPriceWithTax($arr[$keys[13]]);
        if (array_key_exists($keys[14], $arr)) $this->setDiscountWithoutTax($arr[$keys[14]]);
        if (array_key_exists($keys[15], $arr)) $this->setDiscountWithTax($arr[$keys[15]]);
        if (array_key_exists($keys[16], $arr)) $this->setAllowPartialUse($arr[$keys[16]]);
        if (array_key_exists($keys[17], $arr)) $this->setCreatedAt($arr[$keys[17]]);
        if (array_key_exists($keys[18], $arr)) $this->setUpdatedAt($arr[$keys[18]]);
        if (array_key_exists($keys[19], $arr)) $this->setVersion($arr[$keys[19]]);
        if (array_key_exists($keys[20], $arr)) $this->setVersionCreatedAt($arr[$keys[20]]);
        if (array_key_exists($keys[21], $arr)) $this->setVersionCreatedBy($arr[$keys[21]]);
        if (array_key_exists($keys[22], $arr)) $this->setOrderIdVersion($arr[$keys[22]]);
        if (array_key_exists($keys[23], $arr)) $this->setCustomerIdVersion($arr[$keys[23]]);
        if (array_key_exists($keys[24], $arr)) $this->setParentIdVersion($arr[$keys[24]]);
        if (array_key_exists($keys[25], $arr)) $this->setCreditNoteIds($arr[$keys[25]]);
        if (array_key_exists($keys[26], $arr)) $this->setCreditNoteVersions($arr[$keys[26]]);
    }

    /**
     * Build a Criteria object containing the values of all modified columns in this object.
     *
     * @return Criteria The Criteria object containing all modified values.
     */
    public function buildCriteria()
    {
        $criteria = new Criteria(CreditNoteVersionTableMap::DATABASE_NAME);

        if ($this->isColumnModified(CreditNoteVersionTableMap::ID)) $criteria->add(CreditNoteVersionTableMap::ID, $this->id);
        if ($this->isColumnModified(CreditNoteVersionTableMap::REF)) $criteria->add(CreditNoteVersionTableMap::REF, $this->ref);
        if ($this->isColumnModified(CreditNoteVersionTableMap::INVOICE_REF)) $criteria->add(CreditNoteVersionTableMap::INVOICE_REF, $this->invoice_ref);
        if ($this->isColumnModified(CreditNoteVersionTableMap::INVOICE_ADDRESS_ID)) $criteria->add(CreditNoteVersionTableMap::INVOICE_ADDRESS_ID, $this->invoice_address_id);
        if ($this->isColumnModified(CreditNoteVersionTableMap::INVOICE_DATE)) $criteria->add(CreditNoteVersionTableMap::INVOICE_DATE, $this->invoice_date);
        if ($this->isColumnModified(CreditNoteVersionTableMap::ORDER_ID)) $criteria->add(CreditNoteVersionTableMap::ORDER_ID, $this->order_id);
        if ($this->isColumnModified(CreditNoteVersionTableMap::CUSTOMER_ID)) $criteria->add(CreditNoteVersionTableMap::CUSTOMER_ID, $this->customer_id);
        if ($this->isColumnModified(CreditNoteVersionTableMap::PARENT_ID)) $criteria->add(CreditNoteVersionTableMap::PARENT_ID, $this->parent_id);
        if ($this->isColumnModified(CreditNoteVersionTableMap::TYPE_ID)) $criteria->add(CreditNoteVersionTableMap::TYPE_ID, $this->type_id);
        if ($this->isColumnModified(CreditNoteVersionTableMap::STATUS_ID)) $criteria->add(CreditNoteVersionTableMap::STATUS_ID, $this->status_id);
        if ($this->isColumnModified(CreditNoteVersionTableMap::CURRENCY_ID)) $criteria->add(CreditNoteVersionTableMap::CURRENCY_ID, $this->currency_id);
        if ($this->isColumnModified(CreditNoteVersionTableMap::CURRENCY_RATE)) $criteria->add(CreditNoteVersionTableMap::CURRENCY_RATE, $this->currency_rate);
        if ($this->isColumnModified(CreditNoteVersionTableMap::TOTAL_PRICE)) $criteria->add(CreditNoteVersionTableMap::TOTAL_PRICE, $this->total_price);
        if ($this->isColumnModified(CreditNoteVersionTableMap::TOTAL_PRICE_WITH_TAX)) $criteria->add(CreditNoteVersionTableMap::TOTAL_PRICE_WITH_TAX, $this->total_price_with_tax);
        if ($this->isColumnModified(CreditNoteVersionTableMap::DISCOUNT_WITHOUT_TAX)) $criteria->add(CreditNoteVersionTableMap::DISCOUNT_WITHOUT_TAX, $this->discount_without_tax);
        if ($this->isColumnModified(CreditNoteVersionTableMap::DISCOUNT_WITH_TAX)) $criteria->add(CreditNoteVersionTableMap::DISCOUNT_WITH_TAX, $this->discount_with_tax);
        if ($this->isColumnModified(CreditNoteVersionTableMap::ALLOW_PARTIAL_USE)) $criteria->add(CreditNoteVersionTableMap::ALLOW_PARTIAL_USE, $this->allow_partial_use);
        if ($this->isColumnModified(CreditNoteVersionTableMap::CREATED_AT)) $criteria->add(CreditNoteVersionTableMap::CREATED_AT, $this->created_at);
        if ($this->isColumnModified(CreditNoteVersionTableMap::UPDATED_AT)) $criteria->add(CreditNoteVersionTableMap::UPDATED_AT, $this->updated_at);
        if ($this->isColumnModified(CreditNoteVersionTableMap::VERSION)) $criteria->add(CreditNoteVersionTableMap::VERSION, $this->version);
        if ($this->isColumnModified(CreditNoteVersionTableMap::VERSION_CREATED_AT)) $criteria->add(CreditNoteVersionTableMap::VERSION_CREATED_AT, $this->version_created_at);
        if ($this->isColumnModified(CreditNoteVersionTableMap::VERSION_CREATED_BY)) $criteria->add(CreditNoteVersionTableMap::VERSION_CREATED_BY, $this->version_created_by);
        if ($this->isColumnModified(CreditNoteVersionTableMap::ORDER_ID_VERSION)) $criteria->add(CreditNoteVersionTableMap::ORDER_ID_VERSION, $this->order_id_version);
        if ($this->isColumnModified(CreditNoteVersionTableMap::CUSTOMER_ID_VERSION)) $criteria->add(CreditNoteVersionTableMap::CUSTOMER_ID_VERSION, $this->customer_id_version);
        if ($this->isColumnModified(CreditNoteVersionTableMap::PARENT_ID_VERSION)) $criteria->add(CreditNoteVersionTableMap::PARENT_ID_VERSION, $this->parent_id_version);
        if ($this->isColumnModified(CreditNoteVersionTableMap::CREDIT_NOTE_IDS)) $criteria->add(CreditNoteVersionTableMap::CREDIT_NOTE_IDS, $this->credit_note_ids);
        if ($this->isColumnModified(CreditNoteVersionTableMap::CREDIT_NOTE_VERSIONS)) $criteria->add(CreditNoteVersionTableMap::CREDIT_NOTE_VERSIONS, $this->credit_note_versions);

        return $criteria;
    }

    /**
     * Builds a Criteria object containing the primary key for this object.
     *
     * Unlike buildCriteria() this method includes the primary key values regardless
     * of whether or not they have been modified.
     *
     * @return Criteria The Criteria object containing value(s) for primary key(s).
     */
    public function buildPkeyCriteria()
    {
        $criteria = new Criteria(CreditNoteVersionTableMap::DATABASE_NAME);
        $criteria->add(CreditNoteVersionTableMap::ID, $this->id);
        $criteria->add(CreditNoteVersionTableMap::VERSION, $this->version);

        return $criteria;
    }

    /**
     * Returns the composite primary key for this object.
     * The array elements will be in same order as specified in XML.
     * @return array
     */
    public function getPrimaryKey()
    {
        $pks = array();
        $pks[0] = $this->getId();
        $pks[1] = $this->getVersion();

        return $pks;
    }

    /**
     * Set the [composite] primary key.
     *
     * @param      array $keys The elements of the composite key (order must match the order in XML file).
     * @return void
     */
    public function setPrimaryKey($keys)
    {
        $this->setId($keys[0]);
        $this->setVersion($keys[1]);
    }

    /**
     * Returns true if the primary key for this object is null.
     * @return boolean
     */
    public function isPrimaryKeyNull()
    {

        return (null === $this->getId()) && (null === $this->getVersion());
    }

    /**
     * Sets contents of passed object to values from current object.
     *
     * If desired, this method can also make copies of all associated (fkey referrers)
     * objects.
     *
     * @param      object $copyObj An object of \CreditNote\Model\CreditNoteVersion (or compatible) type.
     * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @param      boolean $makeNew Whether to reset autoincrement PKs and make the object new.
     * @throws PropelException
     */
    public function copyInto($copyObj, $deepCopy = false, $makeNew = true)
    {
        $copyObj->setId($this->getId());
        $copyObj->setRef($this->getRef());
        $copyObj->setInvoiceRef($this->getInvoiceRef());
        $copyObj->setInvoiceAddressId($this->getInvoiceAddressId());
        $copyObj->setInvoiceDate($this->getInvoiceDate());
        $copyObj->setOrderId($this->getOrderId());
        $copyObj->setCustomerId($this->getCustomerId());
        $copyObj->setParentId($this->getParentId());
        $copyObj->setTypeId($this->getTypeId());
        $copyObj->setStatusId($this->getStatusId());
        $copyObj->setCurrencyId($this->getCurrencyId());
        $copyObj->setCurrencyRate($this->getCurrencyRate());
        $copyObj->setTotalPrice($this->getTotalPrice());
        $copyObj->setTotalPriceWithTax($this->getTotalPriceWithTax());
        $copyObj->setDiscountWithoutTax($this->getDiscountWithoutTax());
        $copyObj->setDiscountWithTax($this->getDiscountWithTax());
        $copyObj->setAllowPartialUse($this->getAllowPartialUse());
        $copyObj->setCreatedAt($this->getCreatedAt());
        $copyObj->setUpdatedAt($this->getUpdatedAt());
        $copyObj->setVersion($this->getVersion());
        $copyObj->setVersionCreatedAt($this->getVersionCreatedAt());
        $copyObj->setVersionCreatedBy($this->getVersionCreatedBy());
        $copyObj->setOrderIdVersion($this->getOrderIdVersion());
        $copyObj->setCustomerIdVersion($this->getCustomerIdVersion());
        $copyObj->setParentIdVersion($this->getParentIdVersion());
        $copyObj->setCreditNoteIds($this->getCreditNoteIds());
        $copyObj->setCreditNoteVersions($this->getCreditNoteVersions());
        if ($makeNew) {
            $copyObj->setNew(true);
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
     * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @return                 \CreditNote\Model\CreditNoteVersion Clone of current object.
     * @throws PropelException
     */
    public function copy($deepCopy = false)
    {
        // we use get_class(), because this might be a subclass
        $clazz = get_class($this);
        $copyObj = new $clazz();
        $this->copyInto($copyObj, $deepCopy);

        return $copyObj;
    }

    /**
     * Declares an association between this object and a ChildCreditNote object.
     *
     * @param                  ChildCreditNote $v
     * @return                 \CreditNote\Model\CreditNoteVersion The current object (for fluent API support)
     * @throws PropelException
     */
    public function setCreditNote(ChildCreditNote $v = null)
    {
        if ($v === null) {
            $this->setId(NULL);
        } else {
            $this->setId($v->getId());
        }

        $this->aCreditNote = $v;

        // Add binding for other direction of this n:n relationship.
        // If this object has already been added to the ChildCreditNote object, it will not be re-added.
        if ($v !== null) {
            $v->addCreditNoteVersion($this);
        }


        return $this;
    }


    /**
     * Get the associated ChildCreditNote object
     *
     * @param      ConnectionInterface $con Optional Connection object.
     * @return                 ChildCreditNote The associated ChildCreditNote object.
     * @throws PropelException
     */
    public function getCreditNote(ConnectionInterface $con = null)
    {
        if ($this->aCreditNote === null && ($this->id !== null)) {
            $this->aCreditNote = ChildCreditNoteQuery::create()->findPk($this->id, $con);
            /* The following can be used additionally to
                guarantee the related object contains a reference
                to this object.  This level of coupling may, however, be
                undesirable since it could result in an only partially populated collection
                in the referenced object.
                $this->aCreditNote->addCreditNoteVersions($this);
             */
        }

        return $this->aCreditNote;
    }

    /**
     * Clears the current object and sets all attributes to their default values
     */
    public function clear()
    {
        $this->id = null;
        $this->ref = null;
        $this->invoice_ref = null;
        $this->invoice_address_id = null;
        $this->invoice_date = null;
        $this->order_id = null;
        $this->customer_id = null;
        $this->parent_id = null;
        $this->type_id = null;
        $this->status_id = null;
        $this->currency_id = null;
        $this->currency_rate = null;
        $this->total_price = null;
        $this->total_price_with_tax = null;
        $this->discount_without_tax = null;
        $this->discount_with_tax = null;
        $this->allow_partial_use = null;
        $this->created_at = null;
        $this->updated_at = null;
        $this->version = null;
        $this->version_created_at = null;
        $this->version_created_by = null;
        $this->order_id_version = null;
        $this->customer_id_version = null;
        $this->parent_id_version = null;
        $this->credit_note_ids = null;
        $this->credit_note_ids_unserialized = null;
        $this->credit_note_versions = null;
        $this->credit_note_versions_unserialized = null;
        $this->alreadyInSave = false;
        $this->clearAllReferences();
        $this->applyDefaultValues();
        $this->resetModified();
        $this->setNew(true);
        $this->setDeleted(false);
    }

    /**
     * Resets all references to other model objects or collections of model objects.
     *
     * This method is a user-space workaround for PHP's inability to garbage collect
     * objects with circular references (even in PHP 5.3). This is currently necessary
     * when using Propel in certain daemon or large-volume/high-memory operations.
     *
     * @param      boolean $deep Whether to also clear the references on all referrer objects.
     */
    public function clearAllReferences($deep = false)
    {
        if ($deep) {
        } // if ($deep)

        $this->aCreditNote = null;
    }

    /**
     * Return the string representation of this object
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->exportTo(CreditNoteVersionTableMap::DEFAULT_STRING_FORMAT);
    }

    /**
     * Code to be run before persisting the object
     * @param  ConnectionInterface $con
     * @return boolean
     */
    public function preSave(ConnectionInterface $con = null)
    {
        return true;
    }

    /**
     * Code to be run after persisting the object
     * @param ConnectionInterface $con
     */
    public function postSave(ConnectionInterface $con = null)
    {

    }

    /**
     * Code to be run before inserting to database
     * @param  ConnectionInterface $con
     * @return boolean
     */
    public function preInsert(ConnectionInterface $con = null)
    {
        return true;
    }

    /**
     * Code to be run after inserting to database
     * @param ConnectionInterface $con
     */
    public function postInsert(ConnectionInterface $con = null)
    {

    }

    /**
     * Code to be run before updating the object in database
     * @param  ConnectionInterface $con
     * @return boolean
     */
    public function preUpdate(ConnectionInterface $con = null)
    {
        return true;
    }

    /**
     * Code to be run after updating the object in database
     * @param ConnectionInterface $con
     */
    public function postUpdate(ConnectionInterface $con = null)
    {

    }

    /**
     * Code to be run before deleting the object in database
     * @param  ConnectionInterface $con
     * @return boolean
     */
    public function preDelete(ConnectionInterface $con = null)
    {
        return true;
    }

    /**
     * Code to be run after deleting the object in database
     * @param ConnectionInterface $con
     */
    public function postDelete(ConnectionInterface $con = null)
    {

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
