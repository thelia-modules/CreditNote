<?php

namespace CreditNote\Model\Base;

use \DateTime;
use \Exception;
use \PDO;
use CreditNote\Model\CartCreditNote as ChildCartCreditNote;
use CreditNote\Model\CartCreditNoteQuery as ChildCartCreditNoteQuery;
use CreditNote\Model\CreditNote as ChildCreditNote;
use CreditNote\Model\CreditNoteAddress as ChildCreditNoteAddress;
use CreditNote\Model\CreditNoteAddressQuery as ChildCreditNoteAddressQuery;
use CreditNote\Model\CreditNoteComment as ChildCreditNoteComment;
use CreditNote\Model\CreditNoteCommentQuery as ChildCreditNoteCommentQuery;
use CreditNote\Model\CreditNoteDetail as ChildCreditNoteDetail;
use CreditNote\Model\CreditNoteDetailQuery as ChildCreditNoteDetailQuery;
use CreditNote\Model\CreditNoteQuery as ChildCreditNoteQuery;
use CreditNote\Model\CreditNoteStatus as ChildCreditNoteStatus;
use CreditNote\Model\CreditNoteStatusQuery as ChildCreditNoteStatusQuery;
use CreditNote\Model\CreditNoteType as ChildCreditNoteType;
use CreditNote\Model\CreditNoteTypeQuery as ChildCreditNoteTypeQuery;
use CreditNote\Model\CreditNoteVersion as ChildCreditNoteVersion;
use CreditNote\Model\CreditNoteVersionQuery as ChildCreditNoteVersionQuery;
use CreditNote\Model\OrderCreditNote as ChildOrderCreditNote;
use CreditNote\Model\OrderCreditNoteQuery as ChildOrderCreditNoteQuery;
use CreditNote\Model\Event\CreditNoteEvent;
use CreditNote\Model\Map\CartCreditNoteTableMap;
use CreditNote\Model\Map\CreditNoteCommentTableMap;
use CreditNote\Model\Map\CreditNoteDetailTableMap;
use CreditNote\Model\Map\CreditNoteTableMap;
use CreditNote\Model\Map\CreditNoteVersionTableMap;
use CreditNote\Model\Map\OrderCreditNoteTableMap;
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
use Thelia\Model\Currency;
use Thelia\Model\CurrencyQuery;
use Thelia\Model\Customer;
use Thelia\Model\CustomerQuery;
use Thelia\Model\CustomerVersionQuery;
use Thelia\Model\Order;
use Thelia\Model\OrderQuery;
use Thelia\Model\OrderVersionQuery;

/**
 * Base class that represents a row from the 'credit_note' table.
 *
 *
 *
 * @package    propel.generator.CreditNote.Model.Base
 */
abstract class CreditNote implements ActiveRecordInterface
{
    /**
     * TableMap class name
     */
    const TABLE_MAP = '\\CreditNote\\Model\\Map\\CreditNoteTableMap';


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
     * The value for the ref field.
     *
     * @var        string
     */
    protected $ref;

    /**
     * The value for the invoice_ref field.
     *
     * @var        string
     */
    protected $invoice_ref;

    /**
     * The value for the invoice_address_id field.
     *
     * @var        int
     */
    protected $invoice_address_id;

    /**
     * The value for the invoice_date field.
     *
     * @var        DateTime
     */
    protected $invoice_date;

    /**
     * The value for the order_id field.
     *
     * @var        int
     */
    protected $order_id;

    /**
     * The value for the customer_id field.
     *
     * @var        int
     */
    protected $customer_id;

    /**
     * The value for the parent_id field.
     *
     * @var        int
     */
    protected $parent_id;

    /**
     * The value for the type_id field.
     *
     * @var        int
     */
    protected $type_id;

    /**
     * The value for the status_id field.
     *
     * @var        int
     */
    protected $status_id;

    /**
     * The value for the currency_id field.
     *
     * @var        int
     */
    protected $currency_id;

    /**
     * The value for the currency_rate field.
     *
     * @var        double
     */
    protected $currency_rate;

    /**
     * The value for the total_price field.
     *
     * Note: this column has a database default value of: '0.000000'
     * @var        string
     */
    protected $total_price;

    /**
     * The value for the total_price_with_tax field.
     *
     * Note: this column has a database default value of: '0.000000'
     * @var        string
     */
    protected $total_price_with_tax;

    /**
     * The value for the discount_without_tax field.
     *
     * Note: this column has a database default value of: '0.000000'
     * @var        string
     */
    protected $discount_without_tax;

    /**
     * The value for the discount_with_tax field.
     *
     * Note: this column has a database default value of: '0.000000'
     * @var        string
     */
    protected $discount_with_tax;

    /**
     * The value for the allow_partial_use field.
     *
     * Note: this column has a database default value of: true
     * @var        boolean
     */
    protected $allow_partial_use;

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
     * The value for the version field.
     *
     * Note: this column has a database default value of: 0
     * @var        int
     */
    protected $version;

    /**
     * The value for the version_created_at field.
     *
     * @var        DateTime
     */
    protected $version_created_at;

    /**
     * The value for the version_created_by field.
     *
     * @var        string
     */
    protected $version_created_by;

    /**
     * @var        Order
     */
    protected $aOrder;

    /**
     * @var        Customer
     */
    protected $aCustomer;

    /**
     * @var        ChildCreditNote
     */
    protected $aCreditNoteRelatedByParentId;

    /**
     * @var        ChildCreditNoteType
     */
    protected $aCreditNoteType;

    /**
     * @var        ChildCreditNoteStatus
     */
    protected $aCreditNoteStatus;

    /**
     * @var        Currency
     */
    protected $aCurrency;

    /**
     * @var        ChildCreditNoteAddress
     */
    protected $aCreditNoteAddress;

    /**
     * @var        ObjectCollection|ChildCreditNote[] Collection to store aggregation of ChildCreditNote objects.
     */
    protected $collCreditNotesRelatedById;
    protected $collCreditNotesRelatedByIdPartial;

    /**
     * @var        ObjectCollection|ChildOrderCreditNote[] Collection to store aggregation of ChildOrderCreditNote objects.
     */
    protected $collOrderCreditNotes;
    protected $collOrderCreditNotesPartial;

    /**
     * @var        ObjectCollection|ChildCartCreditNote[] Collection to store aggregation of ChildCartCreditNote objects.
     */
    protected $collCartCreditNotes;
    protected $collCartCreditNotesPartial;

    /**
     * @var        ObjectCollection|ChildCreditNoteDetail[] Collection to store aggregation of ChildCreditNoteDetail objects.
     */
    protected $collCreditNoteDetails;
    protected $collCreditNoteDetailsPartial;

    /**
     * @var        ObjectCollection|ChildCreditNoteComment[] Collection to store aggregation of ChildCreditNoteComment objects.
     */
    protected $collCreditNoteComments;
    protected $collCreditNoteCommentsPartial;

    /**
     * @var        ObjectCollection|ChildCreditNoteVersion[] Collection to store aggregation of ChildCreditNoteVersion objects.
     */
    protected $collCreditNoteVersions;
    protected $collCreditNoteVersionsPartial;

    /**
     * Flag to prevent endless save loop, if this object is referenced
     * by another object which falls in this transaction.
     *
     * @var boolean
     */
    protected $alreadyInSave = false;

    // versionable behavior


    /**
     * @var bool
     */
    protected $enforceVersion = false;

