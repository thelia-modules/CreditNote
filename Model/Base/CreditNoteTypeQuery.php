<?php

namespace CreditNote\Model\Base;

use \Exception;
use \PDO;
use CreditNote\Model\CreditNoteType as ChildCreditNoteType;
use CreditNote\Model\CreditNoteTypeI18nQuery as ChildCreditNoteTypeI18nQuery;
use CreditNote\Model\CreditNoteTypeQuery as ChildCreditNoteTypeQuery;
use CreditNote\Model\Map\CreditNoteTypeTableMap;
use Propel\Runtime\Propel;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\ActiveQuery\ModelJoin;
use Propel\Runtime\Collection\ObjectCollection;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\Exception\PropelException;

/**
 * Base class that represents a query for the 'credit_note_type' table.
 *
 *
 *
 * @method     ChildCreditNoteTypeQuery orderById($order = Criteria::ASC) Order by the id column
 * @method     ChildCreditNoteTypeQuery orderByCode($order = Criteria::ASC) Order by the code column
 * @method     ChildCreditNoteTypeQuery orderByColor($order = Criteria::ASC) Order by the color column
 * @method     ChildCreditNoteTypeQuery orderByPosition($order = Criteria::ASC) Order by the position column
 * @method     ChildCreditNoteTypeQuery orderByRequiredOrder($order = Criteria::ASC) Order by the required_order column
 * @method     ChildCreditNoteTypeQuery orderByCreatedAt($order = Criteria::ASC) Order by the created_at column
 * @method     ChildCreditNoteTypeQuery orderByUpdatedAt($order = Criteria::ASC) Order by the updated_at column
 *
 * @method     ChildCreditNoteTypeQuery groupById() Group by the id column
 * @method     ChildCreditNoteTypeQuery groupByCode() Group by the code column
 * @method     ChildCreditNoteTypeQuery groupByColor() Group by the color column
 * @method     ChildCreditNoteTypeQuery groupByPosition() Group by the position column
 * @method     ChildCreditNoteTypeQuery groupByRequiredOrder() Group by the required_order column
 * @method     ChildCreditNoteTypeQuery groupByCreatedAt() Group by the created_at column
 * @method     ChildCreditNoteTypeQuery groupByUpdatedAt() Group by the updated_at column
 *
 * @method     ChildCreditNoteTypeQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     ChildCreditNoteTypeQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     ChildCreditNoteTypeQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     ChildCreditNoteTypeQuery leftJoinWith($relation) Adds a LEFT JOIN clause and with to the query
 * @method     ChildCreditNoteTypeQuery rightJoinWith($relation) Adds a RIGHT JOIN clause and with to the query
 * @method     ChildCreditNoteTypeQuery innerJoinWith($relation) Adds a INNER JOIN clause and with to the query
 *
 * @method     ChildCreditNoteTypeQuery leftJoinCreditNote($relationAlias = null) Adds a LEFT JOIN clause to the query using the CreditNote relation
 * @method     ChildCreditNoteTypeQuery rightJoinCreditNote($relationAlias = null) Adds a RIGHT JOIN clause to the query using the CreditNote relation
 * @method     ChildCreditNoteTypeQuery innerJoinCreditNote($relationAlias = null) Adds a INNER JOIN clause to the query using the CreditNote relation
 *
 * @method     ChildCreditNoteTypeQuery joinWithCreditNote($joinType = Criteria::INNER_JOIN) Adds a join clause and with to the query using the CreditNote relation
 *
 * @method     ChildCreditNoteTypeQuery leftJoinWithCreditNote() Adds a LEFT JOIN clause and with to the query using the CreditNote relation
 * @method     ChildCreditNoteTypeQuery rightJoinWithCreditNote() Adds a RIGHT JOIN clause and with to the query using the CreditNote relation
 * @method     ChildCreditNoteTypeQuery innerJoinWithCreditNote() Adds a INNER JOIN clause and with to the query using the CreditNote relation
 *
 * @method     ChildCreditNoteTypeQuery leftJoinCreditNoteTypeI18n($relationAlias = null) Adds a LEFT JOIN clause to the query using the CreditNoteTypeI18n relation
 * @method     ChildCreditNoteTypeQuery rightJoinCreditNoteTypeI18n($relationAlias = null) Adds a RIGHT JOIN clause to the query using the CreditNoteTypeI18n relation
 * @method     ChildCreditNoteTypeQuery innerJoinCreditNoteTypeI18n($relationAlias = null) Adds a INNER JOIN clause to the query using the CreditNoteTypeI18n relation
 *
 * @method     ChildCreditNoteTypeQuery joinWithCreditNoteTypeI18n($joinType = Criteria::INNER_JOIN) Adds a join clause and with to the query using the CreditNoteTypeI18n relation
 *
 * @method     ChildCreditNoteTypeQuery leftJoinWithCreditNoteTypeI18n() Adds a LEFT JOIN clause and with to the query using the CreditNoteTypeI18n relation
 * @method     ChildCreditNoteTypeQuery rightJoinWithCreditNoteTypeI18n() Adds a RIGHT JOIN clause and with to the query using the CreditNoteTypeI18n relation
 * @method     ChildCreditNoteTypeQuery innerJoinWithCreditNoteTypeI18n() Adds a INNER JOIN clause and with to the query using the CreditNoteTypeI18n relation
 *
 * @method     \CreditNote\Model\CreditNoteQuery|\CreditNote\Model\CreditNoteTypeI18nQuery endUse() Finalizes a secondary criteria and merges it with its primary Criteria
 *
 * @method     ChildCreditNoteType findOne(ConnectionInterface $con = null) Return the first ChildCreditNoteType matching the query
 * @method     ChildCreditNoteType findOneOrCreate(ConnectionInterface $con = null) Return the first ChildCreditNoteType matching the query, or a new ChildCreditNoteType object populated from the query conditions when no match is found
 *
 * @method     ChildCreditNoteType findOneById(int $id) Return the first ChildCreditNoteType filtered by the id column
 * @method     ChildCreditNoteType findOneByCode(string $code) Return the first ChildCreditNoteType filtered by the code column
 * @method     ChildCreditNoteType findOneByColor(string $color) Return the first ChildCreditNoteType filtered by the color column
 * @method     ChildCreditNoteType findOneByPosition(int $position) Return the first ChildCreditNoteType filtered by the position column
 * @method     ChildCreditNoteType findOneByRequiredOrder(boolean $required_order) Return the first ChildCreditNoteType filtered by the required_order column
 * @method     ChildCreditNoteType findOneByCreatedAt(string $created_at) Return the first ChildCreditNoteType filtered by the created_at column
 * @method     ChildCreditNoteType findOneByUpdatedAt(string $updated_at) Return the first ChildCreditNoteType filtered by the updated_at column *

 * @method     ChildCreditNoteType requirePk($key, ConnectionInterface $con = null) Return the ChildCreditNoteType by primary key and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildCreditNoteType requireOne(ConnectionInterface $con = null) Return the first ChildCreditNoteType matching the query and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 *
 * @method     ChildCreditNoteType requireOneById(int $id) Return the first ChildCreditNoteType filtered by the id column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildCreditNoteType requireOneByCode(string $code) Return the first ChildCreditNoteType filtered by the code column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildCreditNoteType requireOneByColor(string $color) Return the first ChildCreditNoteType filtered by the color column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildCreditNoteType requireOneByPosition(int $position) Return the first ChildCreditNoteType filtered by the position column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildCreditNoteType requireOneByRequiredOrder(boolean $required_order) Return the first ChildCreditNoteType filtered by the required_order column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildCreditNoteType requireOneByCreatedAt(string $created_at) Return the first ChildCreditNoteType filtered by the created_at column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildCreditNoteType requireOneByUpdatedAt(string $updated_at) Return the first ChildCreditNoteType filtered by the updated_at column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 *
 * @method     ChildCreditNoteType[]|ObjectCollection find(ConnectionInterface $con = null) Return ChildCreditNoteType objects based on current ModelCriteria
 * @method     ChildCreditNoteType[]|ObjectCollection findById(int $id) Return ChildCreditNoteType objects filtered by the id column
 * @method     ChildCreditNoteType[]|ObjectCollection findByCode(string $code) Return ChildCreditNoteType objects filtered by the code column
 * @method     ChildCreditNoteType[]|ObjectCollection findByColor(string $color) Return ChildCreditNoteType objects filtered by the color column
 * @method     ChildCreditNoteType[]|ObjectCollection findByPosition(int $position) Return ChildCreditNoteType objects filtered by the position column
 * @method     ChildCreditNoteType[]|ObjectCollection findByRequiredOrder(boolean $required_order) Return ChildCreditNoteType objects filtered by the required_order column
 * @method     ChildCreditNoteType[]|ObjectCollection findByCreatedAt(string $created_at) Return ChildCreditNoteType objects filtered by the created_at column
 * @method     ChildCreditNoteType[]|ObjectCollection findByUpdatedAt(string $updated_at) Return ChildCreditNoteType objects filtered by the updated_at column
 * @method     ChildCreditNoteType[]|\Propel\Runtime\Util\PropelModelPager paginate($page = 1, $maxPerPage = 10, ConnectionInterface $con = null) Issue a SELECT query based on the current ModelCriteria and uses a page and a maximum number of results per page to compute an offset and a limit
 *
 */
