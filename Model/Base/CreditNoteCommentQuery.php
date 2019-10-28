<?php

namespace CreditNote\Model\Base;

use \Exception;
use \PDO;
use CreditNote\Model\CreditNoteComment as ChildCreditNoteComment;
use CreditNote\Model\CreditNoteCommentQuery as ChildCreditNoteCommentQuery;
use CreditNote\Model\Map\CreditNoteCommentTableMap;
use Propel\Runtime\Propel;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\ActiveQuery\ModelJoin;
use Propel\Runtime\Collection\ObjectCollection;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\Exception\PropelException;
use Thelia\Model\Admin;

/**
 * Base class that represents a query for the 'credit_note_comment' table.
 *
 *
 *
 * @method     ChildCreditNoteCommentQuery orderById($order = Criteria::ASC) Order by the id column
 * @method     ChildCreditNoteCommentQuery orderByCreditNoteId($order = Criteria::ASC) Order by the credit_note_id column
 * @method     ChildCreditNoteCommentQuery orderByAdminId($order = Criteria::ASC) Order by the admin_id column
 * @method     ChildCreditNoteCommentQuery orderByComment($order = Criteria::ASC) Order by the comment column
 * @method     ChildCreditNoteCommentQuery orderByCreatedAt($order = Criteria::ASC) Order by the created_at column
 * @method     ChildCreditNoteCommentQuery orderByUpdatedAt($order = Criteria::ASC) Order by the updated_at column
 *
 * @method     ChildCreditNoteCommentQuery groupById() Group by the id column
 * @method     ChildCreditNoteCommentQuery groupByCreditNoteId() Group by the credit_note_id column
 * @method     ChildCreditNoteCommentQuery groupByAdminId() Group by the admin_id column
 * @method     ChildCreditNoteCommentQuery groupByComment() Group by the comment column
 * @method     ChildCreditNoteCommentQuery groupByCreatedAt() Group by the created_at column
 * @method     ChildCreditNoteCommentQuery groupByUpdatedAt() Group by the updated_at column
 *
 * @method     ChildCreditNoteCommentQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     ChildCreditNoteCommentQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     ChildCreditNoteCommentQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     ChildCreditNoteCommentQuery leftJoinWith($relation) Adds a LEFT JOIN clause and with to the query
 * @method     ChildCreditNoteCommentQuery rightJoinWith($relation) Adds a RIGHT JOIN clause and with to the query
 * @method     ChildCreditNoteCommentQuery innerJoinWith($relation) Adds a INNER JOIN clause and with to the query
 *
 * @method     ChildCreditNoteCommentQuery leftJoinCreditNote($relationAlias = null) Adds a LEFT JOIN clause to the query using the CreditNote relation
 * @method     ChildCreditNoteCommentQuery rightJoinCreditNote($relationAlias = null) Adds a RIGHT JOIN clause to the query using the CreditNote relation
 * @method     ChildCreditNoteCommentQuery innerJoinCreditNote($relationAlias = null) Adds a INNER JOIN clause to the query using the CreditNote relation
 *
 * @method     ChildCreditNoteCommentQuery joinWithCreditNote($joinType = Criteria::INNER_JOIN) Adds a join clause and with to the query using the CreditNote relation
 *
 * @method     ChildCreditNoteCommentQuery leftJoinWithCreditNote() Adds a LEFT JOIN clause and with to the query using the CreditNote relation
 * @method     ChildCreditNoteCommentQuery rightJoinWithCreditNote() Adds a RIGHT JOIN clause and with to the query using the CreditNote relation
 * @method     ChildCreditNoteCommentQuery innerJoinWithCreditNote() Adds a INNER JOIN clause and with to the query using the CreditNote relation
 *
 * @method     ChildCreditNoteCommentQuery leftJoinAdmin($relationAlias = null) Adds a LEFT JOIN clause to the query using the Admin relation
 * @method     ChildCreditNoteCommentQuery rightJoinAdmin($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Admin relation
 * @method     ChildCreditNoteCommentQuery innerJoinAdmin($relationAlias = null) Adds a INNER JOIN clause to the query using the Admin relation
 *
 * @method     ChildCreditNoteCommentQuery joinWithAdmin($joinType = Criteria::INNER_JOIN) Adds a join clause and with to the query using the Admin relation
 *
 * @method     ChildCreditNoteCommentQuery leftJoinWithAdmin() Adds a LEFT JOIN clause and with to the query using the Admin relation
 * @method     ChildCreditNoteCommentQuery rightJoinWithAdmin() Adds a RIGHT JOIN clause and with to the query using the Admin relation
 * @method     ChildCreditNoteCommentQuery innerJoinWithAdmin() Adds a INNER JOIN clause and with to the query using the Admin relation
 *
 * @method     \CreditNote\Model\CreditNoteQuery|\Thelia\Model\AdminQuery endUse() Finalizes a secondary criteria and merges it with its primary Criteria
 *
 * @method     ChildCreditNoteComment findOne(ConnectionInterface $con = null) Return the first ChildCreditNoteComment matching the query
 * @method     ChildCreditNoteComment findOneOrCreate(ConnectionInterface $con = null) Return the first ChildCreditNoteComment matching the query, or a new ChildCreditNoteComment object populated from the query conditions when no match is found
 *
 * @method     ChildCreditNoteComment findOneById(int $id) Return the first ChildCreditNoteComment filtered by the id column
 * @method     ChildCreditNoteComment findOneByCreditNoteId(int $credit_note_id) Return the first ChildCreditNoteComment filtered by the credit_note_id column
 * @method     ChildCreditNoteComment findOneByAdminId(int $admin_id) Return the first ChildCreditNoteComment filtered by the admin_id column
 * @method     ChildCreditNoteComment findOneByComment(string $comment) Return the first ChildCreditNoteComment filtered by the comment column
 * @method     ChildCreditNoteComment findOneByCreatedAt(string $created_at) Return the first ChildCreditNoteComment filtered by the created_at column
 * @method     ChildCreditNoteComment findOneByUpdatedAt(string $updated_at) Return the first ChildCreditNoteComment filtered by the updated_at column *

 * @method     ChildCreditNoteComment requirePk($key, ConnectionInterface $con = null) Return the ChildCreditNoteComment by primary key and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildCreditNoteComment requireOne(ConnectionInterface $con = null) Return the first ChildCreditNoteComment matching the query and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 *
 * @method     ChildCreditNoteComment requireOneById(int $id) Return the first ChildCreditNoteComment filtered by the id column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildCreditNoteComment requireOneByCreditNoteId(int $credit_note_id) Return the first ChildCreditNoteComment filtered by the credit_note_id column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildCreditNoteComment requireOneByAdminId(int $admin_id) Return the first ChildCreditNoteComment filtered by the admin_id column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildCreditNoteComment requireOneByComment(string $comment) Return the first ChildCreditNoteComment filtered by the comment column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildCreditNoteComment requireOneByCreatedAt(string $created_at) Return the first ChildCreditNoteComment filtered by the created_at column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildCreditNoteComment requireOneByUpdatedAt(string $updated_at) Return the first ChildCreditNoteComment filtered by the updated_at column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 *
 * @method     ChildCreditNoteComment[]|ObjectCollection find(ConnectionInterface $con = null) Return ChildCreditNoteComment objects based on current ModelCriteria
 * @method     ChildCreditNoteComment[]|ObjectCollection findById(int $id) Return ChildCreditNoteComment objects filtered by the id column
 * @method     ChildCreditNoteComment[]|ObjectCollection findByCreditNoteId(int $credit_note_id) Return ChildCreditNoteComment objects filtered by the credit_note_id column
 * @method     ChildCreditNoteComment[]|ObjectCollection findByAdminId(int $admin_id) Return ChildCreditNoteComment objects filtered by the admin_id column
 * @method     ChildCreditNoteComment[]|ObjectCollection findByComment(string $comment) Return ChildCreditNoteComment objects filtered by the comment column
 * @method     ChildCreditNoteComment[]|ObjectCollection findByCreatedAt(string $created_at) Return ChildCreditNoteComment objects filtered by the created_at column
 * @method     ChildCreditNoteComment[]|ObjectCollection findByUpdatedAt(string $updated_at) Return ChildCreditNoteComment objects filtered by the updated_at column
 * @method     ChildCreditNoteComment[]|\Propel\Runtime\Util\PropelModelPager paginate($page = 1, $maxPerPage = 10, ConnectionInterface $con = null) Issue a SELECT query based on the current ModelCriteria and uses a page and a maximum number of results per page to compute an offset and a limit
 *
 */
