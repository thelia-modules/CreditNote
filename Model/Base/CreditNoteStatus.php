<?php

namespace CreditNote\Model\Base;

use \DateTime;
use \Exception;
use \PDO;
use CreditNote\Model\CreditNote as ChildCreditNote;
use CreditNote\Model\CreditNoteQuery as ChildCreditNoteQuery;
use CreditNote\Model\CreditNoteStatus as ChildCreditNoteStatus;
use CreditNote\Model\CreditNoteStatusFlow as ChildCreditNoteStatusFlow;
use CreditNote\Model\CreditNoteStatusFlowQuery as ChildCreditNoteStatusFlowQuery;
use CreditNote\Model\CreditNoteStatusI18n as ChildCreditNoteStatusI18n;
use CreditNote\Model\CreditNoteStatusI18nQuery as ChildCreditNoteStatusI18nQuery;
use CreditNote\Model\CreditNoteStatusQuery as ChildCreditNoteStatusQuery;
use CreditNote\Model\Map\CreditNoteStatusTableMap;
use Propel\Runtime\Propel;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\ActiveRecord\ActiveRecordInterface;
use Propel\Runtime\Collection\Collection;
use Propel\Runtime\Collection\ObjectCollection;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\Exception\BadMethodCallException;
use Propel\Runtime\Exception\PropelException;
use Propel\Runtime\Map\TableMap;
use Propel\Runtime\Parser\AbstractParser;
use Propel\Runtime\Util\PropelDateTime;

abstract class CreditNoteStatus implements ActiveRecordInterface
{
    /**
     * TableMap class name
     */
    const TABLE_MAP = '\\CreditNote\\Model\\Map\\CreditNoteStatusTableMap';


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
     * The value for the code field.
     * @var        string
     */
    protected $code;

    /**
     * The value for the color field.
     * @var        string
     */
    protected $color;

    /**
     * The value for the invoiced field.
     * Note: this column has a database default value of: false
     * @var        boolean
     */
    protected $invoiced;

    /**
     * The value for the used field.
     * Note: this column has a database default value of: false
     * @var        boolean
     */
    protected $used;

    /**
     * The value for the position field.
     * @var        int
     */
    protected $position;

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
     * @var        ObjectCollection|ChildCreditNote[] Collection to store aggregation of ChildCreditNote objects.
     */
    protected $collCreditNotes;
    protected $collCreditNotesPartial;

    /**
     * @var        ObjectCollection|ChildCreditNoteStatusFlow[] Collection to store aggregation of ChildCreditNoteStatusFlow objects.
     */
    protected $collCreditNoteStatusFlowsRelatedByFromStatusId;
    protected $collCreditNoteStatusFlowsRelatedByFromStatusIdPartial;

    /**
     * @var        ObjectCollection|ChildCreditNoteStatusFlow[] Collection to store aggregation of ChildCreditNoteStatusFlow objects.
     */
    protected $collCreditNoteStatusFlowsRelatedByToStatusId;
    protected $collCreditNoteStatusFlowsRelatedByToStatusIdPartial;

    /**
     * @var        ObjectCollection|ChildCreditNoteStatusI18n[] Collection to store aggregation of ChildCreditNoteStatusI18n objects.
     */
    protected $collCreditNoteStatusI18ns;
    protected $collCreditNoteStatusI18nsPartial;

    /**
     * Flag to prevent endless save loop, if this object is referenced
     * by another object which falls in this transaction.
     *
     * @var boolean
     */
    protected $alreadyInSave = false;

    // i18n behavior

    /**
     * Current locale
     * @var        string
     */
    protected $currentLocale = 'en_US';

    /**
     * Current translation objects
     * @var        array[ChildCreditNoteStatusI18n]
     */
    protected $currentTranslations;

    /**
     * An array of objects scheduled for deletion.
     * @var ObjectCollection
     */
    protected $creditNotesScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var ObjectCollection
     */
    protected $creditNoteStatusFlowsRelatedByFromStatusIdScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var ObjectCollection
     */
    protected $creditNoteStatusFlowsRelatedByToStatusIdScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var ObjectCollection
     */
    protected $creditNoteStatusI18nsScheduledForDeletion = null;

    /**
     * Applies default values to this object.
     * This method should be called from the object's constructor (or
     * equivalent initialization method).
     * @see __construct()
     */
    public function applyDefaultValues()
    {
        $this->invoiced = false;
        $this->used = false;
    }

    /**
     * Initializes internal state of CreditNote\Model\Base\CreditNoteStatus object.
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
     * Compares this with another <code>CreditNoteStatus</code> instance.  If
     * <code>obj</code> is an instance of <code>CreditNoteStatus</code>, delegates to
     * <code>equals(CreditNoteStatus)</code>.  Otherwise, returns <code>false</code>.
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
     * @return CreditNoteStatus The current object, for fluid interface
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
     * @return CreditNoteStatus The current object, for fluid interface
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
     * Get the [code] column value.
     *
     * @return   string
     */
    public function getCode()
    {

        return $this->code;
    }

    /**
     * Get the [color] column value.
     *
     * @return   string
     */
    public function getColor()
    {

        return $this->color;
    }

    /**
     * Get the [invoiced] column value.
     *
     * @return   boolean
     */
    public function getInvoiced()
    {

        return $this->invoiced;
    }

    /**
     * Get the [used] column value.
     *
     * @return   boolean
     */
    public function getUsed()
    {

        return $this->used;
    }

    /**
     * Get the [position] column value.
     *
     * @return   int
     */
    public function getPosition()
    {

        return $this->position;
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
     * @return   \CreditNote\Model\CreditNoteStatus The current object (for fluent API support)
     */
    public function setId($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->id !== $v) {
            $this->id = $v;
            $this->modifiedColumns[CreditNoteStatusTableMap::ID] = true;
        }


        return $this;
    } // setId()

    /**
     * Set the value of [code] column.
     *
     * @param      string $v new value
     * @return   \CreditNote\Model\CreditNoteStatus The current object (for fluent API support)
     */
    public function setCode($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->code !== $v) {
            $this->code = $v;
            $this->modifiedColumns[CreditNoteStatusTableMap::CODE] = true;
        }