    /**
     * An array of objects scheduled for deletion.
     * @var ObjectCollection|ChildCreditNote[]
     */
    protected $creditNotesRelatedByIdScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var ObjectCollection|ChildOrderCreditNote[]
     */
    protected $orderCreditNotesScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var ObjectCollection|ChildCartCreditNote[]
     */
    protected $cartCreditNotesScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var ObjectCollection|ChildCreditNoteDetail[]
     */
    protected $creditNoteDetailsScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var ObjectCollection|ChildCreditNoteComment[]
     */
    protected $creditNoteCommentsScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var ObjectCollection|ChildCreditNoteVersion[]
     */
    protected $creditNoteVersionsScheduledForDeletion = null;

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
    }

    /**
     * Initializes internal state of CreditNote\Model\Base\CreditNote object.
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
     * Compares this with another <code>CreditNote</code> instance.  If
     * <code>obj</code> is an instance of <code>CreditNote</code>, delegates to
     * <code>equals(CreditNote)</code>.  Otherwise, returns <code>false</code>.
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
     * @return $this|CreditNote The current object, for fluid interface
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
     * Get the [ref] column value.
     *
     * @return string
     */
    public function getRef()
    {
        return $this->ref;
    }

    /**
     * Get the [invoice_ref] column value.
     *
     * @return string
     */
    public function getInvoiceRef()
    {
        return $this->invoice_ref;
    }

    /**
     * Get the [invoice_address_id] column value.
     *
     * @return int
     */
    public function getInvoiceAddressId()
    {
        return $this->invoice_address_id;
    }

    /**
     * Get the [optionally formatted] temporal [invoice_date] column value.
     *
     *
     * @param      string|null $format The date/time format string (either date()-style or strftime()-style).
     *                            If format is NULL, then the raw DateTime object will be returned.
     *
     * @return string|DateTime Formatted date/time value as string or DateTime object (if format is NULL), NULL if column is NULL, and 0 if column value is 0000-00-00 00:00:00
     *
     * @throws PropelException - if unable to parse/validate the date/time value.
     */
    public function getInvoiceDate($format = NULL)
    {
        if ($format === null) {
            return $this->invoice_date;
        } else {
            return $this->invoice_date instanceof \DateTimeInterface ? $this->invoice_date->format($format) : null;
        }
    }

    /**
     * Get the [order_id] column value.
     *
     * @return int
     */
    public function getOrderId()
    {
        return $this->order_id;
    }

    /**
     * Get the [customer_id] column value.
     *
     * @return int
     */
    public function getCustomerId()
    {
        return $this->customer_id;
    }

    /**
     * Get the [parent_id] column value.
     *
     * @return int
     */
    public function getParentId()
    {
        return $this->parent_id;
    }

    /**
     * Get the [type_id] column value.
     *
     * @return int
     */
    public function getTypeId()
    {
        return $this->type_id;
    }

    /**
     * Get the [status_id] column value.
     *
     * @return int
     */
    public function getStatusId()
    {
        return $this->status_id;
    }

    /**
     * Get the [currency_id] column value.
     *
     * @return int
     */
    public function getCurrencyId()
    {
        return $this->currency_id;
    }

    /**
     * Get the [currency_rate] column value.
     *
     * @return double
     */
    public function getCurrencyRate()
    {
        return $this->currency_rate;
    }

    /**
     * Get the [total_price] column value.
     *
     * @return string
     */
    public function getTotalPrice()
    {
        return $this->total_price;
    }

    /**
     * Get the [total_price_with_tax] column value.
     *
     * @return string
     */
    public function getTotalPriceWithTax()
    {
        return $this->total_price_with_tax;
    }

    /**
     * Get the [discount_without_tax] column value.
     *
     * @return string
     */
    public function getDiscountWithoutTax()
    {
        return $this->discount_without_tax;
    }

    /**
     * Get the [discount_with_tax] column value.
     *
     * @return string
     */
    public function getDiscountWithTax()
    {
        return $this->discount_with_tax;
    }

    /**
     * Get the [allow_partial_use] column value.
     *
     * @return boolean
     */
    public function getAllowPartialUse()
    {
        return $this->allow_partial_use;
    }

    /**
     * Get the [allow_partial_use] column value.
     *
     * @return boolean
     */
    public function isAllowPartialUse()
    {
        return $this->getAllowPartialUse();
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
     * Get the [version] column value.
     *
     * @return int
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * Get the [optionally formatted] temporal [version_created_at] column value.
     *
     *
     * @param      string|null $format The date/time format string (either date()-style or strftime()-style).
     *                            If format is NULL, then the raw DateTime object will be returned.
     *
     * @return string|DateTime Formatted date/time value as string or DateTime object (if format is NULL), NULL if column is NULL, and 0 if column value is 0000-00-00 00:00:00
     *
     * @throws PropelException - if unable to parse/validate the date/time value.
     */
    public function getVersionCreatedAt($format = NULL)
    {
        if ($format === null) {
            return $this->version_created_at;
        } else {
            return $this->version_created_at instanceof \DateTimeInterface ? $this->version_created_at->format($format) : null;
        }
    }

    /**
     * Get the [version_created_by] column value.
     *
     * @return string
     */
    public function getVersionCreatedBy()
    {
        return $this->version_created_by;
    }

    /**
     * Set the value of [id] column.
     *
     * @param int $v new value
     * @return $this|\CreditNote\Model\CreditNote The current object (for fluent API support)
     */
    public function setId($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->id !== $v) {
            $this->id = $v;
            $this->modifiedColumns[CreditNoteTableMap::COL_ID] = true;
        }

        return $this;
    } // setId()

    /**
     * Set the value of [ref] column.
     *
     * @param string $v new value
     * @return $this|\CreditNote\Model\CreditNote The current object (for fluent API support)
     */
    public function setRef($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->ref !== $v) {
            $this->ref = $v;
            $this->modifiedColumns[CreditNoteTableMap::COL_REF] = true;
        }

        return $this;
    } // setRef()

    /**
     * Set the value of [invoice_ref] column.
     *
     * @param string $v new value
     * @return $this|\CreditNote\Model\CreditNote The current object (for fluent API support)
     */
    public function setInvoiceRef($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->invoice_ref !== $v) {
            $this->invoice_ref = $v;
            $this->modifiedColumns[CreditNoteTableMap::COL_INVOICE_REF] = true;
        }

        return $this;
    } // setInvoiceRef()

    /**
     * Set the value of [invoice_address_id] column.
     *
     * @param int $v new value
     * @return $this|\CreditNote\Model\CreditNote The current object (for fluent API support)
     */
    public function setInvoiceAddressId($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->invoice_address_id !== $v) {
            $this->invoice_address_id = $v;
            $this->modifiedColumns[CreditNoteTableMap::COL_INVOICE_ADDRESS_ID] = true;
        }

        if ($this->aCreditNoteAddress !== null && $this->aCreditNoteAddress->getId() !== $v) {
            $this->aCreditNoteAddress = null;
        }

        return $this;
    } // setInvoiceAddressId()

    /**
     * Sets the value of [invoice_date] column to a normalized version of the date/time value specified.
     *
     * @param  mixed $v string, integer (timestamp), or \DateTimeInterface value.
     *               Empty strings are treated as NULL.
     * @return $this|\CreditNote\Model\CreditNote The current object (for fluent API support)
     */
    public function setInvoiceDate($v)
    {
        $dt = PropelDateTime::newInstance($v, null, 'DateTime');
        if ($this->invoice_date !== null || $dt !== null) {
            if ($this->invoice_date === null || $dt === null || $dt->format("Y-m-d H:i:s.u") !== $this->invoice_date->format("Y-m-d H:i:s.u")) {
                $this->invoice_date = $dt === null ? null : clone $dt;
                $this->modifiedColumns[CreditNoteTableMap::COL_INVOICE_DATE] = true;
            }
        } // if either are not null

        return $this;
    } // setInvoiceDate()

    /**
     * Set the value of [order_id] column.
     *
     * @param int $v new value
     * @return $this|\CreditNote\Model\CreditNote The current object (for fluent API support)
     */
    public function setOrderId($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->order_id !== $v) {
            $this->order_id = $v;
            $this->modifiedColumns[CreditNoteTableMap::COL_ORDER_ID] = true;
        }

        if ($this->aOrder !== null && $this->aOrder->getId() !== $v) {
            $this->aOrder = null;
        }

        return $this;
    } // setOrderId()

    /**
     * Set the value of [customer_id] column.
     *
     * @param int $v new value
     * @return $this|\CreditNote\Model\CreditNote The current object (for fluent API support)
     */
    public function setCustomerId($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->customer_id !== $v) {
            $this->customer_id = $v;
            $this->modifiedColumns[CreditNoteTableMap::COL_CUSTOMER_ID] = true;
        }

        if ($this->aCustomer !== null && $this->aCustomer->getId() !== $v) {
            $this->aCustomer = null;
        }

        return $this;
    } // setCustomerId()

    /**
     * Set the value of [parent_id] column.
     *
     * @param int $v new value
     * @return $this|\CreditNote\Model\CreditNote The current object (for fluent API support)
     */
    public function setParentId($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->parent_id !== $v) {
            $this->parent_id = $v;
            $this->modifiedColumns[CreditNoteTableMap::COL_PARENT_ID] = true;
        }

        if ($this->aCreditNoteRelatedByParentId !== null && $this->aCreditNoteRelatedByParentId->getId() !== $v) {
            $this->aCreditNoteRelatedByParentId = null;
        }

        return $this;
    } // setParentId()

    /**
     * Set the value of [type_id] column.
     *
     * @param int $v new value
     * @return $this|\CreditNote\Model\CreditNote The current object (for fluent API support)
     */
    public function setTypeId($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->type_id !== $v) {
            $this->type_id = $v;
            $this->modifiedColumns[CreditNoteTableMap::COL_TYPE_ID] = true;
        }

        if ($this->aCreditNoteType !== null && $this->aCreditNoteType->getId() !== $v) {
            $this->aCreditNoteType = null;
        }

        return $this;
    } // setTypeId()

    /**
     * Set the value of [status_id] column.
     *
     * @param int $v new value
     * @return $this|\CreditNote\Model\CreditNote The current object (for fluent API support)
     */
    public function setStatusId($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->status_id !== $v) {
            $this->status_id = $v;
            $this->modifiedColumns[CreditNoteTableMap::COL_STATUS_ID] = true;
        }

        if ($this->aCreditNoteStatus !== null && $this->aCreditNoteStatus->getId() !== $v) {
            $this->aCreditNoteStatus = null;
        }

        return $this;
    } // setStatusId()

    /**
     * Set the value of [currency_id] column.
     *
     * @param int $v new value
     * @return $this|\CreditNote\Model\CreditNote The current object (for fluent API support)
     */
    public function setCurrencyId($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->currency_id !== $v) {
            $this->currency_id = $v;
            $this->modifiedColumns[CreditNoteTableMap::COL_CURRENCY_ID] = true;
        }

        if ($this->aCurrency !== null && $this->aCurrency->getId() !== $v) {
            $this->aCurrency = null;
        }

        return $this;
    } // setCurrencyId()

    /**
     * Set the value of [currency_rate] column.
     *
     * @param double $v new value
     * @return $this|\CreditNote\Model\CreditNote The current object (for fluent API support)
     */
    public function setCurrencyRate($v)
    {
        if ($v !== null) {
            $v = (double) $v;
        }

        if ($this->currency_rate !== $v) {
            $this->currency_rate = $v;
            $this->modifiedColumns[CreditNoteTableMap::COL_CURRENCY_RATE] = true;
        }

        return $this;
    } // setCurrencyRate()

    /**
     * Set the value of [total_price] column.
     *
     * @param string $v new value
     * @return $this|\CreditNote\Model\CreditNote The current object (for fluent API support)
     */
    public function setTotalPrice($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->total_price !== $v) {
            $this->total_price = $v;
            $this->modifiedColumns[CreditNoteTableMap::COL_TOTAL_PRICE] = true;
        }

        return $this;
    } // setTotalPrice()

    /**
     * Set the value of [total_price_with_tax] column.
     *
     * @param string $v new value
     * @return $this|\CreditNote\Model\CreditNote The current object (for fluent API support)
     */
    public function setTotalPriceWithTax($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->total_price_with_tax !== $v) {
            $this->total_price_with_tax = $v;
            $this->modifiedColumns[CreditNoteTableMap::COL_TOTAL_PRICE_WITH_TAX] = true;
        }

        return $this;
    } // setTotalPriceWithTax()

    /**
     * Set the value of [discount_without_tax] column.
     *
     * @param string $v new value
     * @return $this|\CreditNote\Model\CreditNote The current object (for fluent API support)
     */
    public function setDiscountWithoutTax($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->discount_without_tax !== $v) {
            $this->discount_without_tax = $v;
            $this->modifiedColumns[CreditNoteTableMap::COL_DISCOUNT_WITHOUT_TAX] = true;
        }

        return $this;
    } // setDiscountWithoutTax()

    /**
     * Set the value of [discount_with_tax] column.
     *
     * @param string $v new value
     * @return $this|\CreditNote\Model\CreditNote The current object (for fluent API support)
     */
    public function setDiscountWithTax($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->discount_with_tax !== $v) {
            $this->discount_with_tax = $v;
            $this->modifiedColumns[CreditNoteTableMap::COL_DISCOUNT_WITH_TAX] = true;
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
     * @param  boolean|integer|string $v The new value
     * @return $this|\CreditNote\Model\CreditNote The current object (for fluent API support)
     */
    public function setAllowPartialUse($v)
    {
        if ($v !== null) {
            if (\is_string($v)) {
                $v = \in_array(strtolower($v), array('false', 'off', '-', 'no', 'n', '0', '')) ? false : true;
            } else {
                $v = (boolean) $v;
            }
        }

        if ($this->allow_partial_use !== $v) {
            $this->allow_partial_use = $v;
            $this->modifiedColumns[CreditNoteTableMap::COL_ALLOW_PARTIAL_USE] = true;
        }

        return $this;
    } // setAllowPartialUse()

    /**
     * Sets the value of [created_at] column to a normalized version of the date/time value specified.
     *
     * @param  mixed $v string, integer (timestamp), or \DateTimeInterface value.
     *               Empty strings are treated as NULL.
     * @return $this|\CreditNote\Model\CreditNote The current object (for fluent API support)
     */
    public function setCreatedAt($v)
    {
        $dt = PropelDateTime::newInstance($v, null, 'DateTime');
        if ($this->created_at !== null || $dt !== null) {
            if ($this->created_at === null || $dt === null || $dt->format("Y-m-d H:i:s.u") !== $this->created_at->format("Y-m-d H:i:s.u")) {
                $this->created_at = $dt === null ? null : clone $dt;
                $this->modifiedColumns[CreditNoteTableMap::COL_CREATED_AT] = true;
            }
        } // if either are not null

        return $this;
    } // setCreatedAt()

    /**
     * Sets the value of [updated_at] column to a normalized version of the date/time value specified.
     *
     * @param  mixed $v string, integer (timestamp), or \DateTimeInterface value.
     *               Empty strings are treated as NULL.
     * @return $this|\CreditNote\Model\CreditNote The current object (for fluent API support)
     */
    public function setUpdatedAt($v)
    {
        $dt = PropelDateTime::newInstance($v, null, 'DateTime');
        if ($this->updated_at !== null || $dt !== null) {
            if ($this->updated_at === null || $dt === null || $dt->format("Y-m-d H:i:s.u") !== $this->updated_at->format("Y-m-d H:i:s.u")) {
                $this->updated_at = $dt === null ? null : clone $dt;
                $this->modifiedColumns[CreditNoteTableMap::COL_UPDATED_AT] = true;
            }
        } // if either are not null

        return $this;
    } // setUpdatedAt()

    /**
     * Set the value of [version] column.
     *
     * @param int $v new value
     * @return $this|\CreditNote\Model\CreditNote The current object (for fluent API support)
     */
    public function setVersion($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->version !== $v) {
            $this->version = $v;
            $this->modifiedColumns[CreditNoteTableMap::COL_VERSION] = true;
        }

        return $this;
    } // setVersion()

    /**
     * Sets the value of [version_created_at] column to a normalized version of the date/time value specified.
     *
     * @param  mixed $v string, integer (timestamp), or \DateTimeInterface value.
     *               Empty strings are treated as NULL.
     * @return $this|\CreditNote\Model\CreditNote The current object (for fluent API support)
     */
    public function setVersionCreatedAt($v)
    {
        $dt = PropelDateTime::newInstance($v, null, 'DateTime');
        if ($this->version_created_at !== null || $dt !== null) {
            if ($this->version_created_at === null || $dt === null || $dt->format("Y-m-d H:i:s.u") !== $this->version_created_at->format("Y-m-d H:i:s.u")) {
                $this->version_created_at = $dt === null ? null : clone $dt;
                $this->modifiedColumns[CreditNoteTableMap::COL_VERSION_CREATED_AT] = true;
            }
        } // if either are not null

        return $this;
    } // setVersionCreatedAt()

    /**
     * Set the value of [version_created_by] column.
     *
     * @param string $v new value
     * @return $this|\CreditNote\Model\CreditNote The current object (for fluent API support)
     */
    public function setVersionCreatedBy($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->version_created_by !== $v) {
            $this->version_created_by = $v;
            $this->modifiedColumns[CreditNoteTableMap::COL_VERSION_CREATED_BY] = true;
        }

        return $this;
    } // setVersionCreatedBy()

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

            $col = $row[TableMap::TYPE_NUM == $indexType ? 0 + $startcol : CreditNoteTableMap::translateFieldName('Id', TableMap::TYPE_PHPNAME, $indexType)];
            $this->id = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 1 + $startcol : CreditNoteTableMap::translateFieldName('Ref', TableMap::TYPE_PHPNAME, $indexType)];
            $this->ref = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 2 + $startcol : CreditNoteTableMap::translateFieldName('InvoiceRef', TableMap::TYPE_PHPNAME, $indexType)];
            $this->invoice_ref = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 3 + $startcol : CreditNoteTableMap::translateFieldName('InvoiceAddressId', TableMap::TYPE_PHPNAME, $indexType)];
            $this->invoice_address_id = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 4 + $startcol : CreditNoteTableMap::translateFieldName('InvoiceDate', TableMap::TYPE_PHPNAME, $indexType)];
            if ($col === '0000-00-00 00:00:00') {
                $col = null;
            }
            $this->invoice_date = (null !== $col) ? PropelDateTime::newInstance($col, null, 'DateTime') : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 5 + $startcol : CreditNoteTableMap::translateFieldName('OrderId', TableMap::TYPE_PHPNAME, $indexType)];
            $this->order_id = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 6 + $startcol : CreditNoteTableMap::translateFieldName('CustomerId', TableMap::TYPE_PHPNAME, $indexType)];
            $this->customer_id = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 7 + $startcol : CreditNoteTableMap::translateFieldName('ParentId', TableMap::TYPE_PHPNAME, $indexType)];
            $this->parent_id = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 8 + $startcol : CreditNoteTableMap::translateFieldName('TypeId', TableMap::TYPE_PHPNAME, $indexType)];
            $this->type_id = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 9 + $startcol : CreditNoteTableMap::translateFieldName('StatusId', TableMap::TYPE_PHPNAME, $indexType)];
            $this->status_id = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 10 + $startcol : CreditNoteTableMap::translateFieldName('CurrencyId', TableMap::TYPE_PHPNAME, $indexType)];
            $this->currency_id = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 11 + $startcol : CreditNoteTableMap::translateFieldName('CurrencyRate', TableMap::TYPE_PHPNAME, $indexType)];
            $this->currency_rate = (null !== $col) ? (double) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 12 + $startcol : CreditNoteTableMap::translateFieldName('TotalPrice', TableMap::TYPE_PHPNAME, $indexType)];
            $this->total_price = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 13 + $startcol : CreditNoteTableMap::translateFieldName('TotalPriceWithTax', TableMap::TYPE_PHPNAME, $indexType)];
            $this->total_price_with_tax = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 14 + $startcol : CreditNoteTableMap::translateFieldName('DiscountWithoutTax', TableMap::TYPE_PHPNAME, $indexType)];
            $this->discount_without_tax = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 15 + $startcol : CreditNoteTableMap::translateFieldName('DiscountWithTax', TableMap::TYPE_PHPNAME, $indexType)];
            $this->discount_with_tax = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 16 + $startcol : CreditNoteTableMap::translateFieldName('AllowPartialUse', TableMap::TYPE_PHPNAME, $indexType)];
            $this->allow_partial_use = (null !== $col) ? (boolean) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 17 + $startcol : CreditNoteTableMap::translateFieldName('CreatedAt', TableMap::TYPE_PHPNAME, $indexType)];
            if ($col === '0000-00-00 00:00:00') {
                $col = null;
            }
            $this->created_at = (null !== $col) ? PropelDateTime::newInstance($col, null, 'DateTime') : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 18 + $startcol : CreditNoteTableMap::translateFieldName('UpdatedAt', TableMap::TYPE_PHPNAME, $indexType)];
            if ($col === '0000-00-00 00:00:00') {
                $col = null;
            }
            $this->updated_at = (null !== $col) ? PropelDateTime::newInstance($col, null, 'DateTime') : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 19 + $startcol : CreditNoteTableMap::translateFieldName('Version', TableMap::TYPE_PHPNAME, $indexType)];
            $this->version = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 20 + $startcol : CreditNoteTableMap::translateFieldName('VersionCreatedAt', TableMap::TYPE_PHPNAME, $indexType)];
            if ($col === '0000-00-00 00:00:00') {
                $col = null;
            }
            $this->version_created_at = (null !== $col) ? PropelDateTime::newInstance($col, null, 'DateTime') : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 21 + $startcol : CreditNoteTableMap::translateFieldName('VersionCreatedBy', TableMap::TYPE_PHPNAME, $indexType)];
            $this->version_created_by = (null !== $col) ? (string) $col : null;
            $this->resetModified();

            $this->setNew(false);

            if ($rehydrate) {
                $this->ensureConsistency();
            }

            return $startcol + 22; // 22 = CreditNoteTableMap::NUM_HYDRATE_COLUMNS.

        } catch (Exception $e) {
            throw new PropelException(sprintf('Error populating %s object', '\\CreditNote\\Model\\CreditNote'), 0, $e);
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
        if ($this->aCreditNoteAddress !== null && $this->invoice_address_id !== $this->aCreditNoteAddress->getId()) {
            $this->aCreditNoteAddress = null;
        }
        if ($this->aOrder !== null && $this->order_id !== $this->aOrder->getId()) {
            $this->aOrder = null;
        }
        if ($this->aCustomer !== null && $this->customer_id !== $this->aCustomer->getId()) {
            $this->aCustomer = null;
        }
        if ($this->aCreditNoteRelatedByParentId !== null && $this->parent_id !== $this->aCreditNoteRelatedByParentId->getId()) {
            $this->aCreditNoteRelatedByParentId = null;
        }
        if ($this->aCreditNoteType !== null && $this->type_id !== $this->aCreditNoteType->getId()) {
            $this->aCreditNoteType = null;
        }
        if ($this->aCreditNoteStatus !== null && $this->status_id !== $this->aCreditNoteStatus->getId()) {
            $this->aCreditNoteStatus = null;
        }
        if ($this->aCurrency !== null && $this->currency_id !== $this->aCurrency->getId()) {
            $this->aCurrency = null;
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
            $con = Propel::getServiceContainer()->getReadConnection(CreditNoteTableMap::DATABASE_NAME);
        }

        // We don't need to alter the object instance pool; we're just modifying this instance
        // already in the pool.

        $dataFetcher = ChildCreditNoteQuery::create(null, $this->buildPkeyCriteria())->setFormatter(ModelCriteria::FORMAT_STATEMENT)->find($con);
        $row = $dataFetcher->fetch();
        $dataFetcher->close();
        if (!$row) {
            throw new PropelException('Cannot find matching row in the database to reload object values.');
        }
        $this->hydrate($row, 0, true, $dataFetcher->getIndexType()); // rehydrate

        if ($deep) {  // also de-associate any related objects?

            $this->aOrder = null;
            $this->aCustomer = null;
            $this->aCreditNoteRelatedByParentId = null;
            $this->aCreditNoteType = null;
            $this->aCreditNoteStatus = null;
            $this->aCurrency = null;
            $this->aCreditNoteAddress = null;
            $this->collCreditNotesRelatedById = null;

            $this->collOrderCreditNotes = null;

            $this->collCartCreditNotes = null;

            $this->collCreditNoteDetails = null;

            $this->collCreditNoteComments = null;

            $this->collCreditNoteVersions = null;

        } // if (deep)
    }

    /**
     * Removes this object from datastore and sets delete attribute.
     *
     * @param      ConnectionInterface $con
     * @return void
     * @throws PropelException
     * @see CreditNote::setDeleted()
     * @see CreditNote::isDeleted()
     */
    public function delete(ConnectionInterface $con = null)
    {
        if ($this->isDeleted()) {
            throw new PropelException("This object has already been deleted.");
        }

        if ($con === null) {
            $con = Propel::getServiceContainer()->getWriteConnection(CreditNoteTableMap::DATABASE_NAME);
        }

        $con->transaction(function () use ($con) {
            $deleteQuery = ChildCreditNoteQuery::create()
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
            $con = Propel::getServiceContainer()->getWriteConnection(CreditNoteTableMap::DATABASE_NAME);
        }

        return $con->transaction(function () use ($con) {
            $ret = $this->preSave($con);
            $isInsert = $this->isNew();
            // versionable behavior
            if ($this->isVersioningNecessary()) {
                $this->setVersion($this->isNew() ? 1 : $this->getLastVersionNumber($con) + 1);
                if (!$this->isColumnModified(CreditNoteTableMap::COL_VERSION_CREATED_AT)) {
                    $this->setVersionCreatedAt(time());
                }
                $createVersion = true; // for postSave hook
            }
            if ($isInsert) {
                $ret = $ret && $this->preInsert($con);
                // timestampable behavior
                $time = time();
                $highPrecision = \Propel\Runtime\Util\PropelDateTime::createHighPrecision();
                if (!$this->isColumnModified(CreditNoteTableMap::COL_CREATED_AT)) {
                    $this->setCreatedAt($highPrecision);
                }
                if (!$this->isColumnModified(CreditNoteTableMap::COL_UPDATED_AT)) {
                    $this->setUpdatedAt($highPrecision);
                }
            } else {
                $ret = $ret && $this->preUpdate($con);
                // timestampable behavior
                if ($this->isModified() && !$this->isColumnModified(CreditNoteTableMap::COL_UPDATED_AT)) {
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
                // versionable behavior
                if (isset($createVersion)) {
                    $this->addVersion($con);
                }
                CreditNoteTableMap::addInstanceToPool($this);
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

            if ($this->aOrder !== null) {
                if ($this->aOrder->isModified() || $this->aOrder->isNew()) {
                    $affectedRows += $this->aOrder->save($con);
                }
                $this->setOrder($this->aOrder);
            }

            if ($this->aCustomer !== null) {
                if ($this->aCustomer->isModified() || $this->aCustomer->isNew()) {
                    $affectedRows += $this->aCustomer->save($con);
                }
                $this->setCustomer($this->aCustomer);
            }

            if ($this->aCreditNoteRelatedByParentId !== null) {
                if ($this->aCreditNoteRelatedByParentId->isModified() || $this->aCreditNoteRelatedByParentId->isNew()) {
                    $affectedRows += $this->aCreditNoteRelatedByParentId->save($con);
                }
                $this->setCreditNoteRelatedByParentId($this->aCreditNoteRelatedByParentId);
            }

            if ($this->aCreditNoteType !== null) {
                if ($this->aCreditNoteType->isModified() || $this->aCreditNoteType->isNew()) {
                    $affectedRows += $this->aCreditNoteType->save($con);
                }
                $this->setCreditNoteType($this->aCreditNoteType);
            }

            if ($this->aCreditNoteStatus !== null) {
                if ($this->aCreditNoteStatus->isModified() || $this->aCreditNoteStatus->isNew()) {
                    $affectedRows += $this->aCreditNoteStatus->save($con);
                }
                $this->setCreditNoteStatus($this->aCreditNoteStatus);
            }

            if ($this->aCurrency !== null) {
                if ($this->aCurrency->isModified() || $this->aCurrency->isNew()) {
                    $affectedRows += $this->aCurrency->save($con);
                }
                $this->setCurrency($this->aCurrency);
            }

            if ($this->aCreditNoteAddress !== null) {
                if ($this->aCreditNoteAddress->isModified() || $this->aCreditNoteAddress->isNew()) {
                    $affectedRows += $this->aCreditNoteAddress->save($con);
                }
                $this->setCreditNoteAddress($this->aCreditNoteAddress);
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

            if ($this->creditNotesRelatedByIdScheduledForDeletion !== null) {
                if (!$this->creditNotesRelatedByIdScheduledForDeletion->isEmpty()) {
                    foreach ($this->creditNotesRelatedByIdScheduledForDeletion as $creditNoteRelatedById) {
                        // need to save related object because we set the relation to null
                        $creditNoteRelatedById->save($con);
                    }
                    $this->creditNotesRelatedByIdScheduledForDeletion = null;
                }
            }

            if ($this->collCreditNotesRelatedById !== null) {
                foreach ($this->collCreditNotesRelatedById as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->orderCreditNotesScheduledForDeletion !== null) {
                if (!$this->orderCreditNotesScheduledForDeletion->isEmpty()) {
                    \CreditNote\Model\OrderCreditNoteQuery::create()
                        ->filterByPrimaryKeys($this->orderCreditNotesScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->orderCreditNotesScheduledForDeletion = null;
                }
            }

            if ($this->collOrderCreditNotes !== null) {
                foreach ($this->collOrderCreditNotes as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->cartCreditNotesScheduledForDeletion !== null) {
                if (!$this->cartCreditNotesScheduledForDeletion->isEmpty()) {
                    \CreditNote\Model\CartCreditNoteQuery::create()
                        ->filterByPrimaryKeys($this->cartCreditNotesScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->cartCreditNotesScheduledForDeletion = null;
                }
            }

            if ($this->collCartCreditNotes !== null) {
                foreach ($this->collCartCreditNotes as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->creditNoteDetailsScheduledForDeletion !== null) {
                if (!$this->creditNoteDetailsScheduledForDeletion->isEmpty()) {
                    \CreditNote\Model\CreditNoteDetailQuery::create()
                        ->filterByPrimaryKeys($this->creditNoteDetailsScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->creditNoteDetailsScheduledForDeletion = null;
                }
            }

            if ($this->collCreditNoteDetails !== null) {
                foreach ($this->collCreditNoteDetails as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->creditNoteCommentsScheduledForDeletion !== null) {
                if (!$this->creditNoteCommentsScheduledForDeletion->isEmpty()) {
                    \CreditNote\Model\CreditNoteCommentQuery::create()
                        ->filterByPrimaryKeys($this->creditNoteCommentsScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->creditNoteCommentsScheduledForDeletion = null;
                }
            }

            if ($this->collCreditNoteComments !== null) {
                foreach ($this->collCreditNoteComments as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->creditNoteVersionsScheduledForDeletion !== null) {
                if (!$this->creditNoteVersionsScheduledForDeletion->isEmpty()) {
                    \CreditNote\Model\CreditNoteVersionQuery::create()
                        ->filterByPrimaryKeys($this->creditNoteVersionsScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->creditNoteVersionsScheduledForDeletion = null;
                }
            }

            if ($this->collCreditNoteVersions !== null) {
                foreach ($this->collCreditNoteVersions as $referrerFK) {
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

        $this->modifiedColumns[CreditNoteTableMap::COL_ID] = true;
        if (null !== $this->id) {
            throw new PropelException('Cannot insert a value for auto-increment primary key (' . CreditNoteTableMap::COL_ID . ')');
        }

         // check the columns in natural order for more readable SQL queries
        if ($this->isColumnModified(CreditNoteTableMap::COL_ID)) {
            $modifiedColumns[':p' . $index++]  = '`id`';
        }
        if ($this->isColumnModified(CreditNoteTableMap::COL_REF)) {
            $modifiedColumns[':p' . $index++]  = '`ref`';
        }
        if ($this->isColumnModified(CreditNoteTableMap::COL_INVOICE_REF)) {
            $modifiedColumns[':p' . $index++]  = '`invoice_ref`';
        }
        if ($this->isColumnModified(CreditNoteTableMap::COL_INVOICE_ADDRESS_ID)) {
            $modifiedColumns[':p' . $index++]  = '`invoice_address_id`';
        }
        if ($this->isColumnModified(CreditNoteTableMap::COL_INVOICE_DATE)) {
            $modifiedColumns[':p' . $index++]  = '`invoice_date`';
        }
        if ($this->isColumnModified(CreditNoteTableMap::COL_ORDER_ID)) {
            $modifiedColumns[':p' . $index++]  = '`order_id`';
        }
        if ($this->isColumnModified(CreditNoteTableMap::COL_CUSTOMER_ID)) {
            $modifiedColumns[':p' . $index++]  = '`customer_id`';
        }
        if ($this->isColumnModified(CreditNoteTableMap::COL_PARENT_ID)) {
            $modifiedColumns[':p' . $index++]  = '`parent_id`';
        }
        if ($this->isColumnModified(CreditNoteTableMap::COL_TYPE_ID)) {
            $modifiedColumns[':p' . $index++]  = '`type_id`';
        }
        if ($this->isColumnModified(CreditNoteTableMap::COL_STATUS_ID)) {
            $modifiedColumns[':p' . $index++]  = '`status_id`';
        }
        if ($this->isColumnModified(CreditNoteTableMap::COL_CURRENCY_ID)) {
            $modifiedColumns[':p' . $index++]  = '`currency_id`';
        }
        if ($this->isColumnModified(CreditNoteTableMap::COL_CURRENCY_RATE)) {
            $modifiedColumns[':p' . $index++]  = '`currency_rate`';
        }
        if ($this->isColumnModified(CreditNoteTableMap::COL_TOTAL_PRICE)) {
            $modifiedColumns[':p' . $index++]  = '`total_price`';
        }
        if ($this->isColumnModified(CreditNoteTableMap::COL_TOTAL_PRICE_WITH_TAX)) {
            $modifiedColumns[':p' . $index++]  = '`total_price_with_tax`';
        }
        if ($this->isColumnModified(CreditNoteTableMap::COL_DISCOUNT_WITHOUT_TAX)) {
            $modifiedColumns[':p' . $index++]  = '`discount_without_tax`';
        }
        if ($this->isColumnModified(CreditNoteTableMap::COL_DISCOUNT_WITH_TAX)) {
            $modifiedColumns[':p' . $index++]  = '`discount_with_tax`';
        }
        if ($this->isColumnModified(CreditNoteTableMap::COL_ALLOW_PARTIAL_USE)) {
            $modifiedColumns[':p' . $index++]  = '`allow_partial_use`';
        }
        if ($this->isColumnModified(CreditNoteTableMap::COL_CREATED_AT)) {
            $modifiedColumns[':p' . $index++]  = '`created_at`';
        }
        if ($this->isColumnModified(CreditNoteTableMap::COL_UPDATED_AT)) {
            $modifiedColumns[':p' . $index++]  = '`updated_at`';
        }
        if ($this->isColumnModified(CreditNoteTableMap::COL_VERSION)) {
            $modifiedColumns[':p' . $index++]  = '`version`';
        }
        if ($this->isColumnModified(CreditNoteTableMap::COL_VERSION_CREATED_AT)) {
            $modifiedColumns[':p' . $index++]  = '`version_created_at`';
        }
        if ($this->isColumnModified(CreditNoteTableMap::COL_VERSION_CREATED_BY)) {
            $modifiedColumns[':p' . $index++]  = '`version_created_by`';
        }

        $sql = sprintf(
            'INSERT INTO `credit_note` (%s) VALUES (%s)',
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
                    case '`ref`':
                        $stmt->bindValue($identifier, $this->ref, PDO::PARAM_STR);
                        break;
                    case '`invoice_ref`':
                        $stmt->bindValue($identifier, $this->invoice_ref, PDO::PARAM_STR);
                        break;
                    case '`invoice_address_id`':
                        $stmt->bindValue($identifier, $this->invoice_address_id, PDO::PARAM_INT);
                        break;
                    case '`invoice_date`':
                        $stmt->bindValue($identifier, $this->invoice_date ? $this->invoice_date->format("Y-m-d H:i:s.u") : null, PDO::PARAM_STR);
                        break;
                    case '`order_id`':
                        $stmt->bindValue($identifier, $this->order_id, PDO::PARAM_INT);
                        break;
                    case '`customer_id`':
                        $stmt->bindValue($identifier, $this->customer_id, PDO::PARAM_INT);
                        break;
                    case '`parent_id`':
                        $stmt->bindValue($identifier, $this->parent_id, PDO::PARAM_INT);
                        break;
                    case '`type_id`':
                        $stmt->bindValue($identifier, $this->type_id, PDO::PARAM_INT);
                        break;
                    case '`status_id`':
                        $stmt->bindValue($identifier, $this->status_id, PDO::PARAM_INT);
                        break;
                    case '`currency_id`':
                        $stmt->bindValue($identifier, $this->currency_id, PDO::PARAM_INT);
                        break;
                    case '`currency_rate`':
                        $stmt->bindValue($identifier, $this->currency_rate, PDO::PARAM_STR);
                        break;
                    case '`total_price`':
                        $stmt->bindValue($identifier, $this->total_price, PDO::PARAM_STR);
                        break;
                    case '`total_price_with_tax`':
                        $stmt->bindValue($identifier, $this->total_price_with_tax, PDO::PARAM_STR);
                        break;
                    case '`discount_without_tax`':
                        $stmt->bindValue($identifier, $this->discount_without_tax, PDO::PARAM_STR);
                        break;
                    case '`discount_with_tax`':
                        $stmt->bindValue($identifier, $this->discount_with_tax, PDO::PARAM_STR);
                        break;
                    case '`allow_partial_use`':
                        $stmt->bindValue($identifier, (int) $this->allow_partial_use, PDO::PARAM_INT);
                        break;
                    case '`created_at`':
                        $stmt->bindValue($identifier, $this->created_at ? $this->created_at->format("Y-m-d H:i:s.u") : null, PDO::PARAM_STR);
                        break;
                    case '`updated_at`':
                        $stmt->bindValue($identifier, $this->updated_at ? $this->updated_at->format("Y-m-d H:i:s.u") : null, PDO::PARAM_STR);
                        break;
                    case '`version`':
                        $stmt->bindValue($identifier, $this->version, PDO::PARAM_INT);
                        break;
                    case '`version_created_at`':
                        $stmt->bindValue($identifier, $this->version_created_at ? $this->version_created_at->format("Y-m-d H:i:s.u") : null, PDO::PARAM_STR);
                        break;
                    case '`version_created_by`':
                        $stmt->bindValue($identifier, $this->version_created_by, PDO::PARAM_STR);
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
        $pos = CreditNoteTableMap::translateFieldName($name, $type, TableMap::TYPE_NUM);
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

        if (isset($alreadyDumpedObjects['CreditNote'][$this->hashCode()])) {
            return '*RECURSION*';
        }
        $alreadyDumpedObjects['CreditNote'][$this->hashCode()] = true;
        $keys = CreditNoteTableMap::getFieldNames($keyType);
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
        );
        if ($result[$keys[4]] instanceof \DateTimeInterface) {
            $result[$keys[4]] = $result[$keys[4]]->format('c');
        }

        if ($result[$keys[17]] instanceof \DateTimeInterface) {
            $result[$keys[17]] = $result[$keys[17]]->format('c');
        }

        if ($result[$keys[18]] instanceof \DateTimeInterface) {
            $result[$keys[18]] = $result[$keys[18]]->format('c');
        }

        if ($result[$keys[20]] instanceof \DateTimeInterface) {
            $result[$keys[20]] = $result[$keys[20]]->format('c');
        }

        $virtualColumns = $this->virtualColumns;
        foreach ($virtualColumns as $key => $virtualColumn) {
            $result[$key] = $virtualColumn;
        }

        if ($includeForeignObjects) {
            if (null !== $this->aOrder) {

                switch ($keyType) {
                    case TableMap::TYPE_CAMELNAME:
                        $key = 'order';
                        break;
                    case TableMap::TYPE_FIELDNAME:
                        $key = 'order';
                        break;
                    default:
                        $key = 'Order';
                }

                $result[$key] = $this->aOrder->toArray($keyType, $includeLazyLoadColumns,  $alreadyDumpedObjects, true);
            }
            if (null !== $this->aCustomer) {

                switch ($keyType) {
                    case TableMap::TYPE_CAMELNAME:
                        $key = 'customer';
                        break;
                    case TableMap::TYPE_FIELDNAME:
                        $key = 'customer';
                        break;
                    default:
                        $key = 'Customer';
                }

                $result[$key] = $this->aCustomer->toArray($keyType, $includeLazyLoadColumns,  $alreadyDumpedObjects, true);
            }
            if (null !== $this->aCreditNoteRelatedByParentId) {

                switch ($keyType) {
                    case TableMap::TYPE_CAMELNAME:
                        $key = 'creditNote';
                        break;
                    case TableMap::TYPE_FIELDNAME:
                        $key = 'credit_note';
                        break;
                    default:
                        $key = 'CreditNote';
                }

                $result[$key] = $this->aCreditNoteRelatedByParentId->toArray($keyType, $includeLazyLoadColumns,  $alreadyDumpedObjects, true);
            }
            if (null !== $this->aCreditNoteType) {

                switch ($keyType) {
                    case TableMap::TYPE_CAMELNAME:
                        $key = 'creditNoteType';
                        break;
                    case TableMap::TYPE_FIELDNAME:
                        $key = 'credit_note_type';
                        break;
                    default:
                        $key = 'CreditNoteType';
                }

                $result[$key] = $this->aCreditNoteType->toArray($keyType, $includeLazyLoadColumns,  $alreadyDumpedObjects, true);
            }
            if (null !== $this->aCreditNoteStatus) {

                switch ($keyType) {
                    case TableMap::TYPE_CAMELNAME:
                        $key = 'creditNoteStatus';
                        break;
                    case TableMap::TYPE_FIELDNAME:
                        $key = 'credit_note_status';
                        break;
                    default:
                        $key = 'CreditNoteStatus';
                }

                $result[$key] = $this->aCreditNoteStatus->toArray($keyType, $includeLazyLoadColumns,  $alreadyDumpedObjects, true);
            }
            if (null !== $this->aCurrency) {

                switch ($keyType) {
                    case TableMap::TYPE_CAMELNAME:
                        $key = 'currency';
                        break;
                    case TableMap::TYPE_FIELDNAME:
                        $key = 'currency';
                        break;
                    default:
                        $key = 'Currency';
                }

                $result[$key] = $this->aCurrency->toArray($keyType, $includeLazyLoadColumns,  $alreadyDumpedObjects, true);
            }
            if (null !== $this->aCreditNoteAddress) {

                switch ($keyType) {
                    case TableMap::TYPE_CAMELNAME:
                        $key = 'creditNoteAddress';
                        break;
                    case TableMap::TYPE_FIELDNAME:
                        $key = 'credit_note_address';
                        break;
                    default:
                        $key = 'CreditNoteAddress';
                }

                $result[$key] = $this->aCreditNoteAddress->toArray($keyType, $includeLazyLoadColumns,  $alreadyDumpedObjects, true);
            }
            if (null !== $this->collCreditNotesRelatedById) {

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

                $result[$key] = $this->collCreditNotesRelatedById->toArray(null, false, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collOrderCreditNotes) {

                switch ($keyType) {
                    case TableMap::TYPE_CAMELNAME:
                        $key = 'orderCreditNotes';
                        break;
                    case TableMap::TYPE_FIELDNAME:
                        $key = 'order_credit_notes';
                        break;
                    default:
                        $key = 'OrderCreditNotes';
                }

                $result[$key] = $this->collOrderCreditNotes->toArray(null, false, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collCartCreditNotes) {

                switch ($keyType) {
                    case TableMap::TYPE_CAMELNAME:
                        $key = 'cartCreditNotes';
                        break;
                    case TableMap::TYPE_FIELDNAME:
                        $key = 'cart_credit_notes';
                        break;
                    default:
                        $key = 'CartCreditNotes';
                }

                $result[$key] = $this->collCartCreditNotes->toArray(null, false, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collCreditNoteDetails) {

                switch ($keyType) {
                    case TableMap::TYPE_CAMELNAME:
                        $key = 'creditNoteDetails';
                        break;
                    case TableMap::TYPE_FIELDNAME:
                        $key = 'credit_note_details';
                        break;
                    default:
                        $key = 'CreditNoteDetails';
                }

                $result[$key] = $this->collCreditNoteDetails->toArray(null, false, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collCreditNoteComments) {

                switch ($keyType) {
                    case TableMap::TYPE_CAMELNAME:
                        $key = 'creditNoteComments';
                        break;
                    case TableMap::TYPE_FIELDNAME:
                        $key = 'credit_note_comments';
                        break;
                    default:
                        $key = 'CreditNoteComments';
                }

                $result[$key] = $this->collCreditNoteComments->toArray(null, false, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collCreditNoteVersions) {

                switch ($keyType) {
                    case TableMap::TYPE_CAMELNAME:
                        $key = 'creditNoteVersions';
                        break;
                    case TableMap::TYPE_FIELDNAME:
                        $key = 'credit_note_versions';
                        break;
                    default:
                        $key = 'CreditNoteVersions';
                }

                $result[$key] = $this->collCreditNoteVersions->toArray(null, false, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
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
     * @return $this|\CreditNote\Model\CreditNote
     */
    public function setByName($name, $value, $type = TableMap::TYPE_PHPNAME)
    {
        $pos = CreditNoteTableMap::translateFieldName($name, $type, TableMap::TYPE_NUM);

        return $this->setByPosition($pos, $value);
    }

    /**
     * Sets a field from the object by Position as specified in the xml schema.
     * Zero-based.
     *
     * @param  int $pos position in xml schema
     * @param  mixed $value field value
     * @return $this|\CreditNote\Model\CreditNote
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
        $keys = CreditNoteTableMap::getFieldNames($keyType);

        if (array_key_exists($keys[0], $arr)) {
            $this->setId($arr[$keys[0]]);
        }
        if (array_key_exists($keys[1], $arr)) {
            $this->setRef($arr[$keys[1]]);
        }
        if (array_key_exists($keys[2], $arr)) {
            $this->setInvoiceRef($arr[$keys[2]]);
        }
        if (array_key_exists($keys[3], $arr)) {
            $this->setInvoiceAddressId($arr[$keys[3]]);
        }
        if (array_key_exists($keys[4], $arr)) {
            $this->setInvoiceDate($arr[$keys[4]]);
        }
        if (array_key_exists($keys[5], $arr)) {
            $this->setOrderId($arr[$keys[5]]);
        }
        if (array_key_exists($keys[6], $arr)) {
            $this->setCustomerId($arr[$keys[6]]);
        }
        if (array_key_exists($keys[7], $arr)) {
            $this->setParentId($arr[$keys[7]]);
        }
        if (array_key_exists($keys[8], $arr)) {
            $this->setTypeId($arr[$keys[8]]);
        }
        if (array_key_exists($keys[9], $arr)) {
            $this->setStatusId($arr[$keys[9]]);
        }
        if (array_key_exists($keys[10], $arr)) {
            $this->setCurrencyId($arr[$keys[10]]);
        }
        if (array_key_exists($keys[11], $arr)) {
            $this->setCurrencyRate($arr[$keys[11]]);
        }
        if (array_key_exists($keys[12], $arr)) {
            $this->setTotalPrice($arr[$keys[12]]);
        }
        if (array_key_exists($keys[13], $arr)) {
            $this->setTotalPriceWithTax($arr[$keys[13]]);
        }
        if (array_key_exists($keys[14], $arr)) {
            $this->setDiscountWithoutTax($arr[$keys[14]]);
        }
        if (array_key_exists($keys[15], $arr)) {
            $this->setDiscountWithTax($arr[$keys[15]]);
        }
        if (array_key_exists($keys[16], $arr)) {
            $this->setAllowPartialUse($arr[$keys[16]]);
        }
        if (array_key_exists($keys[17], $arr)) {
            $this->setCreatedAt($arr[$keys[17]]);
        }
        if (array_key_exists($keys[18], $arr)) {
            $this->setUpdatedAt($arr[$keys[18]]);
        }
        if (array_key_exists($keys[19], $arr)) {
            $this->setVersion($arr[$keys[19]]);
        }
        if (array_key_exists($keys[20], $arr)) {
            $this->setVersionCreatedAt($arr[$keys[20]]);
        }
        if (array_key_exists($keys[21], $arr)) {
            $this->setVersionCreatedBy($arr[$keys[21]]);
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
     * @return $this|\CreditNote\Model\CreditNote The current object, for fluid interface
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
        $criteria = new Criteria(CreditNoteTableMap::DATABASE_NAME);

        if ($this->isColumnModified(CreditNoteTableMap::COL_ID)) {
            $criteria->add(CreditNoteTableMap::COL_ID, $this->id);
        }
        if ($this->isColumnModified(CreditNoteTableMap::COL_REF)) {
            $criteria->add(CreditNoteTableMap::COL_REF, $this->ref);
        }
        if ($this->isColumnModified(CreditNoteTableMap::COL_INVOICE_REF)) {
            $criteria->add(CreditNoteTableMap::COL_INVOICE_REF, $this->invoice_ref);
        }
        if ($this->isColumnModified(CreditNoteTableMap::COL_INVOICE_ADDRESS_ID)) {
            $criteria->add(CreditNoteTableMap::COL_INVOICE_ADDRESS_ID, $this->invoice_address_id);
        }
        if ($this->isColumnModified(CreditNoteTableMap::COL_INVOICE_DATE)) {
            $criteria->add(CreditNoteTableMap::COL_INVOICE_DATE, $this->invoice_date);
        }
        if ($this->isColumnModified(CreditNoteTableMap::COL_ORDER_ID)) {
            $criteria->add(CreditNoteTableMap::COL_ORDER_ID, $this->order_id);
        }
        if ($this->isColumnModified(CreditNoteTableMap::COL_CUSTOMER_ID)) {
            $criteria->add(CreditNoteTableMap::COL_CUSTOMER_ID, $this->customer_id);
        }
        if ($this->isColumnModified(CreditNoteTableMap::COL_PARENT_ID)) {
            $criteria->add(CreditNoteTableMap::COL_PARENT_ID, $this->parent_id);
        }
        if ($this->isColumnModified(CreditNoteTableMap::COL_TYPE_ID)) {
            $criteria->add(CreditNoteTableMap::COL_TYPE_ID, $this->type_id);
        }
        if ($this->isColumnModified(CreditNoteTableMap::COL_STATUS_ID)) {
            $criteria->add(CreditNoteTableMap::COL_STATUS_ID, $this->status_id);
        }
        if ($this->isColumnModified(CreditNoteTableMap::COL_CURRENCY_ID)) {
            $criteria->add(CreditNoteTableMap::COL_CURRENCY_ID, $this->currency_id);
        }
        if ($this->isColumnModified(CreditNoteTableMap::COL_CURRENCY_RATE)) {
            $criteria->add(CreditNoteTableMap::COL_CURRENCY_RATE, $this->currency_rate);
        }
        if ($this->isColumnModified(CreditNoteTableMap::COL_TOTAL_PRICE)) {
            $criteria->add(CreditNoteTableMap::COL_TOTAL_PRICE, $this->total_price);
        }
        if ($this->isColumnModified(CreditNoteTableMap::COL_TOTAL_PRICE_WITH_TAX)) {
            $criteria->add(CreditNoteTableMap::COL_TOTAL_PRICE_WITH_TAX, $this->total_price_with_tax);
        }
        if ($this->isColumnModified(CreditNoteTableMap::COL_DISCOUNT_WITHOUT_TAX)) {
            $criteria->add(CreditNoteTableMap::COL_DISCOUNT_WITHOUT_TAX, $this->discount_without_tax);
        }
        if ($this->isColumnModified(CreditNoteTableMap::COL_DISCOUNT_WITH_TAX)) {
            $criteria->add(CreditNoteTableMap::COL_DISCOUNT_WITH_TAX, $this->discount_with_tax);
        }
        if ($this->isColumnModified(CreditNoteTableMap::COL_ALLOW_PARTIAL_USE)) {
            $criteria->add(CreditNoteTableMap::COL_ALLOW_PARTIAL_USE, $this->allow_partial_use);
        }
        if ($this->isColumnModified(CreditNoteTableMap::COL_CREATED_AT)) {
            $criteria->add(CreditNoteTableMap::COL_CREATED_AT, $this->created_at);
        }
        if ($this->isColumnModified(CreditNoteTableMap::COL_UPDATED_AT)) {
            $criteria->add(CreditNoteTableMap::COL_UPDATED_AT, $this->updated_at);
        }
        if ($this->isColumnModified(CreditNoteTableMap::COL_VERSION)) {
            $criteria->add(CreditNoteTableMap::COL_VERSION, $this->version);
        }
        if ($this->isColumnModified(CreditNoteTableMap::COL_VERSION_CREATED_AT)) {
            $criteria->add(CreditNoteTableMap::COL_VERSION_CREATED_AT, $this->version_created_at);
        }
        if ($this->isColumnModified(CreditNoteTableMap::COL_VERSION_CREATED_BY)) {
            $criteria->add(CreditNoteTableMap::COL_VERSION_CREATED_BY, $this->version_created_by);
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
        $criteria = ChildCreditNoteQuery::create();
        $criteria->add(CreditNoteTableMap::COL_ID, $this->id);

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
     * @param      object $copyObj An object of \CreditNote\Model\CreditNote (or compatible) type.
     * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @param      boolean $makeNew Whether to reset autoincrement PKs and make the object new.
     * @throws PropelException
     */
    public function copyInto($copyObj, $deepCopy = false, $makeNew = true)
    {
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

        if ($deepCopy) {
            // important: temporarily setNew(false) because this affects the behavior of
            // the getter/setter methods for fkey referrer objects.
            $copyObj->setNew(false);

            foreach ($this->getCreditNotesRelatedById() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addCreditNoteRelatedById($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getOrderCreditNotes() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addOrderCreditNote($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getCartCreditNotes() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addCartCreditNote($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getCreditNoteDetails() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addCreditNoteDetail($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getCreditNoteComments() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addCreditNoteComment($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getCreditNoteVersions() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addCreditNoteVersion($relObj->copy($deepCopy));
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
     * @return \CreditNote\Model\CreditNote Clone of current object.
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
     * Declares an association between this object and a Order object.
     *
     * @param  Order $v
     * @return $this|\CreditNote\Model\CreditNote The current object (for fluent API support)
     * @throws PropelException
     */
    public function setOrder(Order $v = null)
    {
        if ($v === null) {
            $this->setOrderId(NULL);
        } else {
            $this->setOrderId($v->getId());
        }

        $this->aOrder = $v;

        // Add binding for other direction of this n:n relationship.
        // If this object has already been added to the Order object, it will not be re-added.
        if ($v !== null) {
            $v->addCreditNote($this);
        }


        return $this;
    }


    /**
     * Get the associated Order object
     *
     * @param  ConnectionInterface $con Optional Connection object.
     * @return Order The associated Order object.
     * @throws PropelException
     */
    public function getOrder(ConnectionInterface $con = null)
    {
        if ($this->aOrder === null && ($this->order_id != 0)) {
            $this->aOrder = OrderQuery::create()->findPk($this->order_id, $con);
            /* The following can be used additionally to
                guarantee the related object contains a reference
                to this object.  This level of coupling may, however, be
                undesirable since it could result in an only partially populated collection
                in the referenced object.
                $this->aOrder->addCreditNotes($this);
             */
        }

        return $this->aOrder;
    }

    /**
     * Declares an association between this object and a Customer object.
     *
     * @param  Customer $v
     * @return $this|\CreditNote\Model\CreditNote The current object (for fluent API support)
     * @throws PropelException
     */
    public function setCustomer(Customer $v = null)
    {
        if ($v === null) {
            $this->setCustomerId(NULL);
        } else {
            $this->setCustomerId($v->getId());
        }

        $this->aCustomer = $v;

        // Add binding for other direction of this n:n relationship.
        // If this object has already been added to the Customer object, it will not be re-added.
        if ($v !== null) {
            $v->addCreditNote($this);
        }


        return $this;
    }


    /**
     * Get the associated Customer object
     *
     * @param  ConnectionInterface $con Optional Connection object.
     * @return Customer The associated Customer object.
     * @throws PropelException
     */
    public function getCustomer(ConnectionInterface $con = null)
    {
        if ($this->aCustomer === null && ($this->customer_id != 0)) {
            $this->aCustomer = CustomerQuery::create()->findPk($this->customer_id, $con);
            /* The following can be used additionally to
                guarantee the related object contains a reference
                to this object.  This level of coupling may, however, be
                undesirable since it could result in an only partially populated collection
                in the referenced object.
                $this->aCustomer->addCreditNotes($this);
             */
        }

        return $this->aCustomer;
    }

    /**
     * Declares an association between this object and a ChildCreditNote object.
     *
     * @param  ChildCreditNote $v
     * @return $this|\CreditNote\Model\CreditNote The current object (for fluent API support)
     * @throws PropelException
     */
    public function setCreditNoteRelatedByParentId(ChildCreditNote $v = null)
    {
        if ($v === null) {
            $this->setParentId(NULL);
        } else {
            $this->setParentId($v->getId());
        }

        $this->aCreditNoteRelatedByParentId = $v;

        // Add binding for other direction of this n:n relationship.
        // If this object has already been added to the ChildCreditNote object, it will not be re-added.
        if ($v !== null) {
            $v->addCreditNoteRelatedById($this);
        }


        return $this;
    }


    /**
     * Get the associated ChildCreditNote object
     *
     * @param  ConnectionInterface $con Optional Connection object.
     * @return ChildCreditNote The associated ChildCreditNote object.
     * @throws PropelException
     */
    public function getCreditNoteRelatedByParentId(ConnectionInterface $con = null)
    {
        if ($this->aCreditNoteRelatedByParentId === null && ($this->parent_id != 0)) {
            $this->aCreditNoteRelatedByParentId = ChildCreditNoteQuery::create()->findPk($this->parent_id, $con);
            /* The following can be used additionally to
                guarantee the related object contains a reference
                to this object.  This level of coupling may, however, be
                undesirable since it could result in an only partially populated collection
                in the referenced object.
                $this->aCreditNoteRelatedByParentId->addCreditNotesRelatedById($this);
             */
        }

        return $this->aCreditNoteRelatedByParentId;
    }

    /**
     * Declares an association between this object and a ChildCreditNoteType object.
     *
     * @param  ChildCreditNoteType $v
     * @return $this|\CreditNote\Model\CreditNote The current object (for fluent API support)
     * @throws PropelException
     */
    public function setCreditNoteType(ChildCreditNoteType $v = null)
    {
        if ($v === null) {
            $this->setTypeId(NULL);
        } else {
            $this->setTypeId($v->getId());
        }

        $this->aCreditNoteType = $v;

        // Add binding for other direction of this n:n relationship.
        // If this object has already been added to the ChildCreditNoteType object, it will not be re-added.
        if ($v !== null) {
            $v->addCreditNote($this);
        }


        return $this;
    }


    /**
     * Get the associated ChildCreditNoteType object
     *
     * @param  ConnectionInterface $con Optional Connection object.
     * @return ChildCreditNoteType The associated ChildCreditNoteType object.
     * @throws PropelException
     */
    public function getCreditNoteType(ConnectionInterface $con = null)
    {
        if ($this->aCreditNoteType === null && ($this->type_id != 0)) {
            $this->aCreditNoteType = ChildCreditNoteTypeQuery::create()->findPk($this->type_id, $con);
            /* The following can be used additionally to
                guarantee the related object contains a reference
                to this object.  This level of coupling may, however, be
                undesirable since it could result in an only partially populated collection
                in the referenced object.
                $this->aCreditNoteType->addCreditNotes($this);
             */
        }

        return $this->aCreditNoteType;
    }

    /**
     * Declares an association between this object and a ChildCreditNoteStatus object.
     *
     * @param  ChildCreditNoteStatus $v
     * @return $this|\CreditNote\Model\CreditNote The current object (for fluent API support)
     * @throws PropelException
     */
    public function setCreditNoteStatus(ChildCreditNoteStatus $v = null)
    {
        if ($v === null) {
            $this->setStatusId(NULL);
        } else {
            $this->setStatusId($v->getId());
        }

        $this->aCreditNoteStatus = $v;

        // Add binding for other direction of this n:n relationship.
        // If this object has already been added to the ChildCreditNoteStatus object, it will not be re-added.
        if ($v !== null) {
            $v->addCreditNote($this);
        }


        return $this;
    }


    /**
     * Get the associated ChildCreditNoteStatus object
     *
     * @param  ConnectionInterface $con Optional Connection object.
     * @return ChildCreditNoteStatus The associated ChildCreditNoteStatus object.
     * @throws PropelException
     */
    public function getCreditNoteStatus(ConnectionInterface $con = null)
    {
        if ($this->aCreditNoteStatus === null && ($this->status_id != 0)) {
            $this->aCreditNoteStatus = ChildCreditNoteStatusQuery::create()->findPk($this->status_id, $con);
            /* The following can be used additionally to
                guarantee the related object contains a reference
                to this object.  This level of coupling may, however, be
                undesirable since it could result in an only partially populated collection
                in the referenced object.
                $this->aCreditNoteStatus->addCreditNotes($this);
             */
        }

        return $this->aCreditNoteStatus;
    }

    /**
     * Declares an association between this object and a Currency object.
     *
     * @param  Currency $v
     * @return $this|\CreditNote\Model\CreditNote The current object (for fluent API support)
     * @throws PropelException
     */
    public function setCurrency(Currency $v = null)
    {
        if ($v === null) {
            $this->setCurrencyId(NULL);
        } else {
            $this->setCurrencyId($v->getId());
        }

        $this->aCurrency = $v;

        // Add binding for other direction of this n:n relationship.
        // If this object has already been added to the Currency object, it will not be re-added.
        if ($v !== null) {
            $v->addCreditNote($this);
        }


        return $this;
    }


    /**
     * Get the associated Currency object
     *
     * @param  ConnectionInterface $con Optional Connection object.
     * @return Currency The associated Currency object.
     * @throws PropelException
     */
    public function getCurrency(ConnectionInterface $con = null)
    {
        if ($this->aCurrency === null && ($this->currency_id != 0)) {
            $this->aCurrency = CurrencyQuery::create()->findPk($this->currency_id, $con);
            /* The following can be used additionally to
                guarantee the related object contains a reference
                to this object.  This level of coupling may, however, be
                undesirable since it could result in an only partially populated collection
                in the referenced object.
                $this->aCurrency->addCreditNotes($this);
             */
        }

        return $this->aCurrency;
    }

    /**
     * Declares an association between this object and a ChildCreditNoteAddress object.
     *
     * @param  ChildCreditNoteAddress $v
     * @return $this|\CreditNote\Model\CreditNote The current object (for fluent API support)
     * @throws PropelException
     */
    public function setCreditNoteAddress(ChildCreditNoteAddress $v = null)
    {
        if ($v === null) {
            $this->setInvoiceAddressId(NULL);
        } else {
            $this->setInvoiceAddressId($v->getId());
        }

        $this->aCreditNoteAddress = $v;

        // Add binding for other direction of this n:n relationship.
        // If this object has already been added to the ChildCreditNoteAddress object, it will not be re-added.
        if ($v !== null) {
            $v->addCreditNote($this);
        }


        return $this;
    }


    /**
     * Get the associated ChildCreditNoteAddress object
     *
     * @param  ConnectionInterface $con Optional Connection object.
     * @return ChildCreditNoteAddress The associated ChildCreditNoteAddress object.
     * @throws PropelException
     */
    public function getCreditNoteAddress(ConnectionInterface $con = null)
    {
        if ($this->aCreditNoteAddress === null && ($this->invoice_address_id != 0)) {
            $this->aCreditNoteAddress = ChildCreditNoteAddressQuery::create()->findPk($this->invoice_address_id, $con);
            /* The following can be used additionally to
                guarantee the related object contains a reference
                to this object.  This level of coupling may, however, be
                undesirable since it could result in an only partially populated collection
                in the referenced object.
                $this->aCreditNoteAddress->addCreditNotes($this);
             */
        }

        return $this->aCreditNoteAddress;
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
        if ('CreditNoteRelatedById' == $relationName) {
            $this->initCreditNotesRelatedById();
            return;
        }
        if ('OrderCreditNote' == $relationName) {
            $this->initOrderCreditNotes();
            return;
        }
        if ('CartCreditNote' == $relationName) {
            $this->initCartCreditNotes();
            return;
        }
        if ('CreditNoteDetail' == $relationName) {
            $this->initCreditNoteDetails();
            return;
        }
        if ('CreditNoteComment' == $relationName) {
            $this->initCreditNoteComments();
            return;
        }
        if ('CreditNoteVersion' == $relationName) {
            $this->initCreditNoteVersions();
            return;
        }
    }

    /**
     * Clears out the collCreditNotesRelatedById collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return void
     * @see        addCreditNotesRelatedById()
     */
    public function clearCreditNotesRelatedById()
    {
        $this->collCreditNotesRelatedById = null; // important to set this to NULL since that means it is uninitialized
    }

    /**
     * Reset is the collCreditNotesRelatedById collection loaded partially.
     */
    public function resetPartialCreditNotesRelatedById($v = true)
    {
        $this->collCreditNotesRelatedByIdPartial = $v;
    }

    /**
     * Initializes the collCreditNotesRelatedById collection.
     *
     * By default this just sets the collCreditNotesRelatedById collection to an empty array (like clearcollCreditNotesRelatedById());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param      boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initCreditNotesRelatedById($overrideExisting = true)
    {
        if (null !== $this->collCreditNotesRelatedById && !$overrideExisting) {
            return;
        }

        $collectionClassName = CreditNoteTableMap::getTableMap()->getCollectionClassName();

        $this->collCreditNotesRelatedById = new $collectionClassName;
        $this->collCreditNotesRelatedById->setModel('\CreditNote\Model\CreditNote');
    }

    /**
     * Gets an array of ChildCreditNote objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this ChildCreditNote is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @return ObjectCollection|ChildCreditNote[] List of ChildCreditNote objects
     * @throws PropelException
     */
    public function getCreditNotesRelatedById($criteria = null, ConnectionInterface $con = null)
    {
        $partial = $this->collCreditNotesRelatedByIdPartial && !$this->isNew();
        if (null === $this->collCreditNotesRelatedById || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collCreditNotesRelatedById) {
                // return empty collection
                $this->initCreditNotesRelatedById();
            } else {
                $collCreditNotesRelatedById = ChildCreditNoteQuery::create(null, $criteria)
                    ->filterByCreditNoteRelatedByParentId($this)
                    ->find($con);

                if (null !== $criteria) {
                    if (false !== $this->collCreditNotesRelatedByIdPartial && count($collCreditNotesRelatedById)) {
                        $this->initCreditNotesRelatedById(false);

                        foreach ($collCreditNotesRelatedById as $obj) {
                            if (false == $this->collCreditNotesRelatedById->contains($obj)) {
                                $this->collCreditNotesRelatedById->append($obj);
                            }
                        }

                        $this->collCreditNotesRelatedByIdPartial = true;
                    }

                    return $collCreditNotesRelatedById;
                }

                if ($partial && $this->collCreditNotesRelatedById) {
                    foreach ($this->collCreditNotesRelatedById as $obj) {
                        if ($obj->isNew()) {
                            $collCreditNotesRelatedById[] = $obj;
                        }
                    }
                }

                $this->collCreditNotesRelatedById = $collCreditNotesRelatedById;
                $this->collCreditNotesRelatedByIdPartial = false;
            }
        }

        return $this->collCreditNotesRelatedById;
    }

    /**
     * Sets a collection of ChildCreditNote objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param      Collection $creditNotesRelatedById A Propel collection.
     * @param      ConnectionInterface $con Optional connection object
     * @return $this|ChildCreditNote The current object (for fluent API support)
     */
    public function setCreditNotesRelatedById(Collection $creditNotesRelatedById, ConnectionInterface $con = null)
    {
        /** @var ChildCreditNote[] $creditNotesRelatedByIdToDelete */
        $creditNotesRelatedByIdToDelete = $this->getCreditNotesRelatedById(new Criteria(), $con)->diff($creditNotesRelatedById);


        $this->creditNotesRelatedByIdScheduledForDeletion = $creditNotesRelatedByIdToDelete;

        foreach ($creditNotesRelatedByIdToDelete as $creditNoteRelatedByIdRemoved) {
            $creditNoteRelatedByIdRemoved->setCreditNoteRelatedByParentId(null);
        }

        $this->collCreditNotesRelatedById = null;
        foreach ($creditNotesRelatedById as $creditNoteRelatedById) {
            $this->addCreditNoteRelatedById($creditNoteRelatedById);
        }

        $this->collCreditNotesRelatedById = $creditNotesRelatedById;
        $this->collCreditNotesRelatedByIdPartial = false;

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
    public function countCreditNotesRelatedById(Criteria $criteria = null, $distinct = false, ConnectionInterface $con = null)
    {
        $partial = $this->collCreditNotesRelatedByIdPartial && !$this->isNew();
        if (null === $this->collCreditNotesRelatedById || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collCreditNotesRelatedById) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getCreditNotesRelatedById());
            }

            $query = ChildCreditNoteQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByCreditNoteRelatedByParentId($this)
                ->count($con);
        }

        return count($this->collCreditNotesRelatedById);
    }

    /**
     * Method called to associate a ChildCreditNote object to this object
     * through the ChildCreditNote foreign key attribute.
     *
     * @param  ChildCreditNote $l ChildCreditNote
     * @return $this|\CreditNote\Model\CreditNote The current object (for fluent API support)
     */
    public function addCreditNoteRelatedById(ChildCreditNote $l)
    {
        if ($this->collCreditNotesRelatedById === null) {
            $this->initCreditNotesRelatedById();
            $this->collCreditNotesRelatedByIdPartial = true;
        }

        if (!$this->collCreditNotesRelatedById->contains($l)) {
            $this->doAddCreditNoteRelatedById($l);

            if ($this->creditNotesRelatedByIdScheduledForDeletion and $this->creditNotesRelatedByIdScheduledForDeletion->contains($l)) {
                $this->creditNotesRelatedByIdScheduledForDeletion->remove($this->creditNotesRelatedByIdScheduledForDeletion->search($l));
            }
        }

        return $this;
    }

    /**
     * @param ChildCreditNote $creditNoteRelatedById The ChildCreditNote object to add.
     */
    protected function doAddCreditNoteRelatedById($creditNoteRelatedById)
    {
        $this->collCreditNotesRelatedById[]= $creditNoteRelatedById;
        $creditNoteRelatedById->setCreditNoteRelatedByParentId($this);
    }

    /**
     * @param  ChildCreditNote $creditNoteRelatedById The ChildCreditNote object to remove.
     * @return $this|ChildCreditNote The current object (for fluent API support)
     */
    public function removeCreditNoteRelatedById($creditNoteRelatedById)
    {
        if ($this->getCreditNotesRelatedById()->contains($creditNoteRelatedById)) {
            $pos = $this->collCreditNotesRelatedById->search($creditNoteRelatedById);
            $this->collCreditNotesRelatedById->remove($pos);
            if (null === $this->creditNotesRelatedByIdScheduledForDeletion) {
                $this->creditNotesRelatedByIdScheduledForDeletion = clone $this->collCreditNotesRelatedById;
                $this->creditNotesRelatedByIdScheduledForDeletion->clear();
            }
            $this->creditNotesRelatedByIdScheduledForDeletion[]= $creditNoteRelatedById;
            $creditNoteRelatedById->setCreditNoteRelatedByParentId(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this CreditNote is new, it will return
     * an empty collection; or if this CreditNote has previously
     * been saved, it will retrieve related CreditNotesRelatedById from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in CreditNote.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @param      string $joinBehavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return ObjectCollection|ChildCreditNote[] List of ChildCreditNote objects
     */
    public function getCreditNotesRelatedByIdJoinOrder(Criteria $criteria = null, $con = null, $joinBehavior = Criteria::LEFT_JOIN)
    {
        $query = ChildCreditNoteQuery::create(null, $criteria);
        $query->joinWith('Order', $joinBehavior);

        return $this->getCreditNotesRelatedById($query, $con);
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this CreditNote is new, it will return
     * an empty collection; or if this CreditNote has previously
     * been saved, it will retrieve related CreditNotesRelatedById from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in CreditNote.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @param      string $joinBehavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return ObjectCollection|ChildCreditNote[] List of ChildCreditNote objects
     */
    public function getCreditNotesRelatedByIdJoinCustomer(Criteria $criteria = null, $con = null, $joinBehavior = Criteria::LEFT_JOIN)
    {
        $query = ChildCreditNoteQuery::create(null, $criteria);
        $query->joinWith('Customer', $joinBehavior);

        return $this->getCreditNotesRelatedById($query, $con);
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this CreditNote is new, it will return
     * an empty collection; or if this CreditNote has previously
     * been saved, it will retrieve related CreditNotesRelatedById from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in CreditNote.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @param      string $joinBehavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return ObjectCollection|ChildCreditNote[] List of ChildCreditNote objects
     */
    public function getCreditNotesRelatedByIdJoinCreditNoteType(Criteria $criteria = null, $con = null, $joinBehavior = Criteria::LEFT_JOIN)
    {
        $query = ChildCreditNoteQuery::create(null, $criteria);
        $query->joinWith('CreditNoteType', $joinBehavior);

        return $this->getCreditNotesRelatedById($query, $con);
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this CreditNote is new, it will return
     * an empty collection; or if this CreditNote has previously
     * been saved, it will retrieve related CreditNotesRelatedById from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in CreditNote.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @param      string $joinBehavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return ObjectCollection|ChildCreditNote[] List of ChildCreditNote objects
     */
    public function getCreditNotesRelatedByIdJoinCreditNoteStatus(Criteria $criteria = null, $con = null, $joinBehavior = Criteria::LEFT_JOIN)
    {
        $query = ChildCreditNoteQuery::create(null, $criteria);
        $query->joinWith('CreditNoteStatus', $joinBehavior);

        return $this->getCreditNotesRelatedById($query, $con);
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this CreditNote is new, it will return
     * an empty collection; or if this CreditNote has previously
     * been saved, it will retrieve related CreditNotesRelatedById from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in CreditNote.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @param      string $joinBehavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return ObjectCollection|ChildCreditNote[] List of ChildCreditNote objects
     */
    public function getCreditNotesRelatedByIdJoinCurrency(Criteria $criteria = null, $con = null, $joinBehavior = Criteria::LEFT_JOIN)
    {
        $query = ChildCreditNoteQuery::create(null, $criteria);
        $query->joinWith('Currency', $joinBehavior);

        return $this->getCreditNotesRelatedById($query, $con);
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this CreditNote is new, it will return
     * an empty collection; or if this CreditNote has previously
     * been saved, it will retrieve related CreditNotesRelatedById from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in CreditNote.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @param      string $joinBehavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return ObjectCollection|ChildCreditNote[] List of ChildCreditNote objects
     */
    public function getCreditNotesRelatedByIdJoinCreditNoteAddress(Criteria $criteria = null, $con = null, $joinBehavior = Criteria::LEFT_JOIN)
    {
        $query = ChildCreditNoteQuery::create(null, $criteria);
        $query->joinWith('CreditNoteAddress', $joinBehavior);

        return $this->getCreditNotesRelatedById($query, $con);
    }

    /**
     * Clears out the collOrderCreditNotes collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return void
     * @see        addOrderCreditNotes()
     */
    public function clearOrderCreditNotes()
    {
        $this->collOrderCreditNotes = null; // important to set this to NULL since that means it is uninitialized
    }

    /**
     * Reset is the collOrderCreditNotes collection loaded partially.
     */
    public function resetPartialOrderCreditNotes($v = true)
    {
        $this->collOrderCreditNotesPartial = $v;
    }

    /**
     * Initializes the collOrderCreditNotes collection.
     *
     * By default this just sets the collOrderCreditNotes collection to an empty array (like clearcollOrderCreditNotes());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param      boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initOrderCreditNotes($overrideExisting = true)
    {
        if (null !== $this->collOrderCreditNotes && !$overrideExisting) {
            return;
        }

        $collectionClassName = OrderCreditNoteTableMap::getTableMap()->getCollectionClassName();

        $this->collOrderCreditNotes = new $collectionClassName;
        $this->collOrderCreditNotes->setModel('\CreditNote\Model\OrderCreditNote');
    }

    /**
     * Gets an array of ChildOrderCreditNote objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this ChildCreditNote is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @return ObjectCollection|ChildOrderCreditNote[] List of ChildOrderCreditNote objects
     * @throws PropelException
     */
    public function getOrderCreditNotes($criteria = null, ConnectionInterface $con = null)
    {
        $partial = $this->collOrderCreditNotesPartial && !$this->isNew();
        if (null === $this->collOrderCreditNotes || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collOrderCreditNotes) {
                // return empty collection
                $this->initOrderCreditNotes();
            } else {
                $collOrderCreditNotes = ChildOrderCreditNoteQuery::create(null, $criteria)
                    ->filterByCreditNote($this)
                    ->find($con);

                if (null !== $criteria) {
                    if (false !== $this->collOrderCreditNotesPartial && count($collOrderCreditNotes)) {
                        $this->initOrderCreditNotes(false);

                        foreach ($collOrderCreditNotes as $obj) {
                            if (false == $this->collOrderCreditNotes->contains($obj)) {
                                $this->collOrderCreditNotes->append($obj);
                            }
                        }

                        $this->collOrderCreditNotesPartial = true;
                    }

                    return $collOrderCreditNotes;
                }

                if ($partial && $this->collOrderCreditNotes) {
                    foreach ($this->collOrderCreditNotes as $obj) {
                        if ($obj->isNew()) {
                            $collOrderCreditNotes[] = $obj;
                        }
                    }
                }

                $this->collOrderCreditNotes = $collOrderCreditNotes;
                $this->collOrderCreditNotesPartial = false;
            }
        }

        return $this->collOrderCreditNotes;
    }

    /**
     * Sets a collection of ChildOrderCreditNote objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param      Collection $orderCreditNotes A Propel collection.
     * @param      ConnectionInterface $con Optional connection object
     * @return $this|ChildCreditNote The current object (for fluent API support)
     */
    public function setOrderCreditNotes(Collection $orderCreditNotes, ConnectionInterface $con = null)
    {
        /** @var ChildOrderCreditNote[] $orderCreditNotesToDelete */
        $orderCreditNotesToDelete = $this->getOrderCreditNotes(new Criteria(), $con)->diff($orderCreditNotes);


        //since at least one column in the foreign key is at the same time a PK
        //we can not just set a PK to NULL in the lines below. We have to store
        //a backup of all values, so we are able to manipulate these items based on the onDelete value later.
        $this->orderCreditNotesScheduledForDeletion = clone $orderCreditNotesToDelete;

        foreach ($orderCreditNotesToDelete as $orderCreditNoteRemoved) {
            $orderCreditNoteRemoved->setCreditNote(null);
        }

        $this->collOrderCreditNotes = null;
        foreach ($orderCreditNotes as $orderCreditNote) {
            $this->addOrderCreditNote($orderCreditNote);
        }

        $this->collOrderCreditNotes = $orderCreditNotes;
        $this->collOrderCreditNotesPartial = false;

        return $this;
    }

    /**
     * Returns the number of related OrderCreditNote objects.
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct
     * @param      ConnectionInterface $con
     * @return int             Count of related OrderCreditNote objects.
     * @throws PropelException
     */
    public function countOrderCreditNotes(Criteria $criteria = null, $distinct = false, ConnectionInterface $con = null)
    {
        $partial = $this->collOrderCreditNotesPartial && !$this->isNew();
        if (null === $this->collOrderCreditNotes || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collOrderCreditNotes) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getOrderCreditNotes());
            }

            $query = ChildOrderCreditNoteQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByCreditNote($this)
                ->count($con);
        }

        return count($this->collOrderCreditNotes);
    }

    /**
     * Method called to associate a ChildOrderCreditNote object to this object
     * through the ChildOrderCreditNote foreign key attribute.
     *
     * @param  ChildOrderCreditNote $l ChildOrderCreditNote
     * @return $this|\CreditNote\Model\CreditNote The current object (for fluent API support)
     */
    public function addOrderCreditNote(ChildOrderCreditNote $l)
    {
        if ($this->collOrderCreditNotes === null) {
            $this->initOrderCreditNotes();
            $this->collOrderCreditNotesPartial = true;
        }

        if (!$this->collOrderCreditNotes->contains($l)) {
            $this->doAddOrderCreditNote($l);

            if ($this->orderCreditNotesScheduledForDeletion and $this->orderCreditNotesScheduledForDeletion->contains($l)) {
                $this->orderCreditNotesScheduledForDeletion->remove($this->orderCreditNotesScheduledForDeletion->search($l));
            }
        }

        return $this;
    }

    /**
     * @param ChildOrderCreditNote $orderCreditNote The ChildOrderCreditNote object to add.
     */
    protected function doAddOrderCreditNote($orderCreditNote)
    {
        $this->collOrderCreditNotes[]= $orderCreditNote;
        $orderCreditNote->setCreditNote($this);
    }

    /**
     * @param  ChildOrderCreditNote $orderCreditNote The ChildOrderCreditNote object to remove.
     * @return $this|ChildCreditNote The current object (for fluent API support)
     */
    public function removeOrderCreditNote($orderCreditNote)
    {
        if ($this->getOrderCreditNotes()->contains($orderCreditNote)) {
            $pos = $this->collOrderCreditNotes->search($orderCreditNote);
            $this->collOrderCreditNotes->remove($pos);
            if (null === $this->orderCreditNotesScheduledForDeletion) {
                $this->orderCreditNotesScheduledForDeletion = clone $this->collOrderCreditNotes;
                $this->orderCreditNotesScheduledForDeletion->clear();
            }
            $this->orderCreditNotesScheduledForDeletion[]= clone $orderCreditNote;
            $orderCreditNote->setCreditNote(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this CreditNote is new, it will return
     * an empty collection; or if this CreditNote has previously
     * been saved, it will retrieve related OrderCreditNotes from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in CreditNote.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @param      string $joinBehavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return ObjectCollection|ChildOrderCreditNote[] List of ChildOrderCreditNote objects
     */
    public function getOrderCreditNotesJoinOrder(Criteria $criteria = null, $con = null, $joinBehavior = Criteria::LEFT_JOIN)
    {
        $query = ChildOrderCreditNoteQuery::create(null, $criteria);
        $query->joinWith('Order', $joinBehavior);

        return $this->getOrderCreditNotes($query, $con);
    }

    /**
     * Clears out the collCartCreditNotes collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return void
     * @see        addCartCreditNotes()
     */
    public function clearCartCreditNotes()
    {
        $this->collCartCreditNotes = null; // important to set this to NULL since that means it is uninitialized
    }

    /**
     * Reset is the collCartCreditNotes collection loaded partially.
     */
    public function resetPartialCartCreditNotes($v = true)
    {
        $this->collCartCreditNotesPartial = $v;
    }

    /**
     * Initializes the collCartCreditNotes collection.
     *
     * By default this just sets the collCartCreditNotes collection to an empty array (like clearcollCartCreditNotes());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param      boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initCartCreditNotes($overrideExisting = true)
    {
        if (null !== $this->collCartCreditNotes && !$overrideExisting) {
            return;
        }

        $collectionClassName = CartCreditNoteTableMap::getTableMap()->getCollectionClassName();

        $this->collCartCreditNotes = new $collectionClassName;
        $this->collCartCreditNotes->setModel('\CreditNote\Model\CartCreditNote');
    }

    /**
     * Gets an array of ChildCartCreditNote objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this ChildCreditNote is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @return ObjectCollection|ChildCartCreditNote[] List of ChildCartCreditNote objects
     * @throws PropelException
     */
    public function getCartCreditNotes($criteria = null, ConnectionInterface $con = null)
    {
        $partial = $this->collCartCreditNotesPartial && !$this->isNew();
        if (null === $this->collCartCreditNotes || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collCartCreditNotes) {
                // return empty collection
                $this->initCartCreditNotes();
            } else {
                $collCartCreditNotes = ChildCartCreditNoteQuery::create(null, $criteria)
                    ->filterByCreditNote($this)
                    ->find($con);

                if (null !== $criteria) {
                    if (false !== $this->collCartCreditNotesPartial && count($collCartCreditNotes)) {
                        $this->initCartCreditNotes(false);

                        foreach ($collCartCreditNotes as $obj) {
                            if (false == $this->collCartCreditNotes->contains($obj)) {
                                $this->collCartCreditNotes->append($obj);
                            }
                        }

                        $this->collCartCreditNotesPartial = true;
                    }

                    return $collCartCreditNotes;
                }

                if ($partial && $this->collCartCreditNotes) {
                    foreach ($this->collCartCreditNotes as $obj) {
                        if ($obj->isNew()) {
                            $collCartCreditNotes[] = $obj;
                        }
                    }
                }

                $this->collCartCreditNotes = $collCartCreditNotes;
                $this->collCartCreditNotesPartial = false;
            }
        }

        return $this->collCartCreditNotes;
    }

    /**
     * Sets a collection of ChildCartCreditNote objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param      Collection $cartCreditNotes A Propel collection.
     * @param      ConnectionInterface $con Optional connection object
     * @return $this|ChildCreditNote The current object (for fluent API support)
     */
    public function setCartCreditNotes(Collection $cartCreditNotes, ConnectionInterface $con = null)
    {
        /** @var ChildCartCreditNote[] $cartCreditNotesToDelete */
        $cartCreditNotesToDelete = $this->getCartCreditNotes(new Criteria(), $con)->diff($cartCreditNotes);


        //since at least one column in the foreign key is at the same time a PK
        //we can not just set a PK to NULL in the lines below. We have to store
        //a backup of all values, so we are able to manipulate these items based on the onDelete value later.
        $this->cartCreditNotesScheduledForDeletion = clone $cartCreditNotesToDelete;

        foreach ($cartCreditNotesToDelete as $cartCreditNoteRemoved) {
            $cartCreditNoteRemoved->setCreditNote(null);
        }

        $this->collCartCreditNotes = null;
        foreach ($cartCreditNotes as $cartCreditNote) {
            $this->addCartCreditNote($cartCreditNote);
        }

        $this->collCartCreditNotes = $cartCreditNotes;
        $this->collCartCreditNotesPartial = false;

        return $this;
    }

    /**
     * Returns the number of related CartCreditNote objects.
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct
     * @param      ConnectionInterface $con
     * @return int             Count of related CartCreditNote objects.
     * @throws PropelException
     */
    public function countCartCreditNotes(Criteria $criteria = null, $distinct = false, ConnectionInterface $con = null)
    {
        $partial = $this->collCartCreditNotesPartial && !$this->isNew();
        if (null === $this->collCartCreditNotes || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collCartCreditNotes) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getCartCreditNotes());
            }

            $query = ChildCartCreditNoteQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByCreditNote($this)
                ->count($con);
        }

        return count($this->collCartCreditNotes);
    }

    /**
     * Method called to associate a ChildCartCreditNote object to this object
     * through the ChildCartCreditNote foreign key attribute.
     *
     * @param  ChildCartCreditNote $l ChildCartCreditNote
     * @return $this|\CreditNote\Model\CreditNote The current object (for fluent API support)
     */
    public function addCartCreditNote(ChildCartCreditNote $l)
    {
        if ($this->collCartCreditNotes === null) {
            $this->initCartCreditNotes();
            $this->collCartCreditNotesPartial = true;
        }

        if (!$this->collCartCreditNotes->contains($l)) {
            $this->doAddCartCreditNote($l);

            if ($this->cartCreditNotesScheduledForDeletion and $this->cartCreditNotesScheduledForDeletion->contains($l)) {
                $this->cartCreditNotesScheduledForDeletion->remove($this->cartCreditNotesScheduledForDeletion->search($l));
            }
        }

        return $this;
    }

    /**
     * @param ChildCartCreditNote $cartCreditNote The ChildCartCreditNote object to add.
     */
    protected function doAddCartCreditNote($cartCreditNote)
    {
        $this->collCartCreditNotes[]= $cartCreditNote;
        $cartCreditNote->setCreditNote($this);
    }

    /**
     * @param  ChildCartCreditNote $cartCreditNote The ChildCartCreditNote object to remove.
     * @return $this|ChildCreditNote The current object (for fluent API support)
     */
    public function removeCartCreditNote($cartCreditNote)
    {
        if ($this->getCartCreditNotes()->contains($cartCreditNote)) {
            $pos = $this->collCartCreditNotes->search($cartCreditNote);
            $this->collCartCreditNotes->remove($pos);
            if (null === $this->cartCreditNotesScheduledForDeletion) {
                $this->cartCreditNotesScheduledForDeletion = clone $this->collCartCreditNotes;
                $this->cartCreditNotesScheduledForDeletion->clear();
            }
            $this->cartCreditNotesScheduledForDeletion[]= clone $cartCreditNote;
            $cartCreditNote->setCreditNote(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this CreditNote is new, it will return
     * an empty collection; or if this CreditNote has previously
     * been saved, it will retrieve related CartCreditNotes from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in CreditNote.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @param      string $joinBehavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return ObjectCollection|ChildCartCreditNote[] List of ChildCartCreditNote objects
     */
    public function getCartCreditNotesJoinCart(Criteria $criteria = null, $con = null, $joinBehavior = Criteria::LEFT_JOIN)
    {
        $query = ChildCartCreditNoteQuery::create(null, $criteria);
        $query->joinWith('Cart', $joinBehavior);

        return $this->getCartCreditNotes($query, $con);
    }

    /**
     * Clears out the collCreditNoteDetails collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return void
     * @see        addCreditNoteDetails()
     */
    public function clearCreditNoteDetails()
    {
        $this->collCreditNoteDetails = null; // important to set this to NULL since that means it is uninitialized
    }

    /**
     * Reset is the collCreditNoteDetails collection loaded partially.
     */
    public function resetPartialCreditNoteDetails($v = true)
    {
        $this->collCreditNoteDetailsPartial = $v;
    }

    /**
     * Initializes the collCreditNoteDetails collection.
     *
     * By default this just sets the collCreditNoteDetails collection to an empty array (like clearcollCreditNoteDetails());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param      boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initCreditNoteDetails($overrideExisting = true)
    {
        if (null !== $this->collCreditNoteDetails && !$overrideExisting) {
            return;
        }

        $collectionClassName = CreditNoteDetailTableMap::getTableMap()->getCollectionClassName();

        $this->collCreditNoteDetails = new $collectionClassName;
        $this->collCreditNoteDetails->setModel('\CreditNote\Model\CreditNoteDetail');
    }

    /**
     * Gets an array of ChildCreditNoteDetail objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this ChildCreditNote is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @return ObjectCollection|ChildCreditNoteDetail[] List of ChildCreditNoteDetail objects
     * @throws PropelException
     */
    public function getCreditNoteDetails($criteria = null, ConnectionInterface $con = null)
    {
        $partial = $this->collCreditNoteDetailsPartial && !$this->isNew();
        if (null === $this->collCreditNoteDetails || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collCreditNoteDetails) {
                // return empty collection
                $this->initCreditNoteDetails();
            } else {
                $collCreditNoteDetails = ChildCreditNoteDetailQuery::create(null, $criteria)
                    ->filterByCreditNote($this)
                    ->find($con);

                if (null !== $criteria) {
                    if (false !== $this->collCreditNoteDetailsPartial && count($collCreditNoteDetails)) {
                        $this->initCreditNoteDetails(false);

                        foreach ($collCreditNoteDetails as $obj) {
                            if (false == $this->collCreditNoteDetails->contains($obj)) {
                                $this->collCreditNoteDetails->append($obj);
                            }
                        }

                        $this->collCreditNoteDetailsPartial = true;
                    }

                    return $collCreditNoteDetails;
                }

                if ($partial && $this->collCreditNoteDetails) {
                    foreach ($this->collCreditNoteDetails as $obj) {
                        if ($obj->isNew()) {
                            $collCreditNoteDetails[] = $obj;
                        }
                    }
                }

                $this->collCreditNoteDetails = $collCreditNoteDetails;
                $this->collCreditNoteDetailsPartial = false;
            }
        }

        return $this->collCreditNoteDetails;
    }

    /**
     * Sets a collection of ChildCreditNoteDetail objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param      Collection $creditNoteDetails A Propel collection.
     * @param      ConnectionInterface $con Optional connection object
     * @return $this|ChildCreditNote The current object (for fluent API support)
     */
    public function setCreditNoteDetails(Collection $creditNoteDetails, ConnectionInterface $con = null)
    {
        /** @var ChildCreditNoteDetail[] $creditNoteDetailsToDelete */
        $creditNoteDetailsToDelete = $this->getCreditNoteDetails(new Criteria(), $con)->diff($creditNoteDetails);


        $this->creditNoteDetailsScheduledForDeletion = $creditNoteDetailsToDelete;

        foreach ($creditNoteDetailsToDelete as $creditNoteDetailRemoved) {
            $creditNoteDetailRemoved->setCreditNote(null);
        }

        $this->collCreditNoteDetails = null;
        foreach ($creditNoteDetails as $creditNoteDetail) {
            $this->addCreditNoteDetail($creditNoteDetail);
        }

        $this->collCreditNoteDetails = $creditNoteDetails;
        $this->collCreditNoteDetailsPartial = false;

        return $this;
    }

    /**
     * Returns the number of related CreditNoteDetail objects.
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct
     * @param      ConnectionInterface $con
     * @return int             Count of related CreditNoteDetail objects.
     * @throws PropelException
     */
    public function countCreditNoteDetails(Criteria $criteria = null, $distinct = false, ConnectionInterface $con = null)
    {
        $partial = $this->collCreditNoteDetailsPartial && !$this->isNew();
        if (null === $this->collCreditNoteDetails || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collCreditNoteDetails) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getCreditNoteDetails());
            }

            $query = ChildCreditNoteDetailQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByCreditNote($this)
                ->count($con);
        }

        return count($this->collCreditNoteDetails);
    }

    /**
     * Method called to associate a ChildCreditNoteDetail object to this object
     * through the ChildCreditNoteDetail foreign key attribute.
     *
     * @param  ChildCreditNoteDetail $l ChildCreditNoteDetail
     * @return $this|\CreditNote\Model\CreditNote The current object (for fluent API support)
     */
    public function addCreditNoteDetail(ChildCreditNoteDetail $l)
    {
        if ($this->collCreditNoteDetails === null) {
            $this->initCreditNoteDetails();
            $this->collCreditNoteDetailsPartial = true;
        }

        if (!$this->collCreditNoteDetails->contains($l)) {
            $this->doAddCreditNoteDetail($l);

            if ($this->creditNoteDetailsScheduledForDeletion and $this->creditNoteDetailsScheduledForDeletion->contains($l)) {
                $this->creditNoteDetailsScheduledForDeletion->remove($this->creditNoteDetailsScheduledForDeletion->search($l));
            }
        }

        return $this;
    }

    /**
     * @param ChildCreditNoteDetail $creditNoteDetail The ChildCreditNoteDetail object to add.
     */
    protected function doAddCreditNoteDetail($creditNoteDetail)
    {
        $this->collCreditNoteDetails[]= $creditNoteDetail;
        $creditNoteDetail->setCreditNote($this);
    }

    /**
     * @param  ChildCreditNoteDetail $creditNoteDetail The ChildCreditNoteDetail object to remove.
     * @return $this|ChildCreditNote The current object (for fluent API support)
     */
    public function removeCreditNoteDetail($creditNoteDetail)
    {
        if ($this->getCreditNoteDetails()->contains($creditNoteDetail)) {
            $pos = $this->collCreditNoteDetails->search($creditNoteDetail);
            $this->collCreditNoteDetails->remove($pos);
            if (null === $this->creditNoteDetailsScheduledForDeletion) {
                $this->creditNoteDetailsScheduledForDeletion = clone $this->collCreditNoteDetails;
                $this->creditNoteDetailsScheduledForDeletion->clear();
            }
            $this->creditNoteDetailsScheduledForDeletion[]= clone $creditNoteDetail;
            $creditNoteDetail->setCreditNote(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this CreditNote is new, it will return
     * an empty collection; or if this CreditNote has previously
     * been saved, it will retrieve related CreditNoteDetails from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in CreditNote.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @param      string $joinBehavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return ObjectCollection|ChildCreditNoteDetail[] List of ChildCreditNoteDetail objects
     */
    public function getCreditNoteDetailsJoinOrderProduct(Criteria $criteria = null, $con = null, $joinBehavior = Criteria::LEFT_JOIN)
    {
        $query = ChildCreditNoteDetailQuery::create(null, $criteria);
        $query->joinWith('OrderProduct', $joinBehavior);

        return $this->getCreditNoteDetails($query, $con);
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this CreditNote is new, it will return
     * an empty collection; or if this CreditNote has previously
     * been saved, it will retrieve related CreditNoteDetails from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in CreditNote.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @param      string $joinBehavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return ObjectCollection|ChildCreditNoteDetail[] List of ChildCreditNoteDetail objects
     */
    public function getCreditNoteDetailsJoinTaxRule(Criteria $criteria = null, $con = null, $joinBehavior = Criteria::LEFT_JOIN)
    {
        $query = ChildCreditNoteDetailQuery::create(null, $criteria);
        $query->joinWith('TaxRule', $joinBehavior);

        return $this->getCreditNoteDetails($query, $con);
    }

    /**
     * Clears out the collCreditNoteComments collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return void
     * @see        addCreditNoteComments()
     */
    public function clearCreditNoteComments()
    {
        $this->collCreditNoteComments = null; // important to set this to NULL since that means it is uninitialized
    }

    /**
     * Reset is the collCreditNoteComments collection loaded partially.
     */
    public function resetPartialCreditNoteComments($v = true)
    {
        $this->collCreditNoteCommentsPartial = $v;
    }

    /**
     * Initializes the collCreditNoteComments collection.
     *
     * By default this just sets the collCreditNoteComments collection to an empty array (like clearcollCreditNoteComments());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param      boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initCreditNoteComments($overrideExisting = true)
    {
        if (null !== $this->collCreditNoteComments && !$overrideExisting) {
            return;
        }

        $collectionClassName = CreditNoteCommentTableMap::getTableMap()->getCollectionClassName();

        $this->collCreditNoteComments = new $collectionClassName;
        $this->collCreditNoteComments->setModel('\CreditNote\Model\CreditNoteComment');
    }

    /**
     * Gets an array of ChildCreditNoteComment objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this ChildCreditNote is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @return ObjectCollection|ChildCreditNoteComment[] List of ChildCreditNoteComment objects
     * @throws PropelException
     */
    public function getCreditNoteComments($criteria = null, ConnectionInterface $con = null)
    {
        $partial = $this->collCreditNoteCommentsPartial && !$this->isNew();
        if (null === $this->collCreditNoteComments || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collCreditNoteComments) {
                // return empty collection
                $this->initCreditNoteComments();
            } else {
                $collCreditNoteComments = ChildCreditNoteCommentQuery::create(null, $criteria)
                    ->filterByCreditNote($this)
                    ->find($con);

                if (null !== $criteria) {
                    if (false !== $this->collCreditNoteCommentsPartial && count($collCreditNoteComments)) {
                        $this->initCreditNoteComments(false);

                        foreach ($collCreditNoteComments as $obj) {
                            if (false == $this->collCreditNoteComments->contains($obj)) {
                                $this->collCreditNoteComments->append($obj);
                            }
                        }

                        $this->collCreditNoteCommentsPartial = true;
                    }

                    return $collCreditNoteComments;
                }

                if ($partial && $this->collCreditNoteComments) {
                    foreach ($this->collCreditNoteComments as $obj) {
                        if ($obj->isNew()) {
                            $collCreditNoteComments[] = $obj;
                        }
                    }
                }

                $this->collCreditNoteComments = $collCreditNoteComments;
                $this->collCreditNoteCommentsPartial = false;
            }
        }

        return $this->collCreditNoteComments;
    }

    /**
     * Sets a collection of ChildCreditNoteComment objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param      Collection $creditNoteComments A Propel collection.
     * @param      ConnectionInterface $con Optional connection object
     * @return $this|ChildCreditNote The current object (for fluent API support)
     */
    public function setCreditNoteComments(Collection $creditNoteComments, ConnectionInterface $con = null)
    {
        /** @var ChildCreditNoteComment[] $creditNoteCommentsToDelete */
        $creditNoteCommentsToDelete = $this->getCreditNoteComments(new Criteria(), $con)->diff($creditNoteComments);


        $this->creditNoteCommentsScheduledForDeletion = $creditNoteCommentsToDelete;

        foreach ($creditNoteCommentsToDelete as $creditNoteCommentRemoved) {
            $creditNoteCommentRemoved->setCreditNote(null);
        }

        $this->collCreditNoteComments = null;
        foreach ($creditNoteComments as $creditNoteComment) {
            $this->addCreditNoteComment($creditNoteComment);
        }

        $this->collCreditNoteComments = $creditNoteComments;
        $this->collCreditNoteCommentsPartial = false;

        return $this;
    }

    /**
     * Returns the number of related CreditNoteComment objects.
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct
     * @param      ConnectionInterface $con
     * @return int             Count of related CreditNoteComment objects.
     * @throws PropelException
     */
    public function countCreditNoteComments(Criteria $criteria = null, $distinct = false, ConnectionInterface $con = null)
    {
        $partial = $this->collCreditNoteCommentsPartial && !$this->isNew();
        if (null === $this->collCreditNoteComments || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collCreditNoteComments) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getCreditNoteComments());
            }

            $query = ChildCreditNoteCommentQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByCreditNote($this)
                ->count($con);
        }

        return count($this->collCreditNoteComments);
    }

    /**
     * Method called to associate a ChildCreditNoteComment object to this object
     * through the ChildCreditNoteComment foreign key attribute.
     *
     * @param  ChildCreditNoteComment $l ChildCreditNoteComment
     * @return $this|\CreditNote\Model\CreditNote The current object (for fluent API support)
     */
    public function addCreditNoteComment(ChildCreditNoteComment $l)
    {
        if ($this->collCreditNoteComments === null) {
            $this->initCreditNoteComments();
            $this->collCreditNoteCommentsPartial = true;
        }

        if (!$this->collCreditNoteComments->contains($l)) {
            $this->doAddCreditNoteComment($l);

            if ($this->creditNoteCommentsScheduledForDeletion and $this->creditNoteCommentsScheduledForDeletion->contains($l)) {
                $this->creditNoteCommentsScheduledForDeletion->remove($this->creditNoteCommentsScheduledForDeletion->search($l));
            }
        }

        return $this;
    }

    /**
     * @param ChildCreditNoteComment $creditNoteComment The ChildCreditNoteComment object to add.
     */
    protected function doAddCreditNoteComment($creditNoteComment)
    {
        $this->collCreditNoteComments[]= $creditNoteComment;
        $creditNoteComment->setCreditNote($this);
    }

    /**
     * @param  ChildCreditNoteComment $creditNoteComment The ChildCreditNoteComment object to remove.
     * @return $this|ChildCreditNote The current object (for fluent API support)
     */
    public function removeCreditNoteComment($creditNoteComment)
    {
        if ($this->getCreditNoteComments()->contains($creditNoteComment)) {
            $pos = $this->collCreditNoteComments->search($creditNoteComment);
            $this->collCreditNoteComments->remove($pos);
            if (null === $this->creditNoteCommentsScheduledForDeletion) {
                $this->creditNoteCommentsScheduledForDeletion = clone $this->collCreditNoteComments;
                $this->creditNoteCommentsScheduledForDeletion->clear();
            }
            $this->creditNoteCommentsScheduledForDeletion[]= clone $creditNoteComment;
            $creditNoteComment->setCreditNote(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this CreditNote is new, it will return
     * an empty collection; or if this CreditNote has previously
     * been saved, it will retrieve related CreditNoteComments from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in CreditNote.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @param      string $joinBehavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return ObjectCollection|ChildCreditNoteComment[] List of ChildCreditNoteComment objects
     */
    public function getCreditNoteCommentsJoinAdmin(Criteria $criteria = null, $con = null, $joinBehavior = Criteria::LEFT_JOIN)
    {
        $query = ChildCreditNoteCommentQuery::create(null, $criteria);
        $query->joinWith('Admin', $joinBehavior);

        return $this->getCreditNoteComments($query, $con);
    }

    /**
     * Clears out the collCreditNoteVersions collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return void
     * @see        addCreditNoteVersions()
     */
    public function clearCreditNoteVersions()
    {
        $this->collCreditNoteVersions = null; // important to set this to NULL since that means it is uninitialized
    }

    /**
     * Reset is the collCreditNoteVersions collection loaded partially.
     */
    public function resetPartialCreditNoteVersions($v = true)
    {
        $this->collCreditNoteVersionsPartial = $v;
    }

    /**
     * Initializes the collCreditNoteVersions collection.
     *
     * By default this just sets the collCreditNoteVersions collection to an empty array (like clearcollCreditNoteVersions());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param      boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initCreditNoteVersions($overrideExisting = true)
    {
        if (null !== $this->collCreditNoteVersions && !$overrideExisting) {
            return;
        }

        $collectionClassName = CreditNoteVersionTableMap::getTableMap()->getCollectionClassName();

        $this->collCreditNoteVersions = new $collectionClassName;
        $this->collCreditNoteVersions->setModel('\CreditNote\Model\CreditNoteVersion');
    }

    /**
     * Gets an array of ChildCreditNoteVersion objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this ChildCreditNote is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @return ObjectCollection|ChildCreditNoteVersion[] List of ChildCreditNoteVersion objects
     * @throws PropelException
     */
    public function getCreditNoteVersions($criteria = null, ConnectionInterface $con = null)
    {
        $partial = $this->collCreditNoteVersionsPartial && !$this->isNew();
        if (null === $this->collCreditNoteVersions || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collCreditNoteVersions) {
                // return empty collection
                $this->initCreditNoteVersions();
            } else {
                $collCreditNoteVersions = ChildCreditNoteVersionQuery::create(null, $criteria)
                    ->filterByCreditNote($this)
                    ->find($con);

                if (null !== $criteria) {
                    if (false !== $this->collCreditNoteVersionsPartial && count($collCreditNoteVersions)) {
                        $this->initCreditNoteVersions(false);

                        foreach ($collCreditNoteVersions as $obj) {
                            if (false == $this->collCreditNoteVersions->contains($obj)) {
                                $this->collCreditNoteVersions->append($obj);
                            }
                        }

                        $this->collCreditNoteVersionsPartial = true;
                    }

                    return $collCreditNoteVersions;
                }

                if ($partial && $this->collCreditNoteVersions) {
                    foreach ($this->collCreditNoteVersions as $obj) {
                        if ($obj->isNew()) {
                            $collCreditNoteVersions[] = $obj;
                        }
                    }
                }

                $this->collCreditNoteVersions = $collCreditNoteVersions;
                $this->collCreditNoteVersionsPartial = false;
            }
        }

        return $this->collCreditNoteVersions;
    }

    /**
     * Sets a collection of ChildCreditNoteVersion objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param      Collection $creditNoteVersions A Propel collection.
     * @param      ConnectionInterface $con Optional connection object
     * @return $this|ChildCreditNote The current object (for fluent API support)
     */
    public function setCreditNoteVersions(Collection $creditNoteVersions, ConnectionInterface $con = null)
    {
        /** @var ChildCreditNoteVersion[] $creditNoteVersionsToDelete */
        $creditNoteVersionsToDelete = $this->getCreditNoteVersions(new Criteria(), $con)->diff($creditNoteVersions);


        //since at least one column in the foreign key is at the same time a PK
        //we can not just set a PK to NULL in the lines below. We have to store
        //a backup of all values, so we are able to manipulate these items based on the onDelete value later.
        $this->creditNoteVersionsScheduledForDeletion = clone $creditNoteVersionsToDelete;

        foreach ($creditNoteVersionsToDelete as $creditNoteVersionRemoved) {
            $creditNoteVersionRemoved->setCreditNote(null);
        }

        $this->collCreditNoteVersions = null;
        foreach ($creditNoteVersions as $creditNoteVersion) {
            $this->addCreditNoteVersion($creditNoteVersion);
        }

        $this->collCreditNoteVersions = $creditNoteVersions;
        $this->collCreditNoteVersionsPartial = false;

        return $this;
    }

    /**
     * Returns the number of related CreditNoteVersion objects.
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct
     * @param      ConnectionInterface $con
     * @return int             Count of related CreditNoteVersion objects.
     * @throws PropelException
     */
    public function countCreditNoteVersions(Criteria $criteria = null, $distinct = false, ConnectionInterface $con = null)
    {
        $partial = $this->collCreditNoteVersionsPartial && !$this->isNew();
        if (null === $this->collCreditNoteVersions || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collCreditNoteVersions) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getCreditNoteVersions());
            }

            $query = ChildCreditNoteVersionQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByCreditNote($this)
                ->count($con);
        }

        return count($this->collCreditNoteVersions);
    }

    /**
     * Method called to associate a ChildCreditNoteVersion object to this object
     * through the ChildCreditNoteVersion foreign key attribute.
     *
     * @param  ChildCreditNoteVersion $l ChildCreditNoteVersion
     * @return $this|\CreditNote\Model\CreditNote The current object (for fluent API support)
     */
    public function addCreditNoteVersion(ChildCreditNoteVersion $l)
    {
        if ($this->collCreditNoteVersions === null) {
            $this->initCreditNoteVersions();
            $this->collCreditNoteVersionsPartial = true;
        }

        if (!$this->collCreditNoteVersions->contains($l)) {
            $this->doAddCreditNoteVersion($l);

            if ($this->creditNoteVersionsScheduledForDeletion and $this->creditNoteVersionsScheduledForDeletion->contains($l)) {
                $this->creditNoteVersionsScheduledForDeletion->remove($this->creditNoteVersionsScheduledForDeletion->search($l));
            }
        }

        return $this;
    }

    /**
     * @param ChildCreditNoteVersion $creditNoteVersion The ChildCreditNoteVersion object to add.
     */
    protected function doAddCreditNoteVersion($creditNoteVersion)
    {
        $this->collCreditNoteVersions[]= $creditNoteVersion;
        $creditNoteVersion->setCreditNote($this);
    }

    /**
     * @param  ChildCreditNoteVersion $creditNoteVersion The ChildCreditNoteVersion object to remove.
     * @return $this|ChildCreditNote The current object (for fluent API support)
     */
    public function removeCreditNoteVersion($creditNoteVersion)
    {
        if ($this->getCreditNoteVersions()->contains($creditNoteVersion)) {
            $pos = $this->collCreditNoteVersions->search($creditNoteVersion);
            $this->collCreditNoteVersions->remove($pos);
            if (null === $this->creditNoteVersionsScheduledForDeletion) {
                $this->creditNoteVersionsScheduledForDeletion = clone $this->collCreditNoteVersions;
                $this->creditNoteVersionsScheduledForDeletion->clear();
            }
            $this->creditNoteVersionsScheduledForDeletion[]= clone $creditNoteVersion;
            $creditNoteVersion->setCreditNote(null);
        }

        return $this;
    }

    /**
     * Clears the current object, sets all attributes to their default values and removes
     * outgoing references as well as back-references (from other objects to this one. Results probably in a database
     * change of those foreign objects when you call `save` there).
     */
    public function clear()
    {
        if (null !== $this->aOrder) {
            $this->aOrder->removeCreditNote($this);
        }
        if (null !== $this->aCustomer) {
            $this->aCustomer->removeCreditNote($this);
        }
        if (null !== $this->aCreditNoteRelatedByParentId) {
            $this->aCreditNoteRelatedByParentId->removeCreditNoteRelatedById($this);
        }
        if (null !== $this->aCreditNoteType) {
            $this->aCreditNoteType->removeCreditNote($this);
        }
        if (null !== $this->aCreditNoteStatus) {
            $this->aCreditNoteStatus->removeCreditNote($this);
        }
        if (null !== $this->aCurrency) {
            $this->aCurrency->removeCreditNote($this);
        }
        if (null !== $this->aCreditNoteAddress) {
            $this->aCreditNoteAddress->removeCreditNote($this);
        }
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
        $this->alreadyInSave = false;
        $this->clearAllReferences();
        $this->applyDefaultValues();
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
            if ($this->collCreditNotesRelatedById) {
                foreach ($this->collCreditNotesRelatedById as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collOrderCreditNotes) {
                foreach ($this->collOrderCreditNotes as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collCartCreditNotes) {
                foreach ($this->collCartCreditNotes as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collCreditNoteDetails) {
                foreach ($this->collCreditNoteDetails as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collCreditNoteComments) {
                foreach ($this->collCreditNoteComments as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collCreditNoteVersions) {
                foreach ($this->collCreditNoteVersions as $o) {
                    $o->clearAllReferences($deep);
                }
            }
        } // if ($deep)

        $this->collCreditNotesRelatedById = null;
        $this->collOrderCreditNotes = null;
        $this->collCartCreditNotes = null;
        $this->collCreditNoteDetails = null;
        $this->collCreditNoteComments = null;
        $this->collCreditNoteVersions = null;
        $this->aOrder = null;
        $this->aCustomer = null;
        $this->aCreditNoteRelatedByParentId = null;
        $this->aCreditNoteType = null;
        $this->aCreditNoteStatus = null;
        $this->aCurrency = null;
        $this->aCreditNoteAddress = null;
    }

    /**
     * Return the string representation of this object
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->exportTo(CreditNoteTableMap::DEFAULT_STRING_FORMAT);
    }

    // timestampable behavior

    /**
     * Mark the current object so that the update date doesn't get updated during next save
     *
     * @return     $this|ChildCreditNote The current object (for fluent API support)
     */
    public function keepUpdateDateUnchanged()
    {
        $this->modifiedColumns[CreditNoteTableMap::COL_UPDATED_AT] = true;

        return $this;
    }

    // versionable behavior

    /**
     * Enforce a new Version of this object upon next save.
     *
     * @return $this|\CreditNote\Model\CreditNote
     */
    public function enforceVersioning()
    {
        $this->enforceVersion = true;

        return $this;
    }

    /**
     * Checks whether the current state must be recorded as a version
     *
     * @param   ConnectionInterface $con The ConnectionInterface connection to use.
     * @return  boolean
     */
    public function isVersioningNecessary($con = null)
    {
        if ($this->alreadyInSave) {
            return false;
        }

        if ($this->enforceVersion) {
            return true;
        }

        if (ChildCreditNoteQuery::isVersioningEnabled() && ($this->isNew() || $this->isModified()) || $this->isDeleted()) {
            return true;
        }
        if (null !== ($object = $this->getOrder($con)) && $object->isVersioningNecessary($con)) {
            return true;
        }

        if (null !== ($object = $this->getCustomer($con)) && $object->isVersioningNecessary($con)) {
            return true;
        }

        if (null !== ($object = $this->getCreditNoteRelatedByParentId($con)) && $object->isVersioningNecessary($con)) {
            return true;
        }

        if ($this->collCreditNotesRelatedById) {

            // to avoid infinite loops, emulate in save
            $this->alreadyInSave = true;

            foreach ($this->getCreditNotesRelatedById(null, $con) as $relatedObject) {

                if ($relatedObject->isVersioningNecessary($con)) {

                    $this->alreadyInSave = false;
                    return true;
                }
            }
            $this->alreadyInSave = false;
        }


        return false;
    }

    /**
     * Creates a version of the current object and saves it.
     *
     * @param   ConnectionInterface $con The ConnectionInterface connection to use.
     *
     * @return  ChildCreditNoteVersion A version object
     */
    public function addVersion($con = null)
    {
        $this->enforceVersion = false;

        $version = new ChildCreditNoteVersion();
        $version->setId($this->getId());
        $version->setRef($this->getRef());
        $version->setInvoiceRef($this->getInvoiceRef());
        $version->setInvoiceAddressId($this->getInvoiceAddressId());
        $version->setInvoiceDate($this->getInvoiceDate());
        $version->setOrderId($this->getOrderId());
        $version->setCustomerId($this->getCustomerId());
        $version->setParentId($this->getParentId());
        $version->setTypeId($this->getTypeId());
        $version->setStatusId($this->getStatusId());
        $version->setCurrencyId($this->getCurrencyId());
        $version->setCurrencyRate($this->getCurrencyRate());
        $version->setTotalPrice($this->getTotalPrice());
        $version->setTotalPriceWithTax($this->getTotalPriceWithTax());
        $version->setDiscountWithoutTax($this->getDiscountWithoutTax());
        $version->setDiscountWithTax($this->getDiscountWithTax());
        $version->setAllowPartialUse($this->getAllowPartialUse());
        $version->setCreatedAt($this->getCreatedAt());
        $version->setUpdatedAt($this->getUpdatedAt());
        $version->setVersion($this->getVersion());
        $version->setVersionCreatedAt($this->getVersionCreatedAt());
        $version->setVersionCreatedBy($this->getVersionCreatedBy());
        $version->setCreditNote($this);
        if (($related = $this->getOrder(null, $con)) && $related->getVersion()) {
            $version->setOrderIdVersion($related->getVersion());
        }
        if (($related = $this->getCustomer(null, $con)) && $related->getVersion()) {
            $version->setCustomerIdVersion($related->getVersion());
        }
        if (($related = $this->getCreditNoteRelatedByParentId(null, $con)) && $related->getVersion()) {
            $version->setParentIdVersion($related->getVersion());
        }
        $object = $this->getCreditNotesRelatedById(null, $con);


        if ($object && $relateds = $object->toKeyValue('Id', 'Version')) {
            $version->setCreditNoteIds(array_keys($relateds));
            $version->setCreditNoteVersions(array_values($relateds));
        }

        $version->save($con);

        return $version;
    }

    /**
     * Sets the properties of the current object to the value they had at a specific version
     *
     * @param   integer $versionNumber The version number to read
     * @param   ConnectionInterface $con The ConnectionInterface connection to use.
     *
     * @return  $this|ChildCreditNote The current object (for fluent API support)
     */
    public function toVersion($versionNumber, $con = null)
    {
        $version = $this->getOneVersion($versionNumber, $con);
        if (!$version) {
            throw new PropelException(sprintf('No ChildCreditNote object found with version %d', $version));
        }
        $this->populateFromVersion($version, $con);

        return $this;
    }

    /**
     * Sets the properties of the current object to the value they had at a specific version
     *
     * @param ChildCreditNoteVersion $version The version object to use
     * @param ConnectionInterface   $con the connection to use
     * @param array                 $loadedObjects objects that been loaded in a chain of populateFromVersion calls on referrer or fk objects.
     *
     * @return $this|ChildCreditNote The current object (for fluent API support)
     */
    public function populateFromVersion($version, $con = null, &$loadedObjects = array())
    {
        $loadedObjects['ChildCreditNote'][$version->getId()][$version->getVersion()] = $this;
        $this->setId($version->getId());
        $this->setRef($version->getRef());
        $this->setInvoiceRef($version->getInvoiceRef());
        $this->setInvoiceAddressId($version->getInvoiceAddressId());
        $this->setInvoiceDate($version->getInvoiceDate());
        $this->setOrderId($version->getOrderId());
        $this->setCustomerId($version->getCustomerId());
        $this->setParentId($version->getParentId());
        $this->setTypeId($version->getTypeId());
        $this->setStatusId($version->getStatusId());
        $this->setCurrencyId($version->getCurrencyId());
        $this->setCurrencyRate($version->getCurrencyRate());
        $this->setTotalPrice($version->getTotalPrice());
        $this->setTotalPriceWithTax($version->getTotalPriceWithTax());
        $this->setDiscountWithoutTax($version->getDiscountWithoutTax());
        $this->setDiscountWithTax($version->getDiscountWithTax());
        $this->setAllowPartialUse($version->getAllowPartialUse());
        $this->setCreatedAt($version->getCreatedAt());
        $this->setUpdatedAt($version->getUpdatedAt());
        $this->setVersion($version->getVersion());
        $this->setVersionCreatedAt($version->getVersionCreatedAt());
        $this->setVersionCreatedBy($version->getVersionCreatedBy());
        if ($fkValue = $version->getOrderId()) {
            if (isset($loadedObjects['Order']) && isset($loadedObjects['Order'][$fkValue]) && isset($loadedObjects['Order'][$fkValue][$version->getOrderIdVersion()])) {
                $related = $loadedObjects['Order'][$fkValue][$version->getOrderIdVersion()];
            } else {
                $related = new Order();
                $relatedVersion = OrderVersionQuery::create()
                    ->filterById($fkValue)
                    ->filterByVersionCreatedBy($version->getOrderIdVersion())
                    ->findOne($con);
                $related->populateFromVersion($relatedVersion, $con, $loadedObjects);
                $related->setNew(false);
            }
            $this->setOrder($related);
        }
        if ($fkValue = $version->getCustomerId()) {
            if (isset($loadedObjects['Customer']) && isset($loadedObjects['Customer'][$fkValue]) && isset($loadedObjects['Customer'][$fkValue][$version->getCustomerIdVersion()])) {
                $related = $loadedObjects['Customer'][$fkValue][$version->getCustomerIdVersion()];
            } else {
                $related = new Customer();
                $relatedVersion = CustomerVersionQuery::create()
                    ->filterById($fkValue)
                    ->filterByVersionCreatedBy($version->getCustomerIdVersion())
                    ->findOne($con);
                $related->populateFromVersion($relatedVersion, $con, $loadedObjects);
                $related->setNew(false);
            }
            $this->setCustomer($related);
        }
        if ($fkValue = $version->getParentId()) {
            if (isset($loadedObjects['ChildCreditNote']) && isset($loadedObjects['ChildCreditNote'][$fkValue]) && isset($loadedObjects['ChildCreditNote'][$fkValue][$version->getParentIdVersion()])) {
                $related = $loadedObjects['ChildCreditNote'][$fkValue][$version->getParentIdVersion()];
            } else {
                $related = new ChildCreditNote();
                $relatedVersion = ChildCreditNoteVersionQuery::create()
                    ->filterById($fkValue)
                    ->filterByVersionCreatedBy($version->getParentIdVersion())
                    ->findOne($con);
                $related->populateFromVersion($relatedVersion, $con, $loadedObjects);
                $related->setNew(false);
            }
            $this->setCreditNoteRelatedByParentId($related);
        }
        if ($fkValues = $version->getCreditNoteIds()) {
            $this->clearCreditNotesRelatedById();
            $fkVersions = $version->getCreditNoteVersions();
            $query = ChildCreditNoteVersionQuery::create();
            foreach ($fkValues as $key => $value) {
                $c1 = $query->getNewCriterion(CreditNoteVersionTableMap::COL_ID, $value);
                $c2 = $query->getNewCriterion(CreditNoteVersionTableMap::COL_VERSION, $fkVersions[$key]);
                $c1->addAnd($c2);
                $query->addOr($c1);
            }
            foreach ($query->find($con) as $relatedVersion) {
                if (isset($loadedObjects['ChildCreditNote']) && isset($loadedObjects['ChildCreditNote'][$relatedVersion->getId()]) && isset($loadedObjects['ChildCreditNote'][$relatedVersion->getId()][$relatedVersion->getVersion()])) {
                    $related = $loadedObjects['ChildCreditNote'][$relatedVersion->getId()][$relatedVersion->getVersion()];
                } else {
                    $related = new ChildCreditNote();
                    $related->populateFromVersion($relatedVersion, $con, $loadedObjects);
                    $related->setNew(false);
                }
                $this->addCreditNoteRelatedById($related);
                $this->collCreditNotesRelatedByIdPartial = false;
            }
        }

        return $this;
    }

    /**
     * Gets the latest persisted version number for the current object
     *
     * @param   ConnectionInterface $con The ConnectionInterface connection to use.
     *
     * @return  integer
     */
    public function getLastVersionNumber($con = null)
    {
        $v = ChildCreditNoteVersionQuery::create()
            ->filterByCreditNote($this)
            ->orderByVersion('desc')
            ->findOne($con);
        if (!$v) {
            return 0;
        }

        return $v->getVersion();
    }

    /**
     * Checks whether the current object is the latest one
     *
     * @param   ConnectionInterface $con The ConnectionInterface connection to use.
     *
     * @return  Boolean
     */
    public function isLastVersion($con = null)
    {
        return $this->getLastVersionNumber($con) == $this->getVersion();
    }

    /**
     * Retrieves a version object for this entity and a version number
     *
     * @param   integer $versionNumber The version number to read
     * @param   ConnectionInterface $con The ConnectionInterface connection to use.
     *
     * @return  ChildCreditNoteVersion A version object
     */
    public function getOneVersion($versionNumber, $con = null)
    {
        return ChildCreditNoteVersionQuery::create()
            ->filterByCreditNote($this)
            ->filterByVersion($versionNumber)
            ->findOne($con);
    }

    /**
     * Gets all the versions of this object, in incremental order
     *
     * @param   ConnectionInterface $con The ConnectionInterface connection to use.
     *
     * @return  ObjectCollection|ChildCreditNoteVersion[] A list of ChildCreditNoteVersion objects
     */
    public function getAllVersions($con = null)
    {
        $criteria = new Criteria();
        $criteria->addAscendingOrderByColumn(CreditNoteVersionTableMap::COL_VERSION);

        return $this->getCreditNoteVersions($criteria, $con);
    }

    /**
     * Compares the current object with another of its version.
     * <code>
     * print_r($book->compareVersion(1));
     * => array(
     *   '1' => array('Title' => 'Book title at version 1'),
     *   '2' => array('Title' => 'Book title at version 2')
     * );
     * </code>
     *
     * @param   integer             $versionNumber
     * @param   string              $keys Main key used for the result diff (versions|columns)
     * @param   ConnectionInterface $con The ConnectionInterface connection to use.
     * @param   array               $ignoredColumns  The columns to exclude from the diff.
     *
     * @return  array A list of differences
     */
    public function compareVersion($versionNumber, $keys = 'columns', $con = null, $ignoredColumns = array())
    {
        $fromVersion = $this->toArray();
        $toVersion = $this->getOneVersion($versionNumber, $con)->toArray();

        return $this->computeDiff($fromVersion, $toVersion, $keys, $ignoredColumns);
    }

    /**
     * Compares two versions of the current object.
     * <code>
     * print_r($book->compareVersions(1, 2));
     * => array(
     *   '1' => array('Title' => 'Book title at version 1'),
     *   '2' => array('Title' => 'Book title at version 2')
     * );
     * </code>
     *
     * @param   integer             $fromVersionNumber
     * @param   integer             $toVersionNumber
     * @param   string              $keys Main key used for the result diff (versions|columns)
     * @param   ConnectionInterface $con The ConnectionInterface connection to use.
     * @param   array               $ignoredColumns  The columns to exclude from the diff.
     *
     * @return  array A list of differences
     */
    public function compareVersions($fromVersionNumber, $toVersionNumber, $keys = 'columns', $con = null, $ignoredColumns = array())
    {
        $fromVersion = $this->getOneVersion($fromVersionNumber, $con)->toArray();
        $toVersion = $this->getOneVersion($toVersionNumber, $con)->toArray();

        return $this->computeDiff($fromVersion, $toVersion, $keys, $ignoredColumns);
    }

    /**
     * Computes the diff between two versions.
     * <code>
     * print_r($book->computeDiff(1, 2));
     * => array(
     *   '1' => array('Title' => 'Book title at version 1'),
     *   '2' => array('Title' => 'Book title at version 2')
     * );
     * </code>
     *
     * @param   array     $fromVersion     An array representing the original version.
     * @param   array     $toVersion       An array representing the destination version.
     * @param   string    $keys            Main key used for the result diff (versions|columns).
     * @param   array     $ignoredColumns  The columns to exclude from the diff.
     *
     * @return  array A list of differences
     */
    protected function computeDiff($fromVersion, $toVersion, $keys = 'columns', $ignoredColumns = array())
    {
        $fromVersionNumber = $fromVersion['Version'];
        $toVersionNumber = $toVersion['Version'];
        $ignoredColumns = array_merge(array(
            'Version',
            'VersionCreatedAt',
            'VersionCreatedBy',
        ), $ignoredColumns);
        $diff = array();
        foreach ($fromVersion as $key => $value) {
            if (\in_array($key, $ignoredColumns)) {
                continue;
            }
            if ($toVersion[$key] != $value) {
                switch ($keys) {
                    case 'versions':
                        $diff[$fromVersionNumber][$key] = $value;
                        $diff[$toVersionNumber][$key] = $toVersion[$key];
                        break;
                    default:
                        $diff[$key] = array(
                            $fromVersionNumber => $value,
                            $toVersionNumber => $toVersion[$key],
                        );
                        break;
                }
            }
        }

        return $diff;
    }
    /**
     * retrieve the last $number versions.
     *
     * @param  Integer             $number The number of record to return.
     * @param  Criteria            $criteria The Criteria object containing modified values.
     * @param  ConnectionInterface $con The ConnectionInterface connection to use.
     *
     * @return PropelCollection|\CreditNote\Model\CreditNoteVersion[] List of \CreditNote\Model\CreditNoteVersion objects
     */
    public function getLastVersions($number = 10, $criteria = null, $con = null)
    {
        $criteria = ChildCreditNoteVersionQuery::create(null, $criteria);
        $criteria->addDescendingOrderByColumn(CreditNoteVersionTableMap::COL_VERSION);
        $criteria->limit($number);

        return $this->getCreditNoteVersions($criteria, $con);
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
            $event = new CreditNoteEvent($this);

            $con->getEventDispatcher()
                ->dispatch(
                    CreditNoteEvent::PRE_SAVE,
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
                    CreditNoteEvent::POST_SAVE,
                    new CreditNoteEvent($this)
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
            $event = new CreditNoteEvent($this);
            $con->getEventDispatcher()
                ->dispatch(
                    CreditNoteEvent::PRE_INSERT,
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
                    CreditNoteEvent::POST_INSERT,
                    new CreditNoteEvent($this)
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
            $event = new CreditNoteEvent($this);

            $con->getEventDispatcher()
                ->dispatch(
                    CreditNoteEvent::PRE_UPDATE,
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
                    CreditNoteEvent::POST_UPDATE,
                    new CreditNoteEvent($this)
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
            $event = new CreditNoteEvent($this);

            $con->getEventDispatcher()
                ->dispatch(
                    CreditNoteEvent::PRE_DELETE,
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
                    CreditNoteEvent::POST_DELETE,
                    new CreditNoteEvent($this)
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
