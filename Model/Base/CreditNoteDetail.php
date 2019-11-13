<?php

namespace CreditNote\Model\Base;

use \DateTime;
use \Exception;
use \PDO;
use CreditNote\Model\CreditNote as ChildCreditNote;
use CreditNote\Model\CreditNoteDetail as ChildCreditNoteDetail;
use CreditNote\Model\CreditNoteDetailQuery as ChildCreditNoteDetailQuery;
use CreditNote\Model\CreditNoteQuery as ChildCreditNoteQuery;
use CreditNote\Model\Map\CreditNoteDetailTableMap;
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
use Thelia\Model\OrderProduct as ChildOrderProduct;
use Thelia\Model\TaxRule as ChildTaxRule;
use Thelia\Model\OrderProductQuery;
use Thelia\Model\TaxRuleQuery;

abstract class CreditNoteDetail implements ActiveRecordInterface
{
    /**
     * TableMap class name
     */
    const TABLE_MAP = '\\CreditNote\\Model\\Map\\CreditNoteDetailTableMap';


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
     * The value for the credit_note_id field.
     * @var        int
     */
    protected $credit_note_id;

    /**
     * The value for the price field.
     * Note: this column has a database default value of: '0.000000'
     * @var        string
     */
    protected $price;

    /**
     * The value for the price_with_tax field.
     * Note: this column has a database default value of: '0.000000'
     * @var        string
     */
    protected $price_with_tax;

    /**
     * The value for the tax_rule_id field.
     * @var        int
     */
    protected $tax_rule_id;

    /**
     * The value for the order_product_id field.
     * @var        int
     */
    protected $order_product_id;

    /**
     * The value for the type field.
     * @var        string
     */
    protected $type;

    /**
     * The value for the quantity field.
     * Note: this column has a database default value of: 0
     * @var        int
     */
    protected $quantity;

    /**
     * The value for the title field.
     * @var        string
     */
    protected $title;

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
     * @var        CreditNote
     */
    protected $aCreditNote;

    /**
     * @var        OrderProduct
     */
    protected $aOrderProduct;