abstract class CreditNoteCommentQuery extends ModelCriteria
{
    protected $entityNotFoundExceptionClass = '\\Propel\\Runtime\\Exception\\EntityNotFoundException';

    /**
     * Initializes internal state of \CreditNote\Model\Base\CreditNoteCommentQuery object.
     *
     * @param     string $dbName The database name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = 'thelia', $modelName = '\\CreditNote\\Model\\CreditNoteComment', $modelAlias = null)
    {
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new ChildCreditNoteCommentQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param     Criteria $criteria Optional Criteria to build the query from
     *
     * @return ChildCreditNoteCommentQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof ChildCreditNoteCommentQuery) {
            return $criteria;
        }
        $query = new ChildCreditNoteCommentQuery();
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
     * @return ChildCreditNoteComment|array|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }

        if ($con === null) {
            $con = Propel::getServiceContainer()->getReadConnection(CreditNoteCommentTableMap::DATABASE_NAME);
        }

        $this->basePreSelect($con);

        if (
            $this->formatter || $this->modelAlias || $this->with || $this->select
            || $this->selectColumns || $this->asColumns || $this->selectModifiers
            || $this->map || $this->having || $this->joins
        ) {
            return $this->findPkComplex($key, $con);
        }

        if ((null !== ($obj = CreditNoteCommentTableMap::getInstanceFromPool(null === $key || is_scalar($key) || is_callable([$key, '__toString']) ? (string) $key : $key)))) {
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
     * @return ChildCreditNoteComment A model object, or null if the key is not found
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT `id`, `credit_note_id`, `admin_id`, `comment`, `created_at`, `updated_at` FROM `credit_note_comment` WHERE `id` = :p0';
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
            /** @var ChildCreditNoteComment $obj */
            $obj = new ChildCreditNoteComment();
            $obj->hydrate($row);
            CreditNoteCommentTableMap::addInstanceToPool($obj, null === $key || is_scalar($key) || is_callable([$key, '__toString']) ? (string) $key : $key);
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
     * @return ChildCreditNoteComment|array|mixed the result, formatted by the current formatter
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
     * @return $this|ChildCreditNoteCommentQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(CreditNoteCommentTableMap::COL_ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return $this|ChildCreditNoteCommentQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(CreditNoteCommentTableMap::COL_ID, $keys, Criteria::IN);
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
     * @return $this|ChildCreditNoteCommentQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (\is_array($id)) {
            $useMinMax = false;
            if (isset($id['min'])) {
                $this->addUsingAlias(CreditNoteCommentTableMap::COL_ID, $id['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($id['max'])) {
                $this->addUsingAlias(CreditNoteCommentTableMap::COL_ID, $id['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CreditNoteCommentTableMap::COL_ID, $id, $comparison);
    }

    /**
     * Filter the query on the credit_note_id column
     *
     * Example usage:
     * <code>
     * $query->filterByCreditNoteId(1234); // WHERE credit_note_id = 1234
     * $query->filterByCreditNoteId(array(12, 34)); // WHERE credit_note_id IN (12, 34)
     * $query->filterByCreditNoteId(array('min' => 12)); // WHERE credit_note_id > 12
     * </code>
     *
     * @see       filterByCreditNote()
     *
     * @param     mixed $creditNoteId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for \in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildCreditNoteCommentQuery The current query, for fluid interface
     */
    public function filterByCreditNoteId($creditNoteId = null, $comparison = null)
    {
        if (\is_array($creditNoteId)) {
            $useMinMax = false;
            if (isset($creditNoteId['min'])) {
                $this->addUsingAlias(CreditNoteCommentTableMap::COL_CREDIT_NOTE_ID, $creditNoteId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($creditNoteId['max'])) {
                $this->addUsingAlias(CreditNoteCommentTableMap::COL_CREDIT_NOTE_ID, $creditNoteId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CreditNoteCommentTableMap::COL_CREDIT_NOTE_ID, $creditNoteId, $comparison);
    }

    /**
     * Filter the query on the admin_id column
     *
     * Example usage:
     * <code>
     * $query->filterByAdminId(1234); // WHERE admin_id = 1234
     * $query->filterByAdminId(array(12, 34)); // WHERE admin_id IN (12, 34)
     * $query->filterByAdminId(array('min' => 12)); // WHERE admin_id > 12
     * </code>
     *
     * @see       filterByAdmin()
     *
     * @param     mixed $adminId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for \in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildCreditNoteCommentQuery The current query, for fluid interface
     */
    public function filterByAdminId($adminId = null, $comparison = null)
    {
        if (\is_array($adminId)) {
            $useMinMax = false;
            if (isset($adminId['min'])) {
                $this->addUsingAlias(CreditNoteCommentTableMap::COL_ADMIN_ID, $adminId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($adminId['max'])) {
                $this->addUsingAlias(CreditNoteCommentTableMap::COL_ADMIN_ID, $adminId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CreditNoteCommentTableMap::COL_ADMIN_ID, $adminId, $comparison);
    }

    /**
     * Filter the query on the comment column
     *
     * Example usage:
     * <code>
     * $query->filterByComment('fooValue');   // WHERE comment = 'fooValue'
     * $query->filterByComment('%fooValue%', Criteria::LIKE); // WHERE comment LIKE '%fooValue%'
     * </code>
     *
     * @param     string $comment The value to use as filter.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildCreditNoteCommentQuery The current query, for fluid interface
     */
    public function filterByComment($comment = null, $comparison = null)
    {
        if (null === $comparison) {
            if (\is_array($comment)) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CreditNoteCommentTableMap::COL_COMMENT, $comment, $comparison);
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
     * @return $this|ChildCreditNoteCommentQuery The current query, for fluid interface
     */
    public function filterByCreatedAt($createdAt = null, $comparison = null)
    {
        if (\is_array($createdAt)) {
            $useMinMax = false;
            if (isset($createdAt['min'])) {
                $this->addUsingAlias(CreditNoteCommentTableMap::COL_CREATED_AT, $createdAt['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($createdAt['max'])) {
                $this->addUsingAlias(CreditNoteCommentTableMap::COL_CREATED_AT, $createdAt['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CreditNoteCommentTableMap::COL_CREATED_AT, $createdAt, $comparison);
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
     * @return $this|ChildCreditNoteCommentQuery The current query, for fluid interface
     */
    public function filterByUpdatedAt($updatedAt = null, $comparison = null)
    {
        if (\is_array($updatedAt)) {
            $useMinMax = false;
            if (isset($updatedAt['min'])) {
                $this->addUsingAlias(CreditNoteCommentTableMap::COL_UPDATED_AT, $updatedAt['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($updatedAt['max'])) {
                $this->addUsingAlias(CreditNoteCommentTableMap::COL_UPDATED_AT, $updatedAt['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CreditNoteCommentTableMap::COL_UPDATED_AT, $updatedAt, $comparison);
    }

    /**
     * Filter the query by a related \CreditNote\Model\CreditNote object
     *
     * @param \CreditNote\Model\CreditNote|ObjectCollection $creditNote The related object(s) to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @throws \Propel\Runtime\Exception\PropelException
     *
     * @return ChildCreditNoteCommentQuery The current query, for fluid interface
     */
    public function filterByCreditNote($creditNote, $comparison = null)
    {
        if ($creditNote instanceof \CreditNote\Model\CreditNote) {
            return $this
                ->addUsingAlias(CreditNoteCommentTableMap::COL_CREDIT_NOTE_ID, $creditNote->getId(), $comparison);
        } elseif ($creditNote instanceof ObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(CreditNoteCommentTableMap::COL_CREDIT_NOTE_ID, $creditNote->toKeyValue('PrimaryKey', 'Id'), $comparison);
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
     * @return $this|ChildCreditNoteCommentQuery The current query, for fluid interface
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
     * Filter the query by a related \Thelia\Model\Admin object
     *
     * @param \Thelia\Model\Admin|ObjectCollection $admin The related object(s) to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @throws \Propel\Runtime\Exception\PropelException
     *
     * @return ChildCreditNoteCommentQuery The current query, for fluid interface
     */
    public function filterByAdmin($admin, $comparison = null)
    {
        if ($admin instanceof \Thelia\Model\Admin) {
            return $this
                ->addUsingAlias(CreditNoteCommentTableMap::COL_ADMIN_ID, $admin->getId(), $comparison);
        } elseif ($admin instanceof ObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(CreditNoteCommentTableMap::COL_ADMIN_ID, $admin->toKeyValue('PrimaryKey', 'Id'), $comparison);
        } else {
            throw new PropelException('filterByAdmin() only accepts arguments of type \Thelia\Model\Admin or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the Admin relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return $this|ChildCreditNoteCommentQuery The current query, for fluid interface
     */
    public function joinAdmin($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('Admin');

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
            $this->addJoinObject($join, 'Admin');
        }

        return $this;
    }

    /**
     * Use the Admin relation Admin object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return \Thelia\Model\AdminQuery A secondary query class using the current class as primary query
     */
    public function useAdminQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinAdmin($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'Admin', '\Thelia\Model\AdminQuery');
    }

    /**
     * Exclude object from result
     *
     * @param   ChildCreditNoteComment $creditNoteComment Object to remove from the list of results
     *
     * @return $this|ChildCreditNoteCommentQuery The current query, for fluid interface
     */
    public function prune($creditNoteComment = null)
    {
        if ($creditNoteComment) {
            $this->addUsingAlias(CreditNoteCommentTableMap::COL_ID, $creditNoteComment->getId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

    /**
     * Deletes all rows from the credit_note_comment table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public function doDeleteAll(ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(CreditNoteCommentTableMap::DATABASE_NAME);
        }

        // use transaction because $criteria could contain info
        // for more than one table or we could emulating ON DELETE CASCADE, etc.
        return $con->transaction(function () use ($con) {
            $affectedRows = 0; // initialize var to track total num of affected rows
            $affectedRows += parent::doDeleteAll($con);
            // Because this db requires some delete cascade/set null emulation, we have to
            // clear the cached instance *after* the emulation has happened (since
            // instances get re-added by the select statement contained therein).
            CreditNoteCommentTableMap::clearInstancePool();
            CreditNoteCommentTableMap::clearRelatedInstancePool();

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
            $con = Propel::getServiceContainer()->getWriteConnection(CreditNoteCommentTableMap::DATABASE_NAME);
        }

        $criteria = $this;

        // Set the correct dbName
        $criteria->setDbName(CreditNoteCommentTableMap::DATABASE_NAME);

        // use transaction because $criteria could contain info
        // for more than one table or we could emulating ON DELETE CASCADE, etc.
        return $con->transaction(function () use ($con, $criteria) {
            $affectedRows = 0; // initialize var to track total num of affected rows

            CreditNoteCommentTableMap::removeInstanceFromPool($criteria);

            $affectedRows += ModelCriteria::delete($con);
            CreditNoteCommentTableMap::clearRelatedInstancePool();

            return $affectedRows;
        });
    }

    // timestampable behavior

    /**
     * Filter by the latest updated
     *
     * @param      int $nbDays Maximum age of the latest update in days
     *
     * @return     $this|ChildCreditNoteCommentQuery The current query, for fluid interface
     */
    public function recentlyUpdated($nbDays = 7)
    {
        return $this->addUsingAlias(CreditNoteCommentTableMap::COL_UPDATED_AT, time() - $nbDays * 24 * 60 * 60, Criteria::GREATER_EQUAL);
    }

    /**
     * Order by update date desc
     *
     * @return     $this|ChildCreditNoteCommentQuery The current query, for fluid interface
     */
    public function lastUpdatedFirst()
    {
        return $this->addDescendingOrderByColumn(CreditNoteCommentTableMap::COL_UPDATED_AT);
    }

    /**
     * Order by update date asc
     *
     * @return     $this|ChildCreditNoteCommentQuery The current query, for fluid interface
     */
    public function firstUpdatedFirst()
    {
        return $this->addAscendingOrderByColumn(CreditNoteCommentTableMap::COL_UPDATED_AT);
    }

    /**
     * Order by create date desc
     *
     * @return     $this|ChildCreditNoteCommentQuery The current query, for fluid interface
     */
    public function lastCreatedFirst()
    {
        return $this->addDescendingOrderByColumn(CreditNoteCommentTableMap::COL_CREATED_AT);
    }

    /**
     * Filter by the latest created
     *
     * @param      int $nbDays Maximum age of in days
     *
     * @return     $this|ChildCreditNoteCommentQuery The current query, for fluid interface
     */
    public function recentlyCreated($nbDays = 7)
    {
        return $this->addUsingAlias(CreditNoteCommentTableMap::COL_CREATED_AT, time() - $nbDays * 24 * 60 * 60, Criteria::GREATER_EQUAL);
    }

    /**
     * Order by create date asc
     *
     * @return     $this|ChildCreditNoteCommentQuery The current query, for fluid interface
     */
    public function firstCreatedFirst()
    {
        return $this->addAscendingOrderByColumn(CreditNoteCommentTableMap::COL_CREATED_AT);
    }

} // CreditNoteCommentQuery