        return $this;
    } // setCode()

    /**
     * Set the value of [color] column.
     *
     * @param      string $v new value
     * @return   \CreditNote\Model\CreditNoteStatus The current object (for fluent API support)
     */
    public function setColor($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->color !== $v) {
            $this->color = $v;
            $this->modifiedColumns[CreditNoteStatusTableMap::COLOR] = true;
        }


        return $this;
    } // setColor()

    /**
     * Sets the value of the [invoiced] column.
     * Non-boolean arguments are converted using the following rules:
     *   * 1, '1', 'true',  'on',  and 'yes' are converted to boolean true
     *   * 0, '0', 'false', 'off', and 'no'  are converted to boolean false
     * Check on string values is case insensitive (so 'FaLsE' is seen as 'false').
     *
     * @param      boolean|integer|string $v The new value
     * @return   \CreditNote\Model\CreditNoteStatus The current object (for fluent API support)
     */
    public function setInvoiced($v)
    {
        if ($v !== null) {
            if (is_string($v)) {
                $v = in_array(strtolower($v), array('false', 'off', '-', 'no', 'n', '0', '')) ? false : true;
            } else {
                $v = (boolean) $v;
            }
        }

        if ($this->invoiced !== $v) {
            $this->invoiced = $v;
            $this->modifiedColumns[CreditNoteStatusTableMap::INVOICED] = true;
        }


        return $this;
    } // setInvoiced()

    /**
     * Sets the value of the [used] column.
     * Non-boolean arguments are converted using the following rules:
     *   * 1, '1', 'true',  'on',  and 'yes' are converted to boolean true
     *   * 0, '0', 'false', 'off', and 'no'  are converted to boolean false
     * Check on string values is case insensitive (so 'FaLsE' is seen as 'false').
     *
     * @param      boolean|integer|string $v The new value
     * @return   \CreditNote\Model\CreditNoteStatus The current object (for fluent API support)
     */
    public function setUsed($v)
    {
        if ($v !== null) {
            if (is_string($v)) {
                $v = in_array(strtolower($v), array('false', 'off', '-', 'no', 'n', '0', '')) ? false : true;
            } else {
                $v = (boolean) $v;
            }
        }

        if ($this->used !== $v) {
            $this->used = $v;
            $this->modifiedColumns[CreditNoteStatusTableMap::USED] = true;
        }


        return $this;
    } // setUsed()

    /**
     * Set the value of [position] column.
     *
     * @param      int $v new value
     * @return   \CreditNote\Model\CreditNoteStatus The current object (for fluent API support)
     */
    public function setPosition($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->position !== $v) {
            $this->position = $v;
            $this->modifiedColumns[CreditNoteStatusTableMap::POSITION] = true;
        }


        return $this;
    } // setPosition()

    /**
     * Sets the value of [created_at] column to a normalized version of the date/time value specified.
     *
     * @param      mixed $v string, integer (timestamp), or \DateTime value.
     *               Empty strings are treated as NULL.
     * @return   \CreditNote\Model\CreditNoteStatus The current object (for fluent API support)
     */
    public function setCreatedAt($v)
    {
        $dt = PropelDateTime::newInstance($v, null, '\DateTime');
        if ($this->created_at !== null || $dt !== null) {
            if ($dt !== $this->created_at) {
                $this->created_at = $dt;
                $this->modifiedColumns[CreditNoteStatusTableMap::CREATED_AT] = true;
            }
        } // if either are not null


        return $this;
    } // setCreatedAt()

    /**
     * Sets the value of [updated_at] column to a normalized version of the date/time value specified.
     *
     * @param      mixed $v string, integer (timestamp), or \DateTime value.
     *               Empty strings are treated as NULL.
     * @return   \CreditNote\Model\CreditNoteStatus The current object (for fluent API support)
     */
    public function setUpdatedAt($v)
    {
        $dt = PropelDateTime::newInstance($v, null, '\DateTime');
        if ($this->updated_at !== null || $dt !== null) {
            if ($dt !== $this->updated_at) {
                $this->updated_at = $dt;
                $this->modifiedColumns[CreditNoteStatusTableMap::UPDATED_AT] = true;
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
            if ($this->invoiced !== false) {
                return false;
            }

            if ($this->used !== false) {
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


            $col = $row[TableMap::TYPE_NUM == $indexType ? 0 + $startcol : CreditNoteStatusTableMap::translateFieldName('Id', TableMap::TYPE_PHPNAME, $indexType)];
            $this->id = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 1 + $startcol : CreditNoteStatusTableMap::translateFieldName('Code', TableMap::TYPE_PHPNAME, $indexType)];
            $this->code = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 2 + $startcol : CreditNoteStatusTableMap::translateFieldName('Color', TableMap::TYPE_PHPNAME, $indexType)];
            $this->color = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 3 + $startcol : CreditNoteStatusTableMap::translateFieldName('Invoiced', TableMap::TYPE_PHPNAME, $indexType)];
            $this->invoiced = (null !== $col) ? (boolean) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 4 + $startcol : CreditNoteStatusTableMap::translateFieldName('Used', TableMap::TYPE_PHPNAME, $indexType)];
            $this->used = (null !== $col) ? (boolean) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 5 + $startcol : CreditNoteStatusTableMap::translateFieldName('Position', TableMap::TYPE_PHPNAME, $indexType)];
            $this->position = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 6 + $startcol : CreditNoteStatusTableMap::translateFieldName('CreatedAt', TableMap::TYPE_PHPNAME, $indexType)];
            if ($col === '0000-00-00 00:00:00') {
                $col = null;
            }
            $this->created_at = (null !== $col) ? PropelDateTime::newInstance($col, null, '\DateTime') : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 7 + $startcol : CreditNoteStatusTableMap::translateFieldName('UpdatedAt', TableMap::TYPE_PHPNAME, $indexType)];
            if ($col === '0000-00-00 00:00:00') {
                $col = null;
            }
            $this->updated_at = (null !== $col) ? PropelDateTime::newInstance($col, null, '\DateTime') : null;
            $this->resetModified();

            $this->setNew(false);

            if ($rehydrate) {
                $this->ensureConsistency();
            }

            return $startcol + 8; // 8 = CreditNoteStatusTableMap::NUM_HYDRATE_COLUMNS.

        } catch (Exception $e) {
            throw new PropelException("Error populating \CreditNote\Model\CreditNoteStatus object", 0, $e);
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
            $con = Propel::getServiceContainer()->getReadConnection(CreditNoteStatusTableMap::DATABASE_NAME);
        }

        // We don't need to alter the object instance pool; we're just modifying this instance
        // already in the pool.

        $dataFetcher = ChildCreditNoteStatusQuery::create(null, $this->buildPkeyCriteria())->setFormatter(ModelCriteria::FORMAT_STATEMENT)->find($con);
        $row = $dataFetcher->fetch();
        $dataFetcher->close();
        if (!$row) {
            throw new PropelException('Cannot find matching row in the database to reload object values.');
        }
        $this->hydrate($row, 0, true, $dataFetcher->getIndexType()); // rehydrate

        if ($deep) {  // also de-associate any related objects?

            $this->collCreditNotes = null;

            $this->collCreditNoteStatusFlowsRelatedByFromStatusId = null;

            $this->collCreditNoteStatusFlowsRelatedByToStatusId = null;

            $this->collCreditNoteStatusI18ns = null;

        } // if (deep)
    }

    /**
     * Removes this object from datastore and sets delete attribute.
     *
     * @param      ConnectionInterface $con
     * @return void
     * @throws PropelException
     * @see CreditNoteStatus::setDeleted()
     * @see CreditNoteStatus::isDeleted()
     */
    public function delete(ConnectionInterface $con = null)
    {
        if ($this->isDeleted()) {
            throw new PropelException("This object has already been deleted.");
        }

        if ($con === null) {
            $con = Propel::getServiceContainer()->getWriteConnection(CreditNoteStatusTableMap::DATABASE_NAME);
        }

        $con->beginTransaction();
        try {
            $deleteQuery = ChildCreditNoteStatusQuery::create()
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
            $con = Propel::getServiceContainer()->getWriteConnection(CreditNoteStatusTableMap::DATABASE_NAME);
        }

        $con->beginTransaction();
        $isInsert = $this->isNew();
        try {
            $ret = $this->preSave($con);
            if ($isInsert) {
                $ret = $ret && $this->preInsert($con);
                // timestampable behavior
                if (!$this->isColumnModified(CreditNoteStatusTableMap::CREATED_AT)) {
                    $this->setCreatedAt(time());
                }
                if (!$this->isColumnModified(CreditNoteStatusTableMap::UPDATED_AT)) {
                    $this->setUpdatedAt(time());
                }
            } else {
                $ret = $ret && $this->preUpdate($con);
                // timestampable behavior
                if ($this->isModified() && !$this->isColumnModified(CreditNoteStatusTableMap::UPDATED_AT)) {
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
                CreditNoteStatusTableMap::addInstanceToPool($this);
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

            if ($this->creditNoteStatusFlowsRelatedByFromStatusIdScheduledForDeletion !== null) {
                if (!$this->creditNoteStatusFlowsRelatedByFromStatusIdScheduledForDeletion->isEmpty()) {
                    \CreditNote\Model\CreditNoteStatusFlowQuery::create()
                        ->filterByPrimaryKeys($this->creditNoteStatusFlowsRelatedByFromStatusIdScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->creditNoteStatusFlowsRelatedByFromStatusIdScheduledForDeletion = null;
                }
            }

                if ($this->collCreditNoteStatusFlowsRelatedByFromStatusId !== null) {
            foreach ($this->collCreditNoteStatusFlowsRelatedByFromStatusId as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->creditNoteStatusFlowsRelatedByToStatusIdScheduledForDeletion !== null) {
                if (!$this->creditNoteStatusFlowsRelatedByToStatusIdScheduledForDeletion->isEmpty()) {
                    \CreditNote\Model\CreditNoteStatusFlowQuery::create()
                        ->filterByPrimaryKeys($this->creditNoteStatusFlowsRelatedByToStatusIdScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->creditNoteStatusFlowsRelatedByToStatusIdScheduledForDeletion = null;
                }
            }

                if ($this->collCreditNoteStatusFlowsRelatedByToStatusId !== null) {
            foreach ($this->collCreditNoteStatusFlowsRelatedByToStatusId as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->creditNoteStatusI18nsScheduledForDeletion !== null) {
                if (!$this->creditNoteStatusI18nsScheduledForDeletion->isEmpty()) {
                    \CreditNote\Model\CreditNoteStatusI18nQuery::create()
                        ->filterByPrimaryKeys($this->creditNoteStatusI18nsScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->creditNoteStatusI18nsScheduledForDeletion = null;
                }
            }

                if ($this->collCreditNoteStatusI18ns !== null) {
            foreach ($this->collCreditNoteStatusI18ns as $referrerFK) {
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

        $this->modifiedColumns[CreditNoteStatusTableMap::ID] = true;
        if (null !== $this->id) {
            throw new PropelException('Cannot insert a value for auto-increment primary key (' . CreditNoteStatusTableMap::ID . ')');
        }

         // check the columns in natural order for more readable SQL queries
        if ($this->isColumnModified(CreditNoteStatusTableMap::ID)) {
            $modifiedColumns[':p' . $index++]  = 'ID';
        }
        if ($this->isColumnModified(CreditNoteStatusTableMap::CODE)) {
            $modifiedColumns[':p' . $index++]  = 'CODE';
        }
        if ($this->isColumnModified(CreditNoteStatusTableMap::COLOR)) {
            $modifiedColumns[':p' . $index++]  = 'COLOR';
        }
        if ($this->isColumnModified(CreditNoteStatusTableMap::INVOICED)) {
            $modifiedColumns[':p' . $index++]  = 'INVOICED';
        }
        if ($this->isColumnModified(CreditNoteStatusTableMap::USED)) {
            $modifiedColumns[':p' . $index++]  = 'USED';
        }
        if ($this->isColumnModified(CreditNoteStatusTableMap::POSITION)) {
            $modifiedColumns[':p' . $index++]  = 'POSITION';
        }
        if ($this->isColumnModified(CreditNoteStatusTableMap::CREATED_AT)) {
            $modifiedColumns[':p' . $index++]  = 'CREATED_AT';
        }
        if ($this->isColumnModified(CreditNoteStatusTableMap::UPDATED_AT)) {
            $modifiedColumns[':p' . $index++]  = 'UPDATED_AT';
        }

        $sql = sprintf(
            'INSERT INTO credit_note_status (%s) VALUES (%s)',
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
                    case 'CODE':
                        $stmt->bindValue($identifier, $this->code, PDO::PARAM_STR);
                        break;
                    case 'COLOR':
                        $stmt->bindValue($identifier, $this->color, PDO::PARAM_STR);
                        break;
                    case 'INVOICED':
                        $stmt->bindValue($identifier, (int) $this->invoiced, PDO::PARAM_INT);
                        break;
                    case 'USED':
                        $stmt->bindValue($identifier, (int) $this->used, PDO::PARAM_INT);
                        break;
                    case 'POSITION':
                        $stmt->bindValue($identifier, $this->position, PDO::PARAM_INT);
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
        $pos = CreditNoteStatusTableMap::translateFieldName($name, $type, TableMap::TYPE_NUM);
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
                return $this->getCode();
                break;
            case 2:
                return $this->getColor();
                break;
            case 3:
                return $this->getInvoiced();
                break;
            case 4:
                return $this->getUsed();
                break;
            case 5:
                return $this->getPosition();
                break;
            case 6:
                return $this->getCreatedAt();
                break;
            case 7:
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
        if (isset($alreadyDumpedObjects['CreditNoteStatus'][$this->getPrimaryKey()])) {
            return '*RECURSION*';
        }
        $alreadyDumpedObjects['CreditNoteStatus'][$this->getPrimaryKey()] = true;
        $keys = CreditNoteStatusTableMap::getFieldNames($keyType);
        $result = array(
            $keys[0] => $this->getId(),
            $keys[1] => $this->getCode(),
            $keys[2] => $this->getColor(),
            $keys[3] => $this->getInvoiced(),
            $keys[4] => $this->getUsed(),
            $keys[5] => $this->getPosition(),
            $keys[6] => $this->getCreatedAt(),
            $keys[7] => $this->getUpdatedAt(),
        );
        $virtualColumns = $this->virtualColumns;
        foreach ($virtualColumns as $key => $virtualColumn) {
            $result[$key] = $virtualColumn;
        }

        if ($includeForeignObjects) {
            if (null !== $this->collCreditNotes) {
                $result['CreditNotes'] = $this->collCreditNotes->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collCreditNoteStatusFlowsRelatedByFromStatusId) {
                $result['CreditNoteStatusFlowsRelatedByFromStatusId'] = $this->collCreditNoteStatusFlowsRelatedByFromStatusId->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collCreditNoteStatusFlowsRelatedByToStatusId) {
                $result['CreditNoteStatusFlowsRelatedByToStatusId'] = $this->collCreditNoteStatusFlowsRelatedByToStatusId->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collCreditNoteStatusI18ns) {
                $result['CreditNoteStatusI18ns'] = $this->collCreditNoteStatusI18ns->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
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
        $pos = CreditNoteStatusTableMap::translateFieldName($name, $type, TableMap::TYPE_NUM);

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
                $this->setCode($value);
                break;
            case 2:
                $this->setColor($value);
                break;
            case 3:
                $this->setInvoiced($value);
                break;
            case 4:
                $this->setUsed($value);
                break;
            case 5:
                $this->setPosition($value);
                break;
            case 6:
                $this->setCreatedAt($value);
                break;
            case 7:
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
        $keys = CreditNoteStatusTableMap::getFieldNames($keyType);

        if (array_key_exists($keys[0], $arr)) $this->setId($arr[$keys[0]]);
        if (array_key_exists($keys[1], $arr)) $this->setCode($arr[$keys[1]]);
        if (array_key_exists($keys[2], $arr)) $this->setColor($arr[$keys[2]]);
        if (array_key_exists($keys[3], $arr)) $this->setInvoiced($arr[$keys[3]]);
        if (array_key_exists($keys[4], $arr)) $this->setUsed($arr[$keys[4]]);
        if (array_key_exists($keys[5], $arr)) $this->setPosition($arr[$keys[5]]);
        if (array_key_exists($keys[6], $arr)) $this->setCreatedAt($arr[$keys[6]]);
        if (array_key_exists($keys[7], $arr)) $this->setUpdatedAt($arr[$keys[7]]);
    }

    /**
     * Build a Criteria object containing the values of all modified columns in this object.
     *
     * @return Criteria The Criteria object containing all modified values.
     */
    public function buildCriteria()
    {
        $criteria = new Criteria(CreditNoteStatusTableMap::DATABASE_NAME);

        if ($this->isColumnModified(CreditNoteStatusTableMap::ID)) $criteria->add(CreditNoteStatusTableMap::ID, $this->id);
        if ($this->isColumnModified(CreditNoteStatusTableMap::CODE)) $criteria->add(CreditNoteStatusTableMap::CODE, $this->code);
        if ($this->isColumnModified(CreditNoteStatusTableMap::COLOR)) $criteria->add(CreditNoteStatusTableMap::COLOR, $this->color);
        if ($this->isColumnModified(CreditNoteStatusTableMap::INVOICED)) $criteria->add(CreditNoteStatusTableMap::INVOICED, $this->invoiced);
        if ($this->isColumnModified(CreditNoteStatusTableMap::USED)) $criteria->add(CreditNoteStatusTableMap::USED, $this->used);
        if ($this->isColumnModified(CreditNoteStatusTableMap::POSITION)) $criteria->add(CreditNoteStatusTableMap::POSITION, $this->position);
        if ($this->isColumnModified(CreditNoteStatusTableMap::CREATED_AT)) $criteria->add(CreditNoteStatusTableMap::CREATED_AT, $this->created_at);
        if ($this->isColumnModified(CreditNoteStatusTableMap::UPDATED_AT)) $criteria->add(CreditNoteStatusTableMap::UPDATED_AT, $this->updated_at);

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
        $criteria = new Criteria(CreditNoteStatusTableMap::DATABASE_NAME);
        $criteria->add(CreditNoteStatusTableMap::ID, $this->id);

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
     * @param      object $copyObj An object of \CreditNote\Model\CreditNoteStatus (or compatible) type.
     * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @param      boolean $makeNew Whether to reset autoincrement PKs and make the object new.
     * @throws PropelException
     */
    public function copyInto($copyObj, $deepCopy = false, $makeNew = true)
    {
        $copyObj->setCode($this->getCode());
        $copyObj->setColor($this->getColor());
        $copyObj->setInvoiced($this->getInvoiced());
        $copyObj->setUsed($this->getUsed());
        $copyObj->setPosition($this->getPosition());
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

            foreach ($this->getCreditNoteStatusFlowsRelatedByFromStatusId() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addCreditNoteStatusFlowRelatedByFromStatusId($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getCreditNoteStatusFlowsRelatedByToStatusId() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addCreditNoteStatusFlowRelatedByToStatusId($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getCreditNoteStatusI18ns() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addCreditNoteStatusI18n($relObj->copy($deepCopy));
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
     * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @return                 \CreditNote\Model\CreditNoteStatus Clone of current object.
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
            return $this->initCreditNotes();
        }
        if ('CreditNoteStatusFlowRelatedByFromStatusId' == $relationName) {
            return $this->initCreditNoteStatusFlowsRelatedByFromStatusId();
        }
        if ('CreditNoteStatusFlowRelatedByToStatusId' == $relationName) {
            return $this->initCreditNoteStatusFlowsRelatedByToStatusId();
        }
        if ('CreditNoteStatusI18n' == $relationName) {
            return $this->initCreditNoteStatusI18ns();
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
        $this->collCreditNotes = new ObjectCollection();
        $this->collCreditNotes->setModel('\CreditNote\Model\CreditNote');
    }

    /**
     * Gets an array of ChildCreditNote objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this ChildCreditNoteStatus is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @return Collection|ChildCreditNote[] List of ChildCreditNote objects
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
                    ->filterByCreditNoteStatus($this)
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

                    reset($collCreditNotes);

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
     * Sets a collection of CreditNote objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param      Collection $creditNotes A Propel collection.
     * @param      ConnectionInterface $con Optional connection object
     * @return   ChildCreditNoteStatus The current object (for fluent API support)
     */
    public function setCreditNotes(Collection $creditNotes, ConnectionInterface $con = null)
    {
        $creditNotesToDelete = $this->getCreditNotes(new Criteria(), $con)->diff($creditNotes);


        $this->creditNotesScheduledForDeletion = $creditNotesToDelete;

        foreach ($creditNotesToDelete as $creditNoteRemoved) {
            $creditNoteRemoved->setCreditNoteStatus(null);
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
                ->filterByCreditNoteStatus($this)
                ->count($con);
        }

        return count($this->collCreditNotes);
    }

    /**
     * Method called to associate a ChildCreditNote object to this object
     * through the ChildCreditNote foreign key attribute.
     *
     * @param    ChildCreditNote $l ChildCreditNote
     * @return   \CreditNote\Model\CreditNoteStatus The current object (for fluent API support)
     */
    public function addCreditNote(ChildCreditNote $l)
    {
        if ($this->collCreditNotes === null) {
            $this->initCreditNotes();
            $this->collCreditNotesPartial = true;
        }

        if (!in_array($l, $this->collCreditNotes->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddCreditNote($l);
        }

        return $this;
    }

    /**
     * @param CreditNote $creditNote The creditNote object to add.
     */
    protected function doAddCreditNote($creditNote)
    {
        $this->collCreditNotes[]= $creditNote;
        $creditNote->setCreditNoteStatus($this);
    }

    /**
     * @param  CreditNote $creditNote The creditNote object to remove.
     * @return ChildCreditNoteStatus The current object (for fluent API support)
     */
    public function removeCreditNote($creditNote)
    {
        if ($this->getCreditNotes()->contains($creditNote)) {
            $this->collCreditNotes->remove($this->collCreditNotes->search($creditNote));
            if (null === $this->creditNotesScheduledForDeletion) {
                $this->creditNotesScheduledForDeletion = clone $this->collCreditNotes;
                $this->creditNotesScheduledForDeletion->clear();
            }
            $this->creditNotesScheduledForDeletion[]= clone $creditNote;
            $creditNote->setCreditNoteStatus(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this CreditNoteStatus is new, it will return
     * an empty collection; or if this CreditNoteStatus has previously
     * been saved, it will retrieve related CreditNotes from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in CreditNoteStatus.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @param      string $joinBehavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return Collection|ChildCreditNote[] List of ChildCreditNote objects
     */
    public function getCreditNotesJoinOrder($criteria = null, $con = null, $joinBehavior = Criteria::LEFT_JOIN)
    {
        $query = ChildCreditNoteQuery::create(null, $criteria);
        $query->joinWith('Order', $joinBehavior);

        return $this->getCreditNotes($query, $con);
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this CreditNoteStatus is new, it will return
     * an empty collection; or if this CreditNoteStatus has previously
     * been saved, it will retrieve related CreditNotes from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in CreditNoteStatus.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @param      string $joinBehavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return Collection|ChildCreditNote[] List of ChildCreditNote objects
     */
    public function getCreditNotesJoinCustomer($criteria = null, $con = null, $joinBehavior = Criteria::LEFT_JOIN)
    {
        $query = ChildCreditNoteQuery::create(null, $criteria);
        $query->joinWith('Customer', $joinBehavior);

        return $this->getCreditNotes($query, $con);
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this CreditNoteStatus is new, it will return
     * an empty collection; or if this CreditNoteStatus has previously
     * been saved, it will retrieve related CreditNotes from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in CreditNoteStatus.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @param      string $joinBehavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return Collection|ChildCreditNote[] List of ChildCreditNote objects
     */
    public function getCreditNotesJoinCreditNoteRelatedByParentId($criteria = null, $con = null, $joinBehavior = Criteria::LEFT_JOIN)
    {
        $query = ChildCreditNoteQuery::create(null, $criteria);
        $query->joinWith('CreditNoteRelatedByParentId', $joinBehavior);

        return $this->getCreditNotes($query, $con);
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this CreditNoteStatus is new, it will return
     * an empty collection; or if this CreditNoteStatus has previously
     * been saved, it will retrieve related CreditNotes from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in CreditNoteStatus.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @param      string $joinBehavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return Collection|ChildCreditNote[] List of ChildCreditNote objects
     */
    public function getCreditNotesJoinCreditNoteType($criteria = null, $con = null, $joinBehavior = Criteria::LEFT_JOIN)
    {
        $query = ChildCreditNoteQuery::create(null, $criteria);
        $query->joinWith('CreditNoteType', $joinBehavior);

        return $this->getCreditNotes($query, $con);
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this CreditNoteStatus is new, it will return
     * an empty collection; or if this CreditNoteStatus has previously
     * been saved, it will retrieve related CreditNotes from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in CreditNoteStatus.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @param      string $joinBehavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return Collection|ChildCreditNote[] List of ChildCreditNote objects
     */
    public function getCreditNotesJoinCurrency($criteria = null, $con = null, $joinBehavior = Criteria::LEFT_JOIN)
    {
        $query = ChildCreditNoteQuery::create(null, $criteria);
        $query->joinWith('Currency', $joinBehavior);

        return $this->getCreditNotes($query, $con);
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this CreditNoteStatus is new, it will return
     * an empty collection; or if this CreditNoteStatus has previously
     * been saved, it will retrieve related CreditNotes from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in CreditNoteStatus.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @param      string $joinBehavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return Collection|ChildCreditNote[] List of ChildCreditNote objects
     */
    public function getCreditNotesJoinCreditNoteAddress($criteria = null, $con = null, $joinBehavior = Criteria::LEFT_JOIN)
    {
        $query = ChildCreditNoteQuery::create(null, $criteria);
        $query->joinWith('CreditNoteAddress', $joinBehavior);

        return $this->getCreditNotes($query, $con);
    }

    /**
     * Clears out the collCreditNoteStatusFlowsRelatedByFromStatusId collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return void
     * @see        addCreditNoteStatusFlowsRelatedByFromStatusId()
     */
    public function clearCreditNoteStatusFlowsRelatedByFromStatusId()
    {
        $this->collCreditNoteStatusFlowsRelatedByFromStatusId = null; // important to set this to NULL since that means it is uninitialized
    }

    /**
     * Reset is the collCreditNoteStatusFlowsRelatedByFromStatusId collection loaded partially.
     */
    public function resetPartialCreditNoteStatusFlowsRelatedByFromStatusId($v = true)
    {
        $this->collCreditNoteStatusFlowsRelatedByFromStatusIdPartial = $v;
    }

    /**
     * Initializes the collCreditNoteStatusFlowsRelatedByFromStatusId collection.
     *
     * By default this just sets the collCreditNoteStatusFlowsRelatedByFromStatusId collection to an empty array (like clearcollCreditNoteStatusFlowsRelatedByFromStatusId());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param      boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initCreditNoteStatusFlowsRelatedByFromStatusId($overrideExisting = true)
    {
        if (null !== $this->collCreditNoteStatusFlowsRelatedByFromStatusId && !$overrideExisting) {
            return;
        }
        $this->collCreditNoteStatusFlowsRelatedByFromStatusId = new ObjectCollection();
        $this->collCreditNoteStatusFlowsRelatedByFromStatusId->setModel('\CreditNote\Model\CreditNoteStatusFlow');
    }

    /**
     * Gets an array of ChildCreditNoteStatusFlow objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this ChildCreditNoteStatus is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @return Collection|ChildCreditNoteStatusFlow[] List of ChildCreditNoteStatusFlow objects
     * @throws PropelException
     */
    public function getCreditNoteStatusFlowsRelatedByFromStatusId($criteria = null, ConnectionInterface $con = null)
    {
        $partial = $this->collCreditNoteStatusFlowsRelatedByFromStatusIdPartial && !$this->isNew();
        if (null === $this->collCreditNoteStatusFlowsRelatedByFromStatusId || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collCreditNoteStatusFlowsRelatedByFromStatusId) {
                // return empty collection
                $this->initCreditNoteStatusFlowsRelatedByFromStatusId();
            } else {
                $collCreditNoteStatusFlowsRelatedByFromStatusId = ChildCreditNoteStatusFlowQuery::create(null, $criteria)
                    ->filterByCreditNoteStatusRelatedByFromStatusId($this)
                    ->find($con);

                if (null !== $criteria) {
                    if (false !== $this->collCreditNoteStatusFlowsRelatedByFromStatusIdPartial && count($collCreditNoteStatusFlowsRelatedByFromStatusId)) {
                        $this->initCreditNoteStatusFlowsRelatedByFromStatusId(false);

                        foreach ($collCreditNoteStatusFlowsRelatedByFromStatusId as $obj) {
                            if (false == $this->collCreditNoteStatusFlowsRelatedByFromStatusId->contains($obj)) {
                                $this->collCreditNoteStatusFlowsRelatedByFromStatusId->append($obj);
                            }
                        }

                        $this->collCreditNoteStatusFlowsRelatedByFromStatusIdPartial = true;
                    }

                    reset($collCreditNoteStatusFlowsRelatedByFromStatusId);

                    return $collCreditNoteStatusFlowsRelatedByFromStatusId;
                }

                if ($partial && $this->collCreditNoteStatusFlowsRelatedByFromStatusId) {
                    foreach ($this->collCreditNoteStatusFlowsRelatedByFromStatusId as $obj) {
                        if ($obj->isNew()) {
                            $collCreditNoteStatusFlowsRelatedByFromStatusId[] = $obj;
                        }
                    }
                }

                $this->collCreditNoteStatusFlowsRelatedByFromStatusId = $collCreditNoteStatusFlowsRelatedByFromStatusId;
                $this->collCreditNoteStatusFlowsRelatedByFromStatusIdPartial = false;
            }
        }

        return $this->collCreditNoteStatusFlowsRelatedByFromStatusId;
    }

    /**
     * Sets a collection of CreditNoteStatusFlowRelatedByFromStatusId objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param      Collection $creditNoteStatusFlowsRelatedByFromStatusId A Propel collection.
     * @param      ConnectionInterface $con Optional connection object
     * @return   ChildCreditNoteStatus The current object (for fluent API support)
     */
    public function setCreditNoteStatusFlowsRelatedByFromStatusId(Collection $creditNoteStatusFlowsRelatedByFromStatusId, ConnectionInterface $con = null)
    {
        $creditNoteStatusFlowsRelatedByFromStatusIdToDelete = $this->getCreditNoteStatusFlowsRelatedByFromStatusId(new Criteria(), $con)->diff($creditNoteStatusFlowsRelatedByFromStatusId);


        $this->creditNoteStatusFlowsRelatedByFromStatusIdScheduledForDeletion = $creditNoteStatusFlowsRelatedByFromStatusIdToDelete;

        foreach ($creditNoteStatusFlowsRelatedByFromStatusIdToDelete as $creditNoteStatusFlowRelatedByFromStatusIdRemoved) {
            $creditNoteStatusFlowRelatedByFromStatusIdRemoved->setCreditNoteStatusRelatedByFromStatusId(null);
        }

        $this->collCreditNoteStatusFlowsRelatedByFromStatusId = null;
        foreach ($creditNoteStatusFlowsRelatedByFromStatusId as $creditNoteStatusFlowRelatedByFromStatusId) {
            $this->addCreditNoteStatusFlowRelatedByFromStatusId($creditNoteStatusFlowRelatedByFromStatusId);
        }

        $this->collCreditNoteStatusFlowsRelatedByFromStatusId = $creditNoteStatusFlowsRelatedByFromStatusId;
        $this->collCreditNoteStatusFlowsRelatedByFromStatusIdPartial = false;

        return $this;
    }

    /**
     * Returns the number of related CreditNoteStatusFlow objects.
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct
     * @param      ConnectionInterface $con
     * @return int             Count of related CreditNoteStatusFlow objects.
     * @throws PropelException
     */
    public function countCreditNoteStatusFlowsRelatedByFromStatusId(Criteria $criteria = null, $distinct = false, ConnectionInterface $con = null)
    {
        $partial = $this->collCreditNoteStatusFlowsRelatedByFromStatusIdPartial && !$this->isNew();
        if (null === $this->collCreditNoteStatusFlowsRelatedByFromStatusId || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collCreditNoteStatusFlowsRelatedByFromStatusId) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getCreditNoteStatusFlowsRelatedByFromStatusId());
            }

            $query = ChildCreditNoteStatusFlowQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByCreditNoteStatusRelatedByFromStatusId($this)
                ->count($con);
        }

        return count($this->collCreditNoteStatusFlowsRelatedByFromStatusId);
    }

    /**
     * Method called to associate a ChildCreditNoteStatusFlow object to this object
     * through the ChildCreditNoteStatusFlow foreign key attribute.
     *
     * @param    ChildCreditNoteStatusFlow $l ChildCreditNoteStatusFlow
     * @return   \CreditNote\Model\CreditNoteStatus The current object (for fluent API support)
     */
    public function addCreditNoteStatusFlowRelatedByFromStatusId(ChildCreditNoteStatusFlow $l)
    {
        if ($this->collCreditNoteStatusFlowsRelatedByFromStatusId === null) {
            $this->initCreditNoteStatusFlowsRelatedByFromStatusId();
            $this->collCreditNoteStatusFlowsRelatedByFromStatusIdPartial = true;
        }

        if (!in_array($l, $this->collCreditNoteStatusFlowsRelatedByFromStatusId->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddCreditNoteStatusFlowRelatedByFromStatusId($l);
        }

        return $this;
    }

    /**
     * @param CreditNoteStatusFlowRelatedByFromStatusId $creditNoteStatusFlowRelatedByFromStatusId The creditNoteStatusFlowRelatedByFromStatusId object to add.
     */
    protected function doAddCreditNoteStatusFlowRelatedByFromStatusId($creditNoteStatusFlowRelatedByFromStatusId)
    {
        $this->collCreditNoteStatusFlowsRelatedByFromStatusId[]= $creditNoteStatusFlowRelatedByFromStatusId;
        $creditNoteStatusFlowRelatedByFromStatusId->setCreditNoteStatusRelatedByFromStatusId($this);
    }

    /**
     * @param  CreditNoteStatusFlowRelatedByFromStatusId $creditNoteStatusFlowRelatedByFromStatusId The creditNoteStatusFlowRelatedByFromStatusId object to remove.
     * @return ChildCreditNoteStatus The current object (for fluent API support)
     */
    public function removeCreditNoteStatusFlowRelatedByFromStatusId($creditNoteStatusFlowRelatedByFromStatusId)
    {
        if ($this->getCreditNoteStatusFlowsRelatedByFromStatusId()->contains($creditNoteStatusFlowRelatedByFromStatusId)) {
            $this->collCreditNoteStatusFlowsRelatedByFromStatusId->remove($this->collCreditNoteStatusFlowsRelatedByFromStatusId->search($creditNoteStatusFlowRelatedByFromStatusId));
            if (null === $this->creditNoteStatusFlowsRelatedByFromStatusIdScheduledForDeletion) {
                $this->creditNoteStatusFlowsRelatedByFromStatusIdScheduledForDeletion = clone $this->collCreditNoteStatusFlowsRelatedByFromStatusId;
                $this->creditNoteStatusFlowsRelatedByFromStatusIdScheduledForDeletion->clear();
            }
            $this->creditNoteStatusFlowsRelatedByFromStatusIdScheduledForDeletion[]= clone $creditNoteStatusFlowRelatedByFromStatusId;
            $creditNoteStatusFlowRelatedByFromStatusId->setCreditNoteStatusRelatedByFromStatusId(null);
        }

        return $this;
    }

    /**
     * Clears out the collCreditNoteStatusFlowsRelatedByToStatusId collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return void
     * @see        addCreditNoteStatusFlowsRelatedByToStatusId()
     */
    public function clearCreditNoteStatusFlowsRelatedByToStatusId()
    {
        $this->collCreditNoteStatusFlowsRelatedByToStatusId = null; // important to set this to NULL since that means it is uninitialized
    }

    /**
     * Reset is the collCreditNoteStatusFlowsRelatedByToStatusId collection loaded partially.
     */
    public function resetPartialCreditNoteStatusFlowsRelatedByToStatusId($v = true)
    {
        $this->collCreditNoteStatusFlowsRelatedByToStatusIdPartial = $v;
    }

    /**
     * Initializes the collCreditNoteStatusFlowsRelatedByToStatusId collection.
     *
     * By default this just sets the collCreditNoteStatusFlowsRelatedByToStatusId collection to an empty array (like clearcollCreditNoteStatusFlowsRelatedByToStatusId());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param      boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initCreditNoteStatusFlowsRelatedByToStatusId($overrideExisting = true)
    {
        if (null !== $this->collCreditNoteStatusFlowsRelatedByToStatusId && !$overrideExisting) {
            return;
        }
        $this->collCreditNoteStatusFlowsRelatedByToStatusId = new ObjectCollection();
        $this->collCreditNoteStatusFlowsRelatedByToStatusId->setModel('\CreditNote\Model\CreditNoteStatusFlow');
    }

    /**
     * Gets an array of ChildCreditNoteStatusFlow objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this ChildCreditNoteStatus is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @return Collection|ChildCreditNoteStatusFlow[] List of ChildCreditNoteStatusFlow objects
     * @throws PropelException
     */
    public function getCreditNoteStatusFlowsRelatedByToStatusId($criteria = null, ConnectionInterface $con = null)
    {
        $partial = $this->collCreditNoteStatusFlowsRelatedByToStatusIdPartial && !$this->isNew();
        if (null === $this->collCreditNoteStatusFlowsRelatedByToStatusId || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collCreditNoteStatusFlowsRelatedByToStatusId) {
                // return empty collection
                $this->initCreditNoteStatusFlowsRelatedByToStatusId();
            } else {
                $collCreditNoteStatusFlowsRelatedByToStatusId = ChildCreditNoteStatusFlowQuery::create(null, $criteria)
                    ->filterByCreditNoteStatusRelatedByToStatusId($this)
                    ->find($con);

                if (null !== $criteria) {
                    if (false !== $this->collCreditNoteStatusFlowsRelatedByToStatusIdPartial && count($collCreditNoteStatusFlowsRelatedByToStatusId)) {
                        $this->initCreditNoteStatusFlowsRelatedByToStatusId(false);

                        foreach ($collCreditNoteStatusFlowsRelatedByToStatusId as $obj) {
                            if (false == $this->collCreditNoteStatusFlowsRelatedByToStatusId->contains($obj)) {
                                $this->collCreditNoteStatusFlowsRelatedByToStatusId->append($obj);
                            }
                        }

                        $this->collCreditNoteStatusFlowsRelatedByToStatusIdPartial = true;
                    }

                    reset($collCreditNoteStatusFlowsRelatedByToStatusId);

                    return $collCreditNoteStatusFlowsRelatedByToStatusId;
                }

                if ($partial && $this->collCreditNoteStatusFlowsRelatedByToStatusId) {
                    foreach ($this->collCreditNoteStatusFlowsRelatedByToStatusId as $obj) {
                        if ($obj->isNew()) {
                            $collCreditNoteStatusFlowsRelatedByToStatusId[] = $obj;
                        }
                    }
                }

                $this->collCreditNoteStatusFlowsRelatedByToStatusId = $collCreditNoteStatusFlowsRelatedByToStatusId;
                $this->collCreditNoteStatusFlowsRelatedByToStatusIdPartial = false;
            }
        }

        return $this->collCreditNoteStatusFlowsRelatedByToStatusId;
    }

    /**
     * Sets a collection of CreditNoteStatusFlowRelatedByToStatusId objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param      Collection $creditNoteStatusFlowsRelatedByToStatusId A Propel collection.
     * @param      ConnectionInterface $con Optional connection object
     * @return   ChildCreditNoteStatus The current object (for fluent API support)
     */
    public function setCreditNoteStatusFlowsRelatedByToStatusId(Collection $creditNoteStatusFlowsRelatedByToStatusId, ConnectionInterface $con = null)
    {
        $creditNoteStatusFlowsRelatedByToStatusIdToDelete = $this->getCreditNoteStatusFlowsRelatedByToStatusId(new Criteria(), $con)->diff($creditNoteStatusFlowsRelatedByToStatusId);


        $this->creditNoteStatusFlowsRelatedByToStatusIdScheduledForDeletion = $creditNoteStatusFlowsRelatedByToStatusIdToDelete;

        foreach ($creditNoteStatusFlowsRelatedByToStatusIdToDelete as $creditNoteStatusFlowRelatedByToStatusIdRemoved) {
            $creditNoteStatusFlowRelatedByToStatusIdRemoved->setCreditNoteStatusRelatedByToStatusId(null);
        }

        $this->collCreditNoteStatusFlowsRelatedByToStatusId = null;
        foreach ($creditNoteStatusFlowsRelatedByToStatusId as $creditNoteStatusFlowRelatedByToStatusId) {
            $this->addCreditNoteStatusFlowRelatedByToStatusId($creditNoteStatusFlowRelatedByToStatusId);
        }

        $this->collCreditNoteStatusFlowsRelatedByToStatusId = $creditNoteStatusFlowsRelatedByToStatusId;
        $this->collCreditNoteStatusFlowsRelatedByToStatusIdPartial = false;

        return $this;
    }

    /**
     * Returns the number of related CreditNoteStatusFlow objects.
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct
     * @param      ConnectionInterface $con
     * @return int             Count of related CreditNoteStatusFlow objects.
     * @throws PropelException
     */
    public function countCreditNoteStatusFlowsRelatedByToStatusId(Criteria $criteria = null, $distinct = false, ConnectionInterface $con = null)
    {
        $partial = $this->collCreditNoteStatusFlowsRelatedByToStatusIdPartial && !$this->isNew();
        if (null === $this->collCreditNoteStatusFlowsRelatedByToStatusId || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collCreditNoteStatusFlowsRelatedByToStatusId) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getCreditNoteStatusFlowsRelatedByToStatusId());
            }

            $query = ChildCreditNoteStatusFlowQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByCreditNoteStatusRelatedByToStatusId($this)
                ->count($con);
        }

        return count($this->collCreditNoteStatusFlowsRelatedByToStatusId);
    }

    /**
     * Method called to associate a ChildCreditNoteStatusFlow object to this object
     * through the ChildCreditNoteStatusFlow foreign key attribute.
     *
     * @param    ChildCreditNoteStatusFlow $l ChildCreditNoteStatusFlow
     * @return   \CreditNote\Model\CreditNoteStatus The current object (for fluent API support)
     */
    public function addCreditNoteStatusFlowRelatedByToStatusId(ChildCreditNoteStatusFlow $l)
    {
        if ($this->collCreditNoteStatusFlowsRelatedByToStatusId === null) {
            $this->initCreditNoteStatusFlowsRelatedByToStatusId();
            $this->collCreditNoteStatusFlowsRelatedByToStatusIdPartial = true;
        }

        if (!in_array($l, $this->collCreditNoteStatusFlowsRelatedByToStatusId->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddCreditNoteStatusFlowRelatedByToStatusId($l);
        }

        return $this;
    }

    /**
     * @param CreditNoteStatusFlowRelatedByToStatusId $creditNoteStatusFlowRelatedByToStatusId The creditNoteStatusFlowRelatedByToStatusId object to add.
     */
    protected function doAddCreditNoteStatusFlowRelatedByToStatusId($creditNoteStatusFlowRelatedByToStatusId)
    {
        $this->collCreditNoteStatusFlowsRelatedByToStatusId[]= $creditNoteStatusFlowRelatedByToStatusId;
        $creditNoteStatusFlowRelatedByToStatusId->setCreditNoteStatusRelatedByToStatusId($this);
    }

    /**
     * @param  CreditNoteStatusFlowRelatedByToStatusId $creditNoteStatusFlowRelatedByToStatusId The creditNoteStatusFlowRelatedByToStatusId object to remove.
     * @return ChildCreditNoteStatus The current object (for fluent API support)
     */
    public function removeCreditNoteStatusFlowRelatedByToStatusId($creditNoteStatusFlowRelatedByToStatusId)
    {
        if ($this->getCreditNoteStatusFlowsRelatedByToStatusId()->contains($creditNoteStatusFlowRelatedByToStatusId)) {
            $this->collCreditNoteStatusFlowsRelatedByToStatusId->remove($this->collCreditNoteStatusFlowsRelatedByToStatusId->search($creditNoteStatusFlowRelatedByToStatusId));
            if (null === $this->creditNoteStatusFlowsRelatedByToStatusIdScheduledForDeletion) {
                $this->creditNoteStatusFlowsRelatedByToStatusIdScheduledForDeletion = clone $this->collCreditNoteStatusFlowsRelatedByToStatusId;
                $this->creditNoteStatusFlowsRelatedByToStatusIdScheduledForDeletion->clear();
            }
            $this->creditNoteStatusFlowsRelatedByToStatusIdScheduledForDeletion[]= clone $creditNoteStatusFlowRelatedByToStatusId;
            $creditNoteStatusFlowRelatedByToStatusId->setCreditNoteStatusRelatedByToStatusId(null);
        }

        return $this;
    }

    /**
     * Clears out the collCreditNoteStatusI18ns collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return void
     * @see        addCreditNoteStatusI18ns()
     */
    public function clearCreditNoteStatusI18ns()
    {
        $this->collCreditNoteStatusI18ns = null; // important to set this to NULL since that means it is uninitialized
    }

    /**
     * Reset is the collCreditNoteStatusI18ns collection loaded partially.
     */
    public function resetPartialCreditNoteStatusI18ns($v = true)
    {
        $this->collCreditNoteStatusI18nsPartial = $v;
    }

    /**
     * Initializes the collCreditNoteStatusI18ns collection.
     *
     * By default this just sets the collCreditNoteStatusI18ns collection to an empty array (like clearcollCreditNoteStatusI18ns());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param      boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initCreditNoteStatusI18ns($overrideExisting = true)
    {
        if (null !== $this->collCreditNoteStatusI18ns && !$overrideExisting) {
            return;
        }
        $this->collCreditNoteStatusI18ns = new ObjectCollection();
        $this->collCreditNoteStatusI18ns->setModel('\CreditNote\Model\CreditNoteStatusI18n');
    }

    /**
     * Gets an array of ChildCreditNoteStatusI18n objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this ChildCreditNoteStatus is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @return Collection|ChildCreditNoteStatusI18n[] List of ChildCreditNoteStatusI18n objects
     * @throws PropelException
     */
    public function getCreditNoteStatusI18ns($criteria = null, ConnectionInterface $con = null)
    {
        $partial = $this->collCreditNoteStatusI18nsPartial && !$this->isNew();
        if (null === $this->collCreditNoteStatusI18ns || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collCreditNoteStatusI18ns) {
                // return empty collection
                $this->initCreditNoteStatusI18ns();
            } else {
                $collCreditNoteStatusI18ns = ChildCreditNoteStatusI18nQuery::create(null, $criteria)
                    ->filterByCreditNoteStatus($this)
                    ->find($con);

                if (null !== $criteria) {
                    if (false !== $this->collCreditNoteStatusI18nsPartial && count($collCreditNoteStatusI18ns)) {
                        $this->initCreditNoteStatusI18ns(false);

                        foreach ($collCreditNoteStatusI18ns as $obj) {
                            if (false == $this->collCreditNoteStatusI18ns->contains($obj)) {
                                $this->collCreditNoteStatusI18ns->append($obj);
                            }
                        }

                        $this->collCreditNoteStatusI18nsPartial = true;
                    }

                    reset($collCreditNoteStatusI18ns);

                    return $collCreditNoteStatusI18ns;
                }

                if ($partial && $this->collCreditNoteStatusI18ns) {
                    foreach ($this->collCreditNoteStatusI18ns as $obj) {
                        if ($obj->isNew()) {
                            $collCreditNoteStatusI18ns[] = $obj;
                        }
                    }
                }

                $this->collCreditNoteStatusI18ns = $collCreditNoteStatusI18ns;
                $this->collCreditNoteStatusI18nsPartial = false;
            }
        }

        return $this->collCreditNoteStatusI18ns;
    }

    /**
     * Sets a collection of CreditNoteStatusI18n objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param      Collection $creditNoteStatusI18ns A Propel collection.
     * @param      ConnectionInterface $con Optional connection object
     * @return   ChildCreditNoteStatus The current object (for fluent API support)
     */
    public function setCreditNoteStatusI18ns(Collection $creditNoteStatusI18ns, ConnectionInterface $con = null)
    {
        $creditNoteStatusI18nsToDelete = $this->getCreditNoteStatusI18ns(new Criteria(), $con)->diff($creditNoteStatusI18ns);


        //since at least one column in the foreign key is at the same time a PK
        //we can not just set a PK to NULL in the lines below. We have to store
        //a backup of all values, so we are able to manipulate these items based on the onDelete value later.
        $this->creditNoteStatusI18nsScheduledForDeletion = clone $creditNoteStatusI18nsToDelete;

        foreach ($creditNoteStatusI18nsToDelete as $creditNoteStatusI18nRemoved) {
            $creditNoteStatusI18nRemoved->setCreditNoteStatus(null);
        }

        $this->collCreditNoteStatusI18ns = null;
        foreach ($creditNoteStatusI18ns as $creditNoteStatusI18n) {
            $this->addCreditNoteStatusI18n($creditNoteStatusI18n);
        }

        $this->collCreditNoteStatusI18ns = $creditNoteStatusI18ns;
        $this->collCreditNoteStatusI18nsPartial = false;

        return $this;
    }

    /**
     * Returns the number of related CreditNoteStatusI18n objects.
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct
     * @param      ConnectionInterface $con
     * @return int             Count of related CreditNoteStatusI18n objects.
     * @throws PropelException
     */
    public function countCreditNoteStatusI18ns(Criteria $criteria = null, $distinct = false, ConnectionInterface $con = null)
    {
        $partial = $this->collCreditNoteStatusI18nsPartial && !$this->isNew();
        if (null === $this->collCreditNoteStatusI18ns || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collCreditNoteStatusI18ns) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getCreditNoteStatusI18ns());
            }

            $query = ChildCreditNoteStatusI18nQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByCreditNoteStatus($this)
                ->count($con);
        }

        return count($this->collCreditNoteStatusI18ns);
    }

    /**
     * Method called to associate a ChildCreditNoteStatusI18n object to this object
     * through the ChildCreditNoteStatusI18n foreign key attribute.
     *
     * @param    ChildCreditNoteStatusI18n $l ChildCreditNoteStatusI18n
     * @return   \CreditNote\Model\CreditNoteStatus The current object (for fluent API support)
     */
    public function addCreditNoteStatusI18n(ChildCreditNoteStatusI18n $l)
    {
        if ($l && $locale = $l->getLocale()) {
            $this->setLocale($locale);
            $this->currentTranslations[$locale] = $l;
        }
        if ($this->collCreditNoteStatusI18ns === null) {
            $this->initCreditNoteStatusI18ns();
            $this->collCreditNoteStatusI18nsPartial = true;
        }

        if (!in_array($l, $this->collCreditNoteStatusI18ns->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddCreditNoteStatusI18n($l);
        }

        return $this;
    }

    /**
     * @param CreditNoteStatusI18n $creditNoteStatusI18n The creditNoteStatusI18n object to add.
     */
    protected function doAddCreditNoteStatusI18n($creditNoteStatusI18n)
    {
        $this->collCreditNoteStatusI18ns[]= $creditNoteStatusI18n;
        $creditNoteStatusI18n->setCreditNoteStatus($this);
    }

    /**
     * @param  CreditNoteStatusI18n $creditNoteStatusI18n The creditNoteStatusI18n object to remove.
     * @return ChildCreditNoteStatus The current object (for fluent API support)
     */
    public function removeCreditNoteStatusI18n($creditNoteStatusI18n)
    {
        if ($this->getCreditNoteStatusI18ns()->contains($creditNoteStatusI18n)) {
            $this->collCreditNoteStatusI18ns->remove($this->collCreditNoteStatusI18ns->search($creditNoteStatusI18n));
            if (null === $this->creditNoteStatusI18nsScheduledForDeletion) {
                $this->creditNoteStatusI18nsScheduledForDeletion = clone $this->collCreditNoteStatusI18ns;
                $this->creditNoteStatusI18nsScheduledForDeletion->clear();
            }
            $this->creditNoteStatusI18nsScheduledForDeletion[]= clone $creditNoteStatusI18n;
            $creditNoteStatusI18n->setCreditNoteStatus(null);
        }

        return $this;
    }

    /**
     * Clears the current object and sets all attributes to their default values
     */
    public function clear()
    {
        $this->id = null;
        $this->code = null;
        $this->color = null;
        $this->invoiced = null;
        $this->used = null;
        $this->position = null;
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
            if ($this->collCreditNotes) {
                foreach ($this->collCreditNotes as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collCreditNoteStatusFlowsRelatedByFromStatusId) {
                foreach ($this->collCreditNoteStatusFlowsRelatedByFromStatusId as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collCreditNoteStatusFlowsRelatedByToStatusId) {
                foreach ($this->collCreditNoteStatusFlowsRelatedByToStatusId as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collCreditNoteStatusI18ns) {
                foreach ($this->collCreditNoteStatusI18ns as $o) {
                    $o->clearAllReferences($deep);
                }
            }
        } // if ($deep)

        // i18n behavior
        $this->currentLocale = 'en_US';
        $this->currentTranslations = null;

        $this->collCreditNotes = null;
        $this->collCreditNoteStatusFlowsRelatedByFromStatusId = null;
        $this->collCreditNoteStatusFlowsRelatedByToStatusId = null;
        $this->collCreditNoteStatusI18ns = null;
    }

    /**
     * Return the string representation of this object
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->exportTo(CreditNoteStatusTableMap::DEFAULT_STRING_FORMAT);
    }

    // i18n behavior

    /**
     * Sets the locale for translations
     *
     * @param     string $locale Locale to use for the translation, e.g. 'fr_FR'
     *
     * @return    ChildCreditNoteStatus The current object (for fluent API support)
     */
    public function setLocale($locale = 'en_US')
    {
        $this->currentLocale = $locale;

        return $this;
    }

    /**
     * Gets the locale for translations
     *
     * @return    string $locale Locale to use for the translation, e.g. 'fr_FR'
     */
    public function getLocale()
    {
        return $this->currentLocale;
    }

    /**
     * Returns the current translation for a given locale
     *
     * @param     string $locale Locale to use for the translation, e.g. 'fr_FR'
     * @param     ConnectionInterface $con an optional connection object
     *
     * @return ChildCreditNoteStatusI18n */
    public function getTranslation($locale = 'en_US', ConnectionInterface $con = null)
    {
        if (!isset($this->currentTranslations[$locale])) {
            if (null !== $this->collCreditNoteStatusI18ns) {
                foreach ($this->collCreditNoteStatusI18ns as $translation) {
                    if ($translation->getLocale() == $locale) {
                        $this->currentTranslations[$locale] = $translation;

                        return $translation;
                    }
                }
            }
            if ($this->isNew()) {
                $translation = new ChildCreditNoteStatusI18n();
                $translation->setLocale($locale);
            } else {
                $translation = ChildCreditNoteStatusI18nQuery::create()
                    ->filterByPrimaryKey(array($this->getPrimaryKey(), $locale))
                    ->findOneOrCreate($con);
                $this->currentTranslations[$locale] = $translation;
            }
            $this->addCreditNoteStatusI18n($translation);
        }

        return $this->currentTranslations[$locale];
    }

    /**
     * Remove the translation for a given locale
     *
     * @param     string $locale Locale to use for the translation, e.g. 'fr_FR'
     * @param     ConnectionInterface $con an optional connection object
     *
     * @return    ChildCreditNoteStatus The current object (for fluent API support)
     */
    public function removeTranslation($locale = 'en_US', ConnectionInterface $con = null)
    {
        if (!$this->isNew()) {
            ChildCreditNoteStatusI18nQuery::create()
                ->filterByPrimaryKey(array($this->getPrimaryKey(), $locale))
                ->delete($con);
        }
        if (isset($this->currentTranslations[$locale])) {
            unset($this->currentTranslations[$locale]);
        }
        foreach ($this->collCreditNoteStatusI18ns as $key => $translation) {
            if ($translation->getLocale() == $locale) {
                unset($this->collCreditNoteStatusI18ns[$key]);
                break;
            }
        }

        return $this;
    }

    /**
     * Returns the current translation
     *
     * @param     ConnectionInterface $con an optional connection object
     *
     * @return ChildCreditNoteStatusI18n */
    public function getCurrentTranslation(ConnectionInterface $con = null)
    {
        return $this->getTranslation($this->getLocale(), $con);
    }


        /**
         * Get the [title] column value.
         *
         * @return   string
         */
        public function getTitle()
        {
        return $this->getCurrentTranslation()->getTitle();
    }


        /**
         * Set the value of [title] column.
         *
         * @param      string $v new value
         * @return   \CreditNote\Model\CreditNoteStatusI18n The current object (for fluent API support)
         */
        public function setTitle($v)
        {    $this->getCurrentTranslation()->setTitle($v);

        return $this;
    }


        /**
         * Get the [description] column value.
         *
         * @return   string
         */
        public function getDescription()
        {
        return $this->getCurrentTranslation()->getDescription();
    }


        /**
         * Set the value of [description] column.
         *
         * @param      string $v new value
         * @return   \CreditNote\Model\CreditNoteStatusI18n The current object (for fluent API support)
         */
        public function setDescription($v)
        {    $this->getCurrentTranslation()->setDescription($v);

        return $this;
    }


        /**
         * Get the [chapo] column value.
         *
         * @return   string
         */
        public function getChapo()
        {
        return $this->getCurrentTranslation()->getChapo();
    }


        /**
         * Set the value of [chapo] column.
         *
         * @param      string $v new value
         * @return   \CreditNote\Model\CreditNoteStatusI18n The current object (for fluent API support)
         */
        public function setChapo($v)
        {    $this->getCurrentTranslation()->setChapo($v);

        return $this;
    }


        /**
         * Get the [postscriptum] column value.
         *
         * @return   string
         */
        public function getPostscriptum()
        {
        return $this->getCurrentTranslation()->getPostscriptum();
    }


        /**
         * Set the value of [postscriptum] column.
         *
         * @param      string $v new value
         * @return   \CreditNote\Model\CreditNoteStatusI18n The current object (for fluent API support)
         */
        public function setPostscriptum($v)
        {    $this->getCurrentTranslation()->setPostscriptum($v);

        return $this;
    }

    // timestampable behavior

    /**
     * Mark the current object so that the update date doesn't get updated during next save
     *
     * @return     ChildCreditNoteStatus The current object (for fluent API support)
     */
    public function keepUpdateDateUnchanged()
    {
        $this->modifiedColumns[CreditNoteStatusTableMap::UPDATED_AT] = true;

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