    /**
     * @var        TaxRule
     */
    protected $aTaxRule;

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
        $this->price = '0.000000';
        $this->price_with_tax = '0.000000';
        $this->quantity = 0;
    }

    /**
     * Initializes internal state of CreditNote\Model\Base\CreditNoteDetail object.
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
     * Compares this with another <code>CreditNoteDetail</code> instance.  If
     * <code>obj</code> is an instance of <code>CreditNoteDetail</code>, delegates to
     * <code>equals(CreditNoteDetail)</code>.  Otherwise, returns <code>false</code>.
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
     * @return CreditNoteDetail The current object, for fluid interface
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
     * @return CreditNoteDetail The current object, for fluid interface
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
     * Get the [credit_note_id] column value.
     *
     * @return   int
     */
    public function getCreditNoteId()
    {

        return $this->credit_note_id;
    }

    /**
     * Get the [price] column value.
     *
     * @return   string
     */
    public function getPrice()
    {

        return $this->price;
    }

    /**
     * Get the [price_with_tax] column value.
     *
     * @return   string
     */
    public function getPriceWithTax()
    {

        return $this->price_with_tax;
    }

    /**
     * Get the [tax_rule_id] column value.
     *
     * @return   int
     */
    public function getTaxRuleId()
    {

        return $this->tax_rule_id;
    }

    /**
     * Get the [order_product_id] column value.
     *
     * @return   int
     */
    public function getOrderProductId()
    {

        return $this->order_product_id;
    }

    /**
     * Get the [type] column value.
     *
     * @return   string
     */
    public function getType()
    {

        return $this->type;
    }

    /**
     * Get the [quantity] column value.
     *
     * @return   int
     */
    public function getQuantity()
    {

        return $this->quantity;
    }

    /**
     * Get the [title] column value.
     *
     * @return   string
     */
    public function getTitle()
    {

        return $this->title;
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
     * Set the value of [id] column.
     *
     * @param      int $v new value
     * @return   \CreditNote\Model\CreditNoteDetail The current object (for fluent API support)
     */
    public function setId($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->id !== $v) {
            $this->id = $v;
            $this->modifiedColumns[CreditNoteDetailTableMap::ID] = true;
        }


        return $this;
    } // setId()

    /**
     * Set the value of [credit_note_id] column.
     *
     * @param      int $v new value
     * @return   \CreditNote\Model\CreditNoteDetail The current object (for fluent API support)
     */
    public function setCreditNoteId($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->credit_note_id !== $v) {
            $this->credit_note_id = $v;
            $this->modifiedColumns[CreditNoteDetailTableMap::CREDIT_NOTE_ID] = true;
        }

        if ($this->aCreditNote !== null && $this->aCreditNote->getId() !== $v) {
            $this->aCreditNote = null;
        }


        return $this;
    } // setCreditNoteId()

    /**
     * Set the value of [price] column.
     *
     * @param      string $v new value
     * @return   \CreditNote\Model\CreditNoteDetail The current object (for fluent API support)
     */
    public function setPrice($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->price !== $v) {
            $this->price = $v;
            $this->modifiedColumns[CreditNoteDetailTableMap::PRICE] = true;
        }


        return $this;
    } // setPrice()

    /**
     * Set the value of [price_with_tax] column.
     *
     * @param      string $v new value
     * @return   \CreditNote\Model\CreditNoteDetail The current object (for fluent API support)
     */
    public function setPriceWithTax($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->price_with_tax !== $v) {
            $this->price_with_tax = $v;
            $this->modifiedColumns[CreditNoteDetailTableMap::PRICE_WITH_TAX] = true;
        }


        return $this;
    } // setPriceWithTax()

    /**
     * Set the value of [tax_rule_id] column.
     *
     * @param      int $v new value
     * @return   \CreditNote\Model\CreditNoteDetail The current object (for fluent API support)
     */
    public function setTaxRuleId($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->tax_rule_id !== $v) {
            $this->tax_rule_id = $v;
            $this->modifiedColumns[CreditNoteDetailTableMap::TAX_RULE_ID] = true;
        }

        if ($this->aTaxRule !== null && $this->aTaxRule->getId() !== $v) {
            $this->aTaxRule = null;
        }


        return $this;
    } // setTaxRuleId()

    /**
     * Set the value of [order_product_id] column.
     *
     * @param      int $v new value
     * @return   \CreditNote\Model\CreditNoteDetail The current object (for fluent API support)
     */
    public function setOrderProductId($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->order_product_id !== $v) {
            $this->order_product_id = $v;
            $this->modifiedColumns[CreditNoteDetailTableMap::ORDER_PRODUCT_ID] = true;
        }

        if ($this->aOrderProduct !== null && $this->aOrderProduct->getId() !== $v) {
            $this->aOrderProduct = null;
        }


        return $this;
    } // setOrderProductId()

    /**
     * Set the value of [type] column.
     *
     * @param      string $v new value
     * @return   \CreditNote\Model\CreditNoteDetail The current object (for fluent API support)
     */
    public function setType($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->type !== $v) {
            $this->type = $v;
            $this->modifiedColumns[CreditNoteDetailTableMap::TYPE] = true;
        }


        return $this;
    } // setType()

    /**
     * Set the value of [quantity] column.
     *
     * @param      int $v new value
     * @return   \CreditNote\Model\CreditNoteDetail The current object (for fluent API support)
     */
    public function setQuantity($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->quantity !== $v) {
            $this->quantity = $v;
            $this->modifiedColumns[CreditNoteDetailTableMap::QUANTITY] = true;
        }


        return $this;
    } // setQuantity()

    /**
     * Set the value of [title] column.
     *
     * @param      string $v new value
     * @return   \CreditNote\Model\CreditNoteDetail The current object (for fluent API support)
     */
    public function setTitle($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->title !== $v) {
            $this->title = $v;
            $this->modifiedColumns[CreditNoteDetailTableMap::TITLE] = true;
        }


        return $this;
    } // setTitle()

    /**
     * Sets the value of [created_at] column to a normalized version of the date/time value specified.
     *
     * @param      mixed $v string, integer (timestamp), or \DateTime value.
     *               Empty strings are treated as NULL.
     * @return   \CreditNote\Model\CreditNoteDetail The current object (for fluent API support)
     */
    public function setCreatedAt($v)
    {
        $dt = PropelDateTime::newInstance($v, null, '\DateTime');
        if ($this->created_at !== null || $dt !== null) {
            if ($dt !== $this->created_at) {
                $this->created_at = $dt;
                $this->modifiedColumns[CreditNoteDetailTableMap::CREATED_AT] = true;
            }
        } // if either are not null


        return $this;
    } // setCreatedAt()

    /**
     * Sets the value of [updated_at] column to a normalized version of the date/time value specified.
     *
     * @param      mixed $v string, integer (timestamp), or \DateTime value.
     *               Empty strings are treated as NULL.
     * @return   \CreditNote\Model\CreditNoteDetail The current object (for fluent API support)
     */
    public function setUpdatedAt($v)
    {
        $dt = PropelDateTime::newInstance($v, null, '\DateTime');
        if ($this->updated_at !== null || $dt !== null) {
            if ($dt !== $this->updated_at) {
                $this->updated_at = $dt;
                $this->modifiedColumns[CreditNoteDetailTableMap::UPDATED_AT] = true;
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
            if ($this->price !== '0.000000') {
                return false;
            }

            if ($this->price_with_tax !== '0.000000') {
                return false;
            }

            if ($this->quantity !== 0) {
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


            $col = $row[TableMap::TYPE_NUM == $indexType ? 0 + $startcol : CreditNoteDetailTableMap::translateFieldName('Id', TableMap::TYPE_PHPNAME, $indexType)];
            $this->id = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 1 + $startcol : CreditNoteDetailTableMap::translateFieldName('CreditNoteId', TableMap::TYPE_PHPNAME, $indexType)];
            $this->credit_note_id = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 2 + $startcol : CreditNoteDetailTableMap::translateFieldName('Price', TableMap::TYPE_PHPNAME, $indexType)];
            $this->price = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 3 + $startcol : CreditNoteDetailTableMap::translateFieldName('PriceWithTax', TableMap::TYPE_PHPNAME, $indexType)];
            $this->price_with_tax = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 4 + $startcol : CreditNoteDetailTableMap::translateFieldName('TaxRuleId', TableMap::TYPE_PHPNAME, $indexType)];
            $this->tax_rule_id = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 5 + $startcol : CreditNoteDetailTableMap::translateFieldName('OrderProductId', TableMap::TYPE_PHPNAME, $indexType)];
            $this->order_product_id = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 6 + $startcol : CreditNoteDetailTableMap::translateFieldName('Type', TableMap::TYPE_PHPNAME, $indexType)];
            $this->type = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 7 + $startcol : CreditNoteDetailTableMap::translateFieldName('Quantity', TableMap::TYPE_PHPNAME, $indexType)];
            $this->quantity = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 8 + $startcol : CreditNoteDetailTableMap::translateFieldName('Title', TableMap::TYPE_PHPNAME, $indexType)];
            $this->title = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 9 + $startcol : CreditNoteDetailTableMap::translateFieldName('CreatedAt', TableMap::TYPE_PHPNAME, $indexType)];
            if ($col === '0000-00-00 00:00:00') {
                $col = null;
            }
            $this->created_at = (null !== $col) ? PropelDateTime::newInstance($col, null, '\DateTime') : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 10 + $startcol : CreditNoteDetailTableMap::translateFieldName('UpdatedAt', TableMap::TYPE_PHPNAME, $indexType)];
            if ($col === '0000-00-00 00:00:00') {
                $col = null;
            }
            $this->updated_at = (null !== $col) ? PropelDateTime::newInstance($col, null, '\DateTime') : null;
            $this->resetModified();

            $this->setNew(false);

            if ($rehydrate) {
                $this->ensureConsistency();
            }

            return $startcol + 11; // 11 = CreditNoteDetailTableMap::NUM_HYDRATE_COLUMNS.

        } catch (Exception $e) {
            throw new PropelException("Error populating \CreditNote\Model\CreditNoteDetail object", 0, $e);
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
        if ($this->aCreditNote !== null && $this->credit_note_id !== $this->aCreditNote->getId()) {
            $this->aCreditNote = null;
        }
        if ($this->aTaxRule !== null && $this->tax_rule_id !== $this->aTaxRule->getId()) {
            $this->aTaxRule = null;
        }
        if ($this->aOrderProduct !== null && $this->order_product_id !== $this->aOrderProduct->getId()) {
            $this->aOrderProduct = null;
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
            $con = Propel::getServiceContainer()->getReadConnection(CreditNoteDetailTableMap::DATABASE_NAME);
        }

        // We don't need to alter the object instance pool; we're just modifying this instance
        // already in the pool.

        $dataFetcher = ChildCreditNoteDetailQuery::create(null, $this->buildPkeyCriteria())->setFormatter(ModelCriteria::FORMAT_STATEMENT)->find($con);
        $row = $dataFetcher->fetch();
        $dataFetcher->close();
        if (!$row) {
            throw new PropelException('Cannot find matching row in the database to reload object values.');
        }
        $this->hydrate($row, 0, true, $dataFetcher->getIndexType()); // rehydrate

        if ($deep) {  // also de-associate any related objects?

            $this->aCreditNote = null;
            $this->aOrderProduct = null;
            $this->aTaxRule = null;
        } // if (deep)
    }

    /**
     * Removes this object from datastore and sets delete attribute.
     *
     * @param      ConnectionInterface $con
     * @return void
     * @throws PropelException
     * @see CreditNoteDetail::setDeleted()
     * @see CreditNoteDetail::isDeleted()
     */
    public function delete(ConnectionInterface $con = null)
    {
        if ($this->isDeleted()) {
            throw new PropelException("This object has already been deleted.");
        }

        if ($con === null) {
            $con = Propel::getServiceContainer()->getWriteConnection(CreditNoteDetailTableMap::DATABASE_NAME);
        }

        $con->beginTransaction();
        try {
            $deleteQuery = ChildCreditNoteDetailQuery::create()
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
            $con = Propel::getServiceContainer()->getWriteConnection(CreditNoteDetailTableMap::DATABASE_NAME);
        }

        $con->beginTransaction();
        $isInsert = $this->isNew();
        try {
            $ret = $this->preSave($con);
            if ($isInsert) {
                $ret = $ret && $this->preInsert($con);
                // timestampable behavior
                if (!$this->isColumnModified(CreditNoteDetailTableMap::CREATED_AT)) {
                    $this->setCreatedAt(time());
                }
                if (!$this->isColumnModified(CreditNoteDetailTableMap::UPDATED_AT)) {
                    $this->setUpdatedAt(time());
                }
            } else {
                $ret = $ret && $this->preUpdate($con);
                // timestampable behavior
                if ($this->isModified() && !$this->isColumnModified(CreditNoteDetailTableMap::UPDATED_AT)) {
                    $this->setUpdatedAt(time());
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
                CreditNoteDetailTableMap::addInstanceToPool($this);
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

            if ($this->aOrderProduct !== null) {
                if ($this->aOrderProduct->isModified() || $this->aOrderProduct->isNew()) {
                    $affectedRows += $this->aOrderProduct->save($con);
                }
                $this->setOrderProduct($this->aOrderProduct);
            }

            if ($this->aTaxRule !== null) {
                if ($this->aTaxRule->isModified() || $this->aTaxRule->isNew()) {
                    $affectedRows += $this->aTaxRule->save($con);
                }
                $this->setTaxRule($this->aTaxRule);
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

        $this->modifiedColumns[CreditNoteDetailTableMap::ID] = true;
        if (null !== $this->id) {
            throw new PropelException('Cannot insert a value for auto-increment primary key (' . CreditNoteDetailTableMap::ID . ')');
        }

         // check the columns in natural order for more readable SQL queries
        if ($this->isColumnModified(CreditNoteDetailTableMap::ID)) {
            $modifiedColumns[':p' . $index++]  = 'ID';
        }
        if ($this->isColumnModified(CreditNoteDetailTableMap::CREDIT_NOTE_ID)) {
            $modifiedColumns[':p' . $index++]  = 'CREDIT_NOTE_ID';
        }
        if ($this->isColumnModified(CreditNoteDetailTableMap::PRICE)) {
            $modifiedColumns[':p' . $index++]  = 'PRICE';
        }
        if ($this->isColumnModified(CreditNoteDetailTableMap::PRICE_WITH_TAX)) {
            $modifiedColumns[':p' . $index++]  = 'PRICE_WITH_TAX';
        }
        if ($this->isColumnModified(CreditNoteDetailTableMap::TAX_RULE_ID)) {
            $modifiedColumns[':p' . $index++]  = 'TAX_RULE_ID';
        }
        if ($this->isColumnModified(CreditNoteDetailTableMap::ORDER_PRODUCT_ID)) {
            $modifiedColumns[':p' . $index++]  = 'ORDER_PRODUCT_ID';
        }
        if ($this->isColumnModified(CreditNoteDetailTableMap::TYPE)) {
            $modifiedColumns[':p' . $index++]  = 'TYPE';
        }
        if ($this->isColumnModified(CreditNoteDetailTableMap::QUANTITY)) {
            $modifiedColumns[':p' . $index++]  = 'QUANTITY';
        }
        if ($this->isColumnModified(CreditNoteDetailTableMap::TITLE)) {
            $modifiedColumns[':p' . $index++]  = 'TITLE';
        }
        if ($this->isColumnModified(CreditNoteDetailTableMap::CREATED_AT)) {
            $modifiedColumns[':p' . $index++]  = 'CREATED_AT';
        }
        if ($this->isColumnModified(CreditNoteDetailTableMap::UPDATED_AT)) {
            $modifiedColumns[':p' . $index++]  = 'UPDATED_AT';
        }

        $sql = sprintf(
            'INSERT INTO credit_note_detail (%s) VALUES (%s)',
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
                    case 'CREDIT_NOTE_ID':
                        $stmt->bindValue($identifier, $this->credit_note_id, PDO::PARAM_INT);
                        break;
                    case 'PRICE':
                        $stmt->bindValue($identifier, $this->price, PDO::PARAM_STR);
                        break;
                    case 'PRICE_WITH_TAX':
                        $stmt->bindValue($identifier, $this->price_with_tax, PDO::PARAM_STR);
                        break;
                    case 'TAX_RULE_ID':
                        $stmt->bindValue($identifier, $this->tax_rule_id, PDO::PARAM_INT);
                        break;
                    case 'ORDER_PRODUCT_ID':
                        $stmt->bindValue($identifier, $this->order_product_id, PDO::PARAM_INT);
                        break;
                    case 'TYPE':
                        $stmt->bindValue($identifier, $this->type, PDO::PARAM_STR);
                        break;
                    case 'QUANTITY':
                        $stmt->bindValue($identifier, $this->quantity, PDO::PARAM_INT);
                        break;
                    case 'TITLE':
                        $stmt->bindValue($identifier, $this->title, PDO::PARAM_STR);
                        break;
                    case 'CREATED_AT':
                        $stmt->bindValue($identifier, $this->created_at ? $this->created_at->format("Y-m-d H:i:s") : null, PDO::PARAM_STR);
                        break;
                    case 'UPDATED_AT':
                        $stmt->bindValue($identifier, $this->updated_at ? $this->updated_at->format("Y-m-d H:i:s") : null, PDO::PARAM_STR);
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
     *                     one of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_STUDLYPHPNAME
     *                     TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM.
     *                     Defaults to TableMap::TYPE_PHPNAME.
     * @return mixed Value of field.
     */
    public function getByName($name, $type = TableMap::TYPE_PHPNAME)
    {
        $pos = CreditNoteDetailTableMap::translateFieldName($name, $type, TableMap::TYPE_NUM);
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
                return $this->getCreditNoteId();
                break;
            case 2:
                return $this->getPrice();
                break;
            case 3:
                return $this->getPriceWithTax();
                break;
            case 4:
                return $this->getTaxRuleId();
                break;
            case 5:
                return $this->getOrderProductId();
                break;
            case 6:
                return $this->getType();
                break;
            case 7:
                return $this->getQuantity();
                break;
            case 8:
                return $this->getTitle();
                break;
            case 9:
                return $this->getCreatedAt();
                break;
            case 10:
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
        if (isset($alreadyDumpedObjects['CreditNoteDetail'][$this->getPrimaryKey()])) {
            return '*RECURSION*';
        }
        $alreadyDumpedObjects['CreditNoteDetail'][$this->getPrimaryKey()] = true;
        $keys = CreditNoteDetailTableMap::getFieldNames($keyType);
        $result = array(
            $keys[0] => $this->getId(),
            $keys[1] => $this->getCreditNoteId(),
            $keys[2] => $this->getPrice(),
            $keys[3] => $this->getPriceWithTax(),
            $keys[4] => $this->getTaxRuleId(),
            $keys[5] => $this->getOrderProductId(),
            $keys[6] => $this->getType(),
            $keys[7] => $this->getQuantity(),
            $keys[8] => $this->getTitle(),
            $keys[9] => $this->getCreatedAt(),
            $keys[10] => $this->getUpdatedAt(),
        );
        $virtualColumns = $this->virtualColumns;
        foreach ($virtualColumns as $key => $virtualColumn) {
            $result[$key] = $virtualColumn;
        }

        if ($includeForeignObjects) {
            if (null !== $this->aCreditNote) {
                $result['CreditNote'] = $this->aCreditNote->toArray($keyType, $includeLazyLoadColumns,  $alreadyDumpedObjects, true);
            }
            if (null !== $this->aOrderProduct) {
                $result['OrderProduct'] = $this->aOrderProduct->toArray($keyType, $includeLazyLoadColumns,  $alreadyDumpedObjects, true);
            }
            if (null !== $this->aTaxRule) {
                $result['TaxRule'] = $this->aTaxRule->toArray($keyType, $includeLazyLoadColumns,  $alreadyDumpedObjects, true);
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
        $pos = CreditNoteDetailTableMap::translateFieldName($name, $type, TableMap::TYPE_NUM);

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
                $this->setCreditNoteId($value);
                break;
            case 2:
                $this->setPrice($value);
                break;
            case 3:
                $this->setPriceWithTax($value);
                break;
            case 4:
                $this->setTaxRuleId($value);
                break;
            case 5:
                $this->setOrderProductId($value);
                break;
            case 6:
                $this->setType($value);
                break;
            case 7:
                $this->setQuantity($value);
                break;
            case 8:
                $this->setTitle($value);
                break;
            case 9:
                $this->setCreatedAt($value);
                break;
            case 10:
                $this->setUpdatedAt($value);
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
        $keys = CreditNoteDetailTableMap::getFieldNames($keyType);

        if (array_key_exists($keys[0], $arr)) $this->setId($arr[$keys[0]]);
        if (array_key_exists($keys[1], $arr)) $this->setCreditNoteId($arr[$keys[1]]);
        if (array_key_exists($keys[2], $arr)) $this->setPrice($arr[$keys[2]]);
        if (array_key_exists($keys[3], $arr)) $this->setPriceWithTax($arr[$keys[3]]);
        if (array_key_exists($keys[4], $arr)) $this->setTaxRuleId($arr[$keys[4]]);
        if (array_key_exists($keys[5], $arr)) $this->setOrderProductId($arr[$keys[5]]);
        if (array_key_exists($keys[6], $arr)) $this->setType($arr[$keys[6]]);
        if (array_key_exists($keys[7], $arr)) $this->setQuantity($arr[$keys[7]]);
        if (array_key_exists($keys[8], $arr)) $this->setTitle($arr[$keys[8]]);
        if (array_key_exists($keys[9], $arr)) $this->setCreatedAt($arr[$keys[9]]);
        if (array_key_exists($keys[10], $arr)) $this->setUpdatedAt($arr[$keys[10]]);
    }

    /**
     * Build a Criteria object containing the values of all modified columns in this object.
     *
     * @return Criteria The Criteria object containing all modified values.
     */
    public function buildCriteria()
    {
        $criteria = new Criteria(CreditNoteDetailTableMap::DATABASE_NAME);

        if ($this->isColumnModified(CreditNoteDetailTableMap::ID)) $criteria->add(CreditNoteDetailTableMap::ID, $this->id);
        if ($this->isColumnModified(CreditNoteDetailTableMap::CREDIT_NOTE_ID)) $criteria->add(CreditNoteDetailTableMap::CREDIT_NOTE_ID, $this->credit_note_id);
        if ($this->isColumnModified(CreditNoteDetailTableMap::PRICE)) $criteria->add(CreditNoteDetailTableMap::PRICE, $this->price);
        if ($this->isColumnModified(CreditNoteDetailTableMap::PRICE_WITH_TAX)) $criteria->add(CreditNoteDetailTableMap::PRICE_WITH_TAX, $this->price_with_tax);
        if ($this->isColumnModified(CreditNoteDetailTableMap::TAX_RULE_ID)) $criteria->add(CreditNoteDetailTableMap::TAX_RULE_ID, $this->tax_rule_id);
        if ($this->isColumnModified(CreditNoteDetailTableMap::ORDER_PRODUCT_ID)) $criteria->add(CreditNoteDetailTableMap::ORDER_PRODUCT_ID, $this->order_product_id);
        if ($this->isColumnModified(CreditNoteDetailTableMap::TYPE)) $criteria->add(CreditNoteDetailTableMap::TYPE, $this->type);
        if ($this->isColumnModified(CreditNoteDetailTableMap::QUANTITY)) $criteria->add(CreditNoteDetailTableMap::QUANTITY, $this->quantity);
        if ($this->isColumnModified(CreditNoteDetailTableMap::TITLE)) $criteria->add(CreditNoteDetailTableMap::TITLE, $this->title);
        if ($this->isColumnModified(CreditNoteDetailTableMap::CREATED_AT)) $criteria->add(CreditNoteDetailTableMap::CREATED_AT, $this->created_at);
        if ($this->isColumnModified(CreditNoteDetailTableMap::UPDATED_AT)) $criteria->add(CreditNoteDetailTableMap::UPDATED_AT, $this->updated_at);

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
        $criteria = new Criteria(CreditNoteDetailTableMap::DATABASE_NAME);
        $criteria->add(CreditNoteDetailTableMap::ID, $this->id);

        return $criteria;
    }

    /**
     * Returns the primary key for this object (row).
     * @return   int
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
     * @param      object $copyObj An object of \CreditNote\Model\CreditNoteDetail (or compatible) type.
     * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @param      boolean $makeNew Whether to reset autoincrement PKs and make the object new.
     * @throws PropelException
     */
    public function copyInto($copyObj, $deepCopy = false, $makeNew = true)
    {
        $copyObj->setCreditNoteId($this->getCreditNoteId());
        $copyObj->setPrice($this->getPrice());
        $copyObj->setPriceWithTax($this->getPriceWithTax());
        $copyObj->setTaxRuleId($this->getTaxRuleId());
        $copyObj->setOrderProductId($this->getOrderProductId());
        $copyObj->setType($this->getType());
        $copyObj->setQuantity($this->getQuantity());
        $copyObj->setTitle($this->getTitle());
        $copyObj->setCreatedAt($this->getCreatedAt());
        $copyObj->setUpdatedAt($this->getUpdatedAt());
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
     * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @return                 \CreditNote\Model\CreditNoteDetail Clone of current object.
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
     * @return                 \CreditNote\Model\CreditNoteDetail The current object (for fluent API support)
     * @throws PropelException
     */
    public function setCreditNote(ChildCreditNote $v = null)
    {
        if ($v === null) {
            $this->setCreditNoteId(NULL);
        } else {
            $this->setCreditNoteId($v->getId());
        }

        $this->aCreditNote = $v;

        // Add binding for other direction of this n:n relationship.
        // If this object has already been added to the ChildCreditNote object, it will not be re-added.
        if ($v !== null) {
            $v->addCreditNoteDetail($this);
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
        if ($this->aCreditNote === null && ($this->credit_note_id !== null)) {
            $this->aCreditNote = ChildCreditNoteQuery::create()->findPk($this->credit_note_id, $con);
            /* The following can be used additionally to
                guarantee the related object contains a reference
                to this object.  This level of coupling may, however, be
                undesirable since it could result in an only partially populated collection
                in the referenced object.
                $this->aCreditNote->addCreditNoteDetails($this);
             */
        }

        return $this->aCreditNote;
    }

    /**
     * Declares an association between this object and a ChildOrderProduct object.
     *
     * @param                  ChildOrderProduct $v
     * @return                 \CreditNote\Model\CreditNoteDetail The current object (for fluent API support)
     * @throws PropelException
     */
    public function setOrderProduct(ChildOrderProduct $v = null)
    {
        if ($v === null) {
            $this->setOrderProductId(NULL);
        } else {
            $this->setOrderProductId($v->getId());
        }

        $this->aOrderProduct = $v;

        // Add binding for other direction of this n:n relationship.
        // If this object has already been added to the ChildOrderProduct object, it will not be re-added.
        if ($v !== null) {
            $v->addCreditNoteDetail($this);
        }


        return $this;
    }


    /**
     * Get the associated ChildOrderProduct object
     *
     * @param      ConnectionInterface $con Optional Connection object.
     * @return                 ChildOrderProduct The associated ChildOrderProduct object.
     * @throws PropelException
     */
    public function getOrderProduct(ConnectionInterface $con = null)
    {
        if ($this->aOrderProduct === null && ($this->order_product_id !== null)) {
            $this->aOrderProduct = OrderProductQuery::create()->findPk($this->order_product_id, $con);
            /* The following can be used additionally to
                guarantee the related object contains a reference
                to this object.  This level of coupling may, however, be
                undesirable since it could result in an only partially populated collection
                in the referenced object.
                $this->aOrderProduct->addCreditNoteDetails($this);
             */
        }

        return $this->aOrderProduct;
    }

    /**
     * Declares an association between this object and a ChildTaxRule object.
     *
     * @param                  ChildTaxRule $v
     * @return                 \CreditNote\Model\CreditNoteDetail The current object (for fluent API support)
     * @throws PropelException
     */
    public function setTaxRule(ChildTaxRule $v = null)
    {
        if ($v === null) {
            $this->setTaxRuleId(NULL);
        } else {
            $this->setTaxRuleId($v->getId());
        }

        $this->aTaxRule = $v;

        // Add binding for other direction of this n:n relationship.
        // If this object has already been added to the ChildTaxRule object, it will not be re-added.
        if ($v !== null) {
            $v->addCreditNoteDetail($this);
        }


        return $this;
    }


    /**
     * Get the associated ChildTaxRule object
     *
     * @param      ConnectionInterface $con Optional Connection object.
     * @return                 ChildTaxRule The associated ChildTaxRule object.
     * @throws PropelException
     */
    public function getTaxRule(ConnectionInterface $con = null)
    {
        if ($this->aTaxRule === null && ($this->tax_rule_id !== null)) {
            $this->aTaxRule = TaxRuleQuery::create()->findPk($this->tax_rule_id, $con);
            /* The following can be used additionally to
                guarantee the related object contains a reference
                to this object.  This level of coupling may, however, be
                undesirable since it could result in an only partially populated collection
                in the referenced object.
                $this->aTaxRule->addCreditNoteDetails($this);
             */
        }

        return $this->aTaxRule;
    }

    /**
     * Clears the current object and sets all attributes to their default values
     */
    public function clear()
    {
        $this->id = null;
        $this->credit_note_id = null;
        $this->price = null;
        $this->price_with_tax = null;
        $this->tax_rule_id = null;
        $this->order_product_id = null;
        $this->type = null;
        $this->quantity = null;
        $this->title = null;
        $this->created_at = null;
        $this->updated_at = null;
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
        $this->aOrderProduct = null;
        $this->aTaxRule = null;
    }

    /**
     * Return the string representation of this object
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->exportTo(CreditNoteDetailTableMap::DEFAULT_STRING_FORMAT);
    }

    // timestampable behavior

    /**
     * Mark the current object so that the update date doesn't get updated during next save
     *
     * @return     ChildCreditNoteDetail The current object (for fluent API support)
     */
    public function keepUpdateDateUnchanged()
    {
        $this->modifiedColumns[CreditNoteDetailTableMap::UPDATED_AT] = true;

        return $this;
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