abstract class CreditNoteTypeQuery extends ModelCriteria
{
    protected $entityNotFoundExceptionClass = '\\Propel\\Runtime\\Exception\\EntityNotFoundException';

    /**
     * Initializes internal state of \CreditNote\Model\Base\CreditNoteTypeQuery object.
     *
     * @param     string $dbName The database name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = 'thelia', $modelName = '\\CreditNote\\Model\\CreditNoteType', $modelAlias = null)
    {
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new ChildCreditNoteTypeQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param     Criteria $criteria Optional Criteria to build the query from
     *
     * @return ChildCreditNoteTypeQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof ChildCreditNoteTypeQuery) {
            return $criteria;
        }
        $query = new ChildCreditNoteTypeQuery();
        if (null !== $modelAlias) {
            $query->setModelAlias($modelAlias);
        }
        if ($criteria instanceof Criteria) {
            $query->mergeWith($criteria);
        }

        return $query;
    }

    /**
     * Find object by primary key.
     * Propel uses the instance pool to skip the database if the object exists.
     * Go fast if the query is untouched.
     *
     * <code>
     * $obj  = $c->findPk(12, $con);
     * </code>
     *
     * @param mixed $key Primary key to use for the query
     * @param ConnectionInterface $con an optional connection object
     *
     * @return ChildCreditNoteType|array|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }

        if ($con === null) {
            $con = Propel::getServiceContainer()->getReadConnection(CreditNoteTypeTableMap::DATABASE_NAME);
        }

        $this->basePreSelect($con);

        if (
            $this->formatter || $this->modelAlias || $this->with || $this->select
            || $this->selectColumns || $this->asColumns || $this->selectModifiers
            || $this->map || $this->having || $this->joins
        ) {
            return $this->findPkComplex($key, $con);
        }

        if ((null !== ($obj = CreditNoteTypeTableMap::getInstanceFromPool(null === $key || is_scalar($key) || is_callable([$key, '__toString']) ? (string) $key : $key)))) {
            // the object is already in the instance pool
            return $obj;
        }

        return $this->findPkSimple($key, $con);
    }

    /**
     * Find object by primary key using raw SQL to go fast.
     * Bypass doSelect() and the object formatter by using generated code.
     *
     * @param     mixed $key Primary key to use for the query
     * @param     ConnectionInterface $con A connection object
     *
     * @throws \Propel\Runtime\Exception\PropelException
     *
     * @return ChildCreditNoteType A model object, or null if the key is not found
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT `id`, `code`, `color`, `position`, `required_order`, `created_at`, `updated_at` FROM `credit_note_type` WHERE `id` = :p0';
        try {
            $stmt = $con->prepare($sql);
            $stmt->bindValue(':p0', $key, PDO::PARAM_INT);
            $stmt->execute();
        } catch (Exception $e) {
            Propel::log($e->getMessage(), Propel::LOG_ERR);
            throw new PropelException(sprintf('Unable to execute SELECT statement [%s]', $sql), 0, $e);
        }
        $obj = null;
        if ($row = $stmt->fetch(\PDO::FETCH_NUM)) {
            /** @var ChildCreditNoteType $obj */
            $obj = new ChildCreditNoteType();
            $obj->hydrate($row);
            CreditNoteTypeTableMap::addInstanceToPool($obj, null === $key || is_scalar($key) || is_callable([$key, '__toString']) ? (string) $key : $key);
        }
        $stmt->closeCursor();

        return $obj;
    }

    /**
     * Find object by primary key.
     *
     * @param     mixed $key Primary key to use for the query
     * @param     ConnectionInterface $con A connection object
     *
     * @return ChildCreditNoteType|array|mixed the result, formatted by the current formatter
     */
    protected function findPkComplex($key, $con)
    {
        // As the query uses a PK condition, no limit(1) is necessary.
        $criteria = $this->isKeepQuery() ? clone $this : $this;
        $dataFetcher = $criteria
            ->filterByPrimaryKey($key)
            ->doSelect($con);

        return $criteria->getFormatter()->init($criteria)->formatOne($dataFetcher);
    }

    /**
     * Find objects by primary key
     * <code>
     * $objs = $c->findPks(array(12, 56, 832), $con);
     * </code>
     * @param     array $keys Primary keys to use for the query
     * @param     ConnectionInterface $con an optional connection object
     *
     * @return ObjectCollection|array|mixed the list of results, formatted by the current formatter
     */
    public function findPks($keys, $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getReadConnection($this->getDbName());
        }
        $this->basePreSelect($con);
        $criteria = $this->isKeepQuery() ? clone $this : $this;
        $dataFetcher = $criteria
            ->filterByPrimaryKeys($keys)
            ->doSelect($con);

        return $criteria->getFormatter()->init($criteria)->format($dataFetcher);
    }

    /**
     * Filter the query by primary key
     *
     * @param     mixed $key Primary key to use for the query
     *
     * @return $this|ChildCreditNoteTypeQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(CreditNoteTypeTableMap::COL_ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return $this|ChildCreditNoteTypeQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(CreditNoteTypeTableMap::COL_ID, $keys, Criteria::IN);
    }

    /**
     * Filter the query on the id column
     *
     * Example usage:
     * <code>
     * $query->filterById(1234); // WHERE id = 1234
     * $query->filterById(array(12, 34)); // WHERE id IN (12, 34)
     * $query->filterById(array('min' => 12)); // WHERE id > 12
     * </code>
     *
     * @param     mixed $id The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for \in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildCreditNoteTypeQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (\is_array($id)) {
            $useMinMax = false;
            if (isset($id['min'])) {
                $this->addUsingAlias(CreditNoteTypeTableMap::COL_ID, $id['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($id['max'])) {
                $this->addUsingAlias(CreditNoteTypeTableMap::COL_ID, $id['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CreditNoteTypeTableMap::COL_ID, $id, $comparison);
    }

    /**
     * Filter the query on the code column
     *
     * Example usage:
     * <code>
     * $query->filterByCode('fooValue');   // WHERE code = 'fooValue'
     * $query->filterByCode('%fooValue%', Criteria::LIKE); // WHERE code LIKE '%fooValue%'
     * </code>
     *
     * @param     string $code The value to use as filter.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildCreditNoteTypeQuery The current query, for fluid interface
     */
    public function filterByCode($code = null, $comparison = null)
    {
        if (null === $comparison) {
            if (\is_array($code)) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CreditNoteTypeTableMap::COL_CODE, $code, $comparison);
    }

    /**
     * Filter the query on the color column
     *
     * Example usage:
     * <code>
     * $query->filterByColor('fooValue');   // WHERE color = 'fooValue'
     * $query->filterByColor('%fooValue%', Criteria::LIKE); // WHERE color LIKE '%fooValue%'
     * </code>
     *
     * @param     string $color The value to use as filter.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildCreditNoteTypeQuery The current query, for fluid interface
     */
    public function filterByColor($color = null, $comparison = null)
    {
        if (null === $comparison) {
            if (\is_array($color)) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CreditNoteTypeTableMap::COL_COLOR, $color, $comparison);
    }

    /**
     * Filter the query on the position column
     *
     * Example usage:
     * <code>
     * $query->filterByPosition(1234); // WHERE position = 1234
     * $query->filterByPosition(array(12, 34)); // WHERE position IN (12, 34)
     * $query->filterByPosition(array('min' => 12)); // WHERE position > 12
     * </code>
     *
     * @param     mixed $position The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for \in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildCreditNoteTypeQuery The current query, for fluid interface
     */
    public function filterByPosition($position = null, $comparison = null)
    {
        if (\is_array($position)) {
            $useMinMax = false;
            if (isset($position['min'])) {
                $this->addUsingAlias(CreditNoteTypeTableMap::COL_POSITION, $position['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($position['max'])) {
                $this->addUsingAlias(CreditNoteTypeTableMap::COL_POSITION, $position['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CreditNoteTypeTableMap::COL_POSITION, $position, $comparison);
    }

    /**
     * Filter the query on the required_order column
     *
     * Example usage:
     * <code>
     * $query->filterByRequiredOrder(true); // WHERE required_order = true
     * $query->filterByRequiredOrder('yes'); // WHERE required_order = true
     * </code>
     *
     * @param     boolean|string $requiredOrder The value to use as filter.
     *              Non-boolean arguments are converted using the following rules:
     *                * 1, '1', 'true',  'on',  and 'yes' are converted to boolean true
     *                * 0, '0', 'false', 'off', and 'no'  are converted to boolean false
     *              Check on string values is case insensitive (so 'FaLsE' is seen as 'false').
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildCreditNoteTypeQuery The current query, for fluid interface
     */
    public function filterByRequiredOrder($requiredOrder = null, $comparison = null)
    {
        if (\is_string($requiredOrder)) {
            $requiredOrder = \in_array(strtolower($requiredOrder), array('false', 'off', '-', 'no', 'n', '0', '')) ? false : true;
        }

        return $this->addUsingAlias(CreditNoteTypeTableMap::COL_REQUIRED_ORDER, $requiredOrder, $comparison);
    }

    /**
     * Filter the query on the created_at column
     *
     * Example usage:
     * <code>
     * $query->filterByCreatedAt('2011-03-14'); // WHERE created_at = '2011-03-14'
     * $query->filterByCreatedAt('now'); // WHERE created_at = '2011-03-14'
     * $query->filterByCreatedAt(array('max' => 'yesterday')); // WHERE created_at > '2011-03-13'
     * </code>
     *
     * @param     mixed $createdAt The value to use as filter.
     *              Values can be integers (unix timestamps), DateTime objects, or strings.
     *              Empty strings are treated as NULL.
     *              Use scalar values for equality.
     *              Use array values for \in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildCreditNoteTypeQuery The current query, for fluid interface
     */
    public function filterByCreatedAt($createdAt = null, $comparison = null)
    {
        if (\is_array($createdAt)) {
            $useMinMax = false;
            if (isset($createdAt['min'])) {
                $this->addUsingAlias(CreditNoteTypeTableMap::COL_CREATED_AT, $createdAt['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($createdAt['max'])) {
                $this->addUsingAlias(CreditNoteTypeTableMap::COL_CREATED_AT, $createdAt['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CreditNoteTypeTableMap::COL_CREATED_AT, $createdAt, $comparison);
    }

    /**
     * Filter the query on the updated_at column
     *
     * Example usage:
     * <code>
     * $query->filterByUpdatedAt('2011-03-14'); // WHERE updated_at = '2011-03-14'
     * $query->filterByUpdatedAt('now'); // WHERE updated_at = '2011-03-14'
     * $query->filterByUpdatedAt(array('max' => 'yesterday')); // WHERE updated_at > '2011-03-13'
     * </code>
     *
     * @param     mixed $updatedAt The value to use as filter.
     *              Values can be integers (unix timestamps), DateTime objects, or strings.
     *              Empty strings are treated as NULL.
     *              Use scalar values for equality.
     *              Use array values for \in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildCreditNoteTypeQuery The current query, for fluid interface
     */
    public function filterByUpdatedAt($updatedAt = null, $comparison = null)
    {
        if (\is_array($updatedAt)) {
            $useMinMax = false;
            if (isset($updatedAt['min'])) {
                $this->addUsingAlias(CreditNoteTypeTableMap::COL_UPDATED_AT, $updatedAt['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($updatedAt['max'])) {
                $this->addUsingAlias(CreditNoteTypeTableMap::COL_UPDATED_AT, $updatedAt['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CreditNoteTypeTableMap::COL_UPDATED_AT, $updatedAt, $comparison);
    }

    /**
     * Filter the query by a related \CreditNote\Model\CreditNote object
     *
     * @param \CreditNote\Model\CreditNote|ObjectCollection $creditNote the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildCreditNoteTypeQuery The current query, for fluid interface
     */
    public function filterByCreditNote($creditNote, $comparison = null)
    {
        if ($creditNote instanceof \CreditNote\Model\CreditNote) {
            return $this
                ->addUsingAlias(CreditNoteTypeTableMap::COL_ID, $creditNote->getTypeId(), $comparison);
        } elseif ($creditNote instanceof ObjectCollection) {
            return $this
                ->useCreditNoteQuery()
                ->filterByPrimaryKeys($creditNote->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByCreditNote() only accepts arguments of type \CreditNote\Model\CreditNote or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the CreditNote relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return $this|ChildCreditNoteTypeQuery The current query, for fluid interface
     */
    public function joinCreditNote($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('CreditNote');

        // create a ModelJoin object for this join
        $join = new ModelJoin();
        $join->setJoinType($joinType);
        $join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
        if ($previousJoin = $this->getPreviousJoin()) {
            $join->setPreviousJoin($previousJoin);
        }

        // add the ModelJoin to the current object
        if ($relationAlias) {
            $this->addAlias($relationAlias, $relationMap->getRightTable()->getName());
            $this->addJoinObject($join, $relationAlias);
        } else {
            $this->addJoinObject($join, 'CreditNote');
        }

        return $this;
    }

    /**
     * Use the CreditNote relation CreditNote object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return \CreditNote\Model\CreditNoteQuery A secondary query class using the current class as primary query
     */
    public function useCreditNoteQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinCreditNote($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'CreditNote', '\CreditNote\Model\CreditNoteQuery');
    }

    /**
     * Filter the query by a related \CreditNote\Model\CreditNoteTypeI18n object
     *
     * @param \CreditNote\Model\CreditNoteTypeI18n|ObjectCollection $creditNoteTypeI18n the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildCreditNoteTypeQuery The current query, for fluid interface
     */
    public function filterByCreditNoteTypeI18n($creditNoteTypeI18n, $comparison = null)
    {
        if ($creditNoteTypeI18n instanceof \CreditNote\Model\CreditNoteTypeI18n) {
            return $this
                ->addUsingAlias(CreditNoteTypeTableMap::COL_ID, $creditNoteTypeI18n->getId(), $comparison);
        } elseif ($creditNoteTypeI18n instanceof ObjectCollection) {
            return $this
                ->useCreditNoteTypeI18nQuery()
                ->filterByPrimaryKeys($creditNoteTypeI18n->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByCreditNoteTypeI18n() only accepts arguments of type \CreditNote\Model\CreditNoteTypeI18n or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the CreditNoteTypeI18n relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return $this|ChildCreditNoteTypeQuery The current query, for fluid interface
     */
    public function joinCreditNoteTypeI18n($relationAlias = null, $joinType = 'LEFT JOIN')
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('CreditNoteTypeI18n');

        // create a ModelJoin object for this join
        $join = new ModelJoin();
        $join->setJoinType($joinType);
        $join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
        if ($previousJoin = $this->getPreviousJoin()) {
            $join->setPreviousJoin($previousJoin);
        }

        // add the ModelJoin to the current object
        if ($relationAlias) {
            $this->addAlias($relationAlias, $relationMap->getRightTable()->getName());
            $this->addJoinObject($join, $relationAlias);
        } else {
            $this->addJoinObject($join, 'CreditNoteTypeI18n');
        }

        return $this;
    }

    /**
     * Use the CreditNoteTypeI18n relation CreditNoteTypeI18n object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return \CreditNote\Model\CreditNoteTypeI18nQuery A secondary query class using the current class as primary query
     */
    public function useCreditNoteTypeI18nQuery($relationAlias = null, $joinType = 'LEFT JOIN')
    {
        return $this
            ->joinCreditNoteTypeI18n($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'CreditNoteTypeI18n', '\CreditNote\Model\CreditNoteTypeI18nQuery');
    }

    /**
     * Exclude object from result
     *
     * @param   ChildCreditNoteType $creditNoteType Object to remove from the list of results
     *
     * @return $this|ChildCreditNoteTypeQuery The current query, for fluid interface
     */
    public function prune($creditNoteType = null)
    {
        if ($creditNoteType) {
            $this->addUsingAlias(CreditNoteTypeTableMap::COL_ID, $creditNoteType->getId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

    /**
     * Deletes all rows from the credit_note_type table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public function doDeleteAll(ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(CreditNoteTypeTableMap::DATABASE_NAME);
        }

        // use transaction because $criteria could contain info
        // for more than one table or we could emulating ON DELETE CASCADE, etc.
        return $con->transaction(function () use ($con) {
            $affectedRows = 0; // initialize var to track total num of affected rows
            $affectedRows += parent::doDeleteAll($con);
            // Because this db requires some delete cascade/set null emulation, we have to
            // clear the cached instance *after* the emulation has happened (since
            // instances get re-added by the select statement contained therein).
            CreditNoteTypeTableMap::clearInstancePool();
            CreditNoteTypeTableMap::clearRelatedInstancePool();

            return $affectedRows;
        });
    }

    /**
     * Performs a DELETE on the database based on the current ModelCriteria
     *
     * @param ConnectionInterface $con the connection to use
     * @return int             The number of affected rows (if supported by underlying database driver).  This includes CASCADE-related rows
     *                         if supported by native driver or if emulated using Propel.
     * @throws PropelException Any exceptions caught during processing will be
     *                         rethrown wrapped into a PropelException.
     */
    public function delete(ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(CreditNoteTypeTableMap::DATABASE_NAME);
        }

        $criteria = $this;

        // Set the correct dbName
        $criteria->setDbName(CreditNoteTypeTableMap::DATABASE_NAME);

        // use transaction because $criteria could contain info
        // for more than one table or we could emulating ON DELETE CASCADE, etc.
        return $con->transaction(function () use ($con, $criteria) {
            $affectedRows = 0; // initialize var to track total num of affected rows

            CreditNoteTypeTableMap::removeInstanceFromPool($criteria);

            $affectedRows += ModelCriteria::delete($con);
            CreditNoteTypeTableMap::clearRelatedInstancePool();

            return $affectedRows;
        });
    }

    // i18n behavior

    /**
     * Adds a JOIN clause to the query using the i18n relation
     *
     * @param     string $locale Locale to use for the join condition, e.g. 'fr_FR'
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'. Defaults to left join.
     *
     * @return    ChildCreditNoteTypeQuery The current query, for fluid interface
     */
    public function joinI18n($locale = 'en_US', $relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        $relationName = $relationAlias ? $relationAlias : 'CreditNoteTypeI18n';

        return $this
            ->joinCreditNoteTypeI18n($relationAlias, $joinType)
            ->addJoinCondition($relationName, $relationName . '.Locale = ?', $locale);
    }

    /**
     * Adds a JOIN clause to the query and hydrates the related I18n object.
     * Shortcut for $c->joinI18n($locale)->with()
     *
     * @param     string $locale Locale to use for the join condition, e.g. 'fr_FR'
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'. Defaults to left join.
     *
     * @return    $this|ChildCreditNoteTypeQuery The current query, for fluid interface
     */
    public function joinWithI18n($locale = 'en_US', $joinType = Criteria::LEFT_JOIN)
    {
        $this
            ->joinI18n($locale, null, $joinType)
            ->with('CreditNoteTypeI18n');
        $this->with['CreditNoteTypeI18n']->setIsWithOneToMany(false);

        return $this;
    }

    /**
     * Use the I18n relation query object
     *
     * @see       useQuery()
     *
     * @param     string $locale Locale to use for the join condition, e.g. 'fr_FR'
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'. Defaults to left join.
     *
     * @return    ChildCreditNoteTypeI18nQuery A secondary query class using the current class as primary query
     */
    public function useI18nQuery($locale = 'en_US', $relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinI18n($locale, $relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'CreditNoteTypeI18n', '\CreditNote\Model\CreditNoteTypeI18nQuery');
    }

    // timestampable behavior

    /**
     * Filter by the latest updated
     *
     * @param      int $nbDays Maximum age of the latest update in days
     *
     * @return     $this|ChildCreditNoteTypeQuery The current query, for fluid interface
     */
    public function recentlyUpdated($nbDays = 7)
    {
        return $this->addUsingAlias(CreditNoteTypeTableMap::COL_UPDATED_AT, time() - $nbDays * 24 * 60 * 60, Criteria::GREATER_EQUAL);
    }

    /**
     * Order by update date desc
     *
     * @return     $this|ChildCreditNoteTypeQuery The current query, for fluid interface
     */
    public function lastUpdatedFirst()
    {
        return $this->addDescendingOrderByColumn(CreditNoteTypeTableMap::COL_UPDATED_AT);
    }

    /**
     * Order by update date asc
     *
     * @return     $this|ChildCreditNoteTypeQuery The current query, for fluid interface
     */
    public function firstUpdatedFirst()
    {
        return $this->addAscendingOrderByColumn(CreditNoteTypeTableMap::COL_UPDATED_AT);
    }

    /**
     * Order by create date desc
     *
     * @return     $this|ChildCreditNoteTypeQuery The current query, for fluid interface
     */
    public function lastCreatedFirst()
    {
        return $this->addDescendingOrderByColumn(CreditNoteTypeTableMap::COL_CREATED_AT);
    }

    /**
     * Filter by the latest created
     *
     * @param      int $nbDays Maximum age of in days
     *
     * @return     $this|ChildCreditNoteTypeQuery The current query, for fluid interface
     */
    public function recentlyCreated($nbDays = 7)
    {
        return $this->addUsingAlias(CreditNoteTypeTableMap::COL_CREATED_AT, time() - $nbDays * 24 * 60 * 60, Criteria::GREATER_EQUAL);
    }

    /**
     * Order by create date asc
     *
     * @return     $this|ChildCreditNoteTypeQuery The current query, for fluid interface
     */
    public function firstCreatedFirst()
    {
        return $this->addAscendingOrderByColumn(CreditNoteTypeTableMap::COL_CREATED_AT);
    }

} // CreditNoteTypeQuery
