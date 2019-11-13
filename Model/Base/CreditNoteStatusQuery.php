<?php

namespace CreditNote\Model\Base;

use \Exception;
use \PDO;
use CreditNote\Model\CreditNoteStatus as ChildCreditNoteStatus;
use CreditNote\Model\CreditNoteStatusI18nQuery as ChildCreditNoteStatusI18nQuery;
use CreditNote\Model\CreditNoteStatusQuery as ChildCreditNoteStatusQuery;
use CreditNote\Model\Map\CreditNoteStatusTableMap;
use Propel\Runtime\Propel;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\ActiveQuery\ModelJoin;
use Propel\Runtime\Collection\Collection;
use Propel\Runtime\Collection\ObjectCollection;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\Exception\PropelException;

/**
 * Base class that represents a query for the 'credit_note_status' table.
 *
 *
 *
 * @method     ChildCreditNoteStatusQuery orderById($order = Criteria::ASC) Order by the id column
 * @method     ChildCreditNoteStatusQuery orderByCode($order = Criteria::ASC) Order by the code column
 * @method     ChildCreditNoteStatusQuery orderByColor($order = Criteria::ASC) Order by the color column
 * @method     ChildCreditNoteStatusQuery orderByInvoiced($order = Criteria::ASC) Order by the invoiced column
 * @method     ChildCreditNoteStatusQuery orderByUsed($order = Criteria::ASC) Order by the used column
 * @method     ChildCreditNoteStatusQuery orderByPosition($order = Criteria::ASC) Order by the position column
 * @method     ChildCreditNoteStatusQuery orderByCreatedAt($order = Criteria::ASC) Order by the created_at column
 * @method     ChildCreditNoteStatusQuery orderByUpdatedAt($order = Criteria::ASC) Order by the updated_at column
 *
 * @method     ChildCreditNoteStatusQuery groupById() Group by the id column
 * @method     ChildCreditNoteStatusQuery groupByCode() Group by the code column
 * @method     ChildCreditNoteStatusQuery groupByColor() Group by the color column
 * @method     ChildCreditNoteStatusQuery groupByInvoiced() Group by the invoiced column
 * @method     ChildCreditNoteStatusQuery groupByUsed() Group by the used column
 * @method     ChildCreditNoteStatusQuery groupByPosition() Group by the position column
 * @method     ChildCreditNoteStatusQuery groupByCreatedAt() Group by the created_at column
 * @method     ChildCreditNoteStatusQuery groupByUpdatedAt() Group by the updated_at column
 *
 * @method     ChildCreditNoteStatusQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     ChildCreditNoteStatusQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     ChildCreditNoteStatusQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     ChildCreditNoteStatusQuery leftJoinCreditNote($relationAlias = null) Adds a LEFT JOIN clause to the query using the CreditNote relation
 * @method     ChildCreditNoteStatusQuery rightJoinCreditNote($relationAlias = null) Adds a RIGHT JOIN clause to the query using the CreditNote relation
 * @method     ChildCreditNoteStatusQuery innerJoinCreditNote($relationAlias = null) Adds a INNER JOIN clause to the query using the CreditNote relation
 *
 * @method     ChildCreditNoteStatusQuery leftJoinCreditNoteStatusFlowRelatedByFromStatusId($relationAlias = null) Adds a LEFT JOIN clause to the query using the CreditNoteStatusFlowRelatedByFromStatusId relation
 * @method     ChildCreditNoteStatusQuery rightJoinCreditNoteStatusFlowRelatedByFromStatusId($relationAlias = null) Adds a RIGHT JOIN clause to the query using the CreditNoteStatusFlowRelatedByFromStatusId relation
 * @method     ChildCreditNoteStatusQuery innerJoinCreditNoteStatusFlowRelatedByFromStatusId($relationAlias = null) Adds a INNER JOIN clause to the query using the CreditNoteStatusFlowRelatedByFromStatusId relation
 *
 * @method     ChildCreditNoteStatusQuery leftJoinCreditNoteStatusFlowRelatedByToStatusId($relationAlias = null) Adds a LEFT JOIN clause to the query using the CreditNoteStatusFlowRelatedByToStatusId relation
 * @method     ChildCreditNoteStatusQuery rightJoinCreditNoteStatusFlowRelatedByToStatusId($relationAlias = null) Adds a RIGHT JOIN clause to the query using the CreditNoteStatusFlowRelatedByToStatusId relation
 * @method     ChildCreditNoteStatusQuery innerJoinCreditNoteStatusFlowRelatedByToStatusId($relationAlias = null) Adds a INNER JOIN clause to the query using the CreditNoteStatusFlowRelatedByToStatusId relation
 *
 * @method     ChildCreditNoteStatusQuery leftJoinCreditNoteStatusI18n($relationAlias = null) Adds a LEFT JOIN clause to the query using the CreditNoteStatusI18n relation
 * @method     ChildCreditNoteStatusQuery rightJoinCreditNoteStatusI18n($relationAlias = null) Adds a RIGHT JOIN clause to the query using the CreditNoteStatusI18n relation
 * @method     ChildCreditNoteStatusQuery innerJoinCreditNoteStatusI18n($relationAlias = null) Adds a INNER JOIN clause to the query using the CreditNoteStatusI18n relation
 *
 * @method     ChildCreditNoteStatus findOne(ConnectionInterface $con = null) Return the first ChildCreditNoteStatus matching the query
 * @method     ChildCreditNoteStatus findOneOrCreate(ConnectionInterface $con = null) Return the first ChildCreditNoteStatus matching the query, or a new ChildCreditNoteStatus object populated from the query conditions when no match is found
 *
 * @method     ChildCreditNoteStatus findOneById(int $id) Return the first ChildCreditNoteStatus filtered by the id column
 * @method     ChildCreditNoteStatus findOneByCode(string $code) Return the first ChildCreditNoteStatus filtered by the code column
 * @method     ChildCreditNoteStatus findOneByColor(string $color) Return the first ChildCreditNoteStatus filtered by the color column
 * @method     ChildCreditNoteStatus findOneByInvoiced(boolean $invoiced) Return the first ChildCreditNoteStatus filtered by the invoiced column
 * @method     ChildCreditNoteStatus findOneByUsed(boolean $used) Return the first ChildCreditNoteStatus filtered by the used column
 * @method     ChildCreditNoteStatus findOneByPosition(int $position) Return the first ChildCreditNoteStatus filtered by the position column
 * @method     ChildCreditNoteStatus findOneByCreatedAt(string $created_at) Return the first ChildCreditNoteStatus filtered by the created_at column
 * @method     ChildCreditNoteStatus findOneByUpdatedAt(string $updated_at) Return the first ChildCreditNoteStatus filtered by the updated_at column
 *
 * @method     array findById(int $id) Return ChildCreditNoteStatus objects filtered by the id column
 * @method     array findByCode(string $code) Return ChildCreditNoteStatus objects filtered by the code column
 * @method     array findByColor(string $color) Return ChildCreditNoteStatus objects filtered by the color column
 * @method     array findByInvoiced(boolean $invoiced) Return ChildCreditNoteStatus objects filtered by the invoiced column
 * @method     array findByUsed(boolean $used) Return ChildCreditNoteStatus objects filtered by the used column
 * @method     array findByPosition(int $position) Return ChildCreditNoteStatus objects filtered by the position column
 * @method     array findByCreatedAt(string $created_at) Return ChildCreditNoteStatus objects filtered by the created_at column
 * @method     array findByUpdatedAt(string $updated_at) Return ChildCreditNoteStatus objects filtered by the updated_at column
 *
 */
abstract class CreditNoteStatusQuery extends ModelCriteria
{

    /**
     * Initializes internal state of \CreditNote\Model\Base\CreditNoteStatusQuery object.
     *
     * @param     string $dbName The database name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = 'thelia', $modelName = '\\CreditNote\\Model\\CreditNoteStatus', $modelAlias = null)
    {
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new ChildCreditNoteStatusQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param     Criteria $criteria Optional Criteria to build the query from
     *
     * @return ChildCreditNoteStatusQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof \CreditNote\Model\CreditNoteStatusQuery) {
            return $criteria;
        }
        $query = new \CreditNote\Model\CreditNoteStatusQuery();
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
     * @return ChildCreditNoteStatus|array|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = CreditNoteStatusTableMap::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getServiceContainer()->getReadConnection(CreditNoteStatusTableMap::DATABASE_NAME);
        }
        $this->basePreSelect($con);
        if ($this->formatter || $this->modelAlias || $this->with || $this->select
         || $this->selectColumns || $this->asColumns || $this->selectModifiers
         || $this->map || $this->having || $this->joins) {
            return $this->findPkComplex($key, $con);
        } else {
            return $this->findPkSimple($key, $con);
        }
    }

    /**
     * Find object by primary key using raw SQL to go fast.
     * Bypass doSelect() and the object formatter by using generated code.
     *
     * @param     mixed $key Primary key to use for the query
     * @param     ConnectionInterface $con A connection object
     *
     * @return   ChildCreditNoteStatus A model object, or null if the key is not found
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT ID, CODE, COLOR, INVOICED, USED, POSITION, CREATED_AT, UPDATED_AT FROM credit_note_status WHERE ID = :p0';
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
            $obj = new ChildCreditNoteStatus();
            $obj->hydrate($row);
            CreditNoteStatusTableMap::addInstanceToPool($obj, (string) $key);
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
     * @return ChildCreditNoteStatus|array|mixed the result, formatted by the current formatter
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
     * @return ChildCreditNoteStatusQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(CreditNoteStatusTableMap::ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return ChildCreditNoteStatusQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(CreditNoteStatusTableMap::ID, $keys, Criteria::IN);
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
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildCreditNoteStatusQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id)) {
            $useMinMax = false;
            if (isset($id['min'])) {
                $this->addUsingAlias(CreditNoteStatusTableMap::ID, $id['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($id['max'])) {
                $this->addUsingAlias(CreditNoteStatusTableMap::ID, $id['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CreditNoteStatusTableMap::ID, $id, $comparison);
    }

    /**
     * Filter the query on the code column
     *
     * Example usage:
     * <code>
     * $query->filterByCode('fooValue');   // WHERE code = 'fooValue'
     * $query->filterByCode('%fooValue%'); // WHERE code LIKE '%fooValue%'
     * </code>
     *
     * @param     string $code The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildCreditNoteStatusQuery The current query, for fluid interface
     */
    public function filterByCode($code = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($code)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $code)) {
                $code = str_replace('*', '%', $code);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(CreditNoteStatusTableMap::CODE, $code, $comparison);
    }

    /**
     * Filter the query on the color column
     *
     * Example usage:
     * <code>
     * $query->filterByColor('fooValue');   // WHERE color = 'fooValue'
     * $query->filterByColor('%fooValue%'); // WHERE color LIKE '%fooValue%'
     * </code>
     *
     * @param     string $color The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildCreditNoteStatusQuery The current query, for fluid interface
     */
    public function filterByColor($color = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($color)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $color)) {
                $color = str_replace('*', '%', $color);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(CreditNoteStatusTableMap::COLOR, $color, $comparison);
    }

    /**
     * Filter the query on the invoiced column
     *
     * Example usage:
     * <code>
     * $query->filterByInvoiced(true); // WHERE invoiced = true
     * $query->filterByInvoiced('yes'); // WHERE invoiced = true
     * </code>
     *
     * @param     boolean|string $invoiced The value to use as filter.
     *              Non-boolean arguments are converted using the following rules:
     *                * 1, '1', 'true',  'on',  and 'yes' are converted to boolean true
     *                * 0, '0', 'false', 'off', and 'no'  are converted to boolean false
     *              Check on string values is case insensitive (so 'FaLsE' is seen as 'false').
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildCreditNoteStatusQuery The current query, for fluid interface
     */
    public function filterByInvoiced($invoiced = null, $comparison = null)
    {
        if (is_string($invoiced)) {
            $invoiced = in_array(strtolower($invoiced), array('false', 'off', '-', 'no', 'n', '0', '')) ? false : true;
        }

        return $this->addUsingAlias(CreditNoteStatusTableMap::INVOICED, $invoiced, $comparison);
    }

    /**
     * Filter the query on the used column
     *
     * Example usage:
     * <code>
     * $query->filterByUsed(true); // WHERE used = true
     * $query->filterByUsed('yes'); // WHERE used = true
     * </code>
     *
     * @param     boolean|string $used The value to use as filter.
     *              Non-boolean arguments are converted using the following rules:
     *                * 1, '1', 'true',  'on',  and 'yes' are converted to boolean true
     *                * 0, '0', 'false', 'off', and 'no'  are converted to boolean false
     *              Check on string values is case insensitive (so 'FaLsE' is seen as 'false').
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildCreditNoteStatusQuery The current query, for fluid interface
     */
    public function filterByUsed($used = null, $comparison = null)
    {
        if (is_string($used)) {
            $used = in_array(strtolower($used), array('false', 'off', '-', 'no', 'n', '0', '')) ? false : true;
        }

        return $this->addUsingAlias(CreditNoteStatusTableMap::USED, $used, $comparison);
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
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildCreditNoteStatusQuery The current query, for fluid interface
     */
    public function filterByPosition($position = null, $comparison = null)
    {
        if (is_array($position)) {
            $useMinMax = false;
            if (isset($position['min'])) {
                $this->addUsingAlias(CreditNoteStatusTableMap::POSITION, $position['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($position['max'])) {
                $this->addUsingAlias(CreditNoteStatusTableMap::POSITION, $position['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CreditNoteStatusTableMap::POSITION, $position, $comparison);
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
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildCreditNoteStatusQuery The current query, for fluid interface
     */
    public function filterByCreatedAt($createdAt = null, $comparison = null)
    {
        if (is_array($createdAt)) {
            $useMinMax = false;
            if (isset($createdAt['min'])) {
                $this->addUsingAlias(CreditNoteStatusTableMap::CREATED_AT, $createdAt['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($createdAt['max'])) {
                $this->addUsingAlias(CreditNoteStatusTableMap::CREATED_AT, $createdAt['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CreditNoteStatusTableMap::CREATED_AT, $createdAt, $comparison);
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
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildCreditNoteStatusQuery The current query, for fluid interface
     */
    public function filterByUpdatedAt($updatedAt = null, $comparison = null)
    {
        if (is_array($updatedAt)) {
            $useMinMax = false;
            if (isset($updatedAt['min'])) {
                $this->addUsingAlias(CreditNoteStatusTableMap::UPDATED_AT, $updatedAt['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($updatedAt['max'])) {
                $this->addUsingAlias(CreditNoteStatusTableMap::UPDATED_AT, $updatedAt['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CreditNoteStatusTableMap::UPDATED_AT, $updatedAt, $comparison);
    }

    /**
     * Filter the query by a related \CreditNote\Model\CreditNote object
     *
     * @param \CreditNote\Model\CreditNote|ObjectCollection $creditNote  the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildCreditNoteStatusQuery The current query, for fluid interface
     */
    public function filterByCreditNote($creditNote, $comparison = null)
    {
        if ($creditNote instanceof \CreditNote\Model\CreditNote) {
            return $this
                ->addUsingAlias(CreditNoteStatusTableMap::ID, $creditNote->getStatusId(), $comparison);
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
     * @return ChildCreditNoteStatusQuery The current query, for fluid interface
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
     * @return   \CreditNote\Model\CreditNoteQuery A secondary query class using the current class as primary query
     */
    public function useCreditNoteQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinCreditNote($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'CreditNote', '\CreditNote\Model\CreditNoteQuery');
    }

    /**
     * Filter the query by a related \CreditNote\Model\CreditNoteStatusFlow object
     *
     * @param \CreditNote\Model\CreditNoteStatusFlow|ObjectCollection $creditNoteStatusFlow  the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildCreditNoteStatusQuery The current query, for fluid interface
     */
    public function filterByCreditNoteStatusFlowRelatedByFromStatusId($creditNoteStatusFlow, $comparison = null)
    {
        if ($creditNoteStatusFlow instanceof \CreditNote\Model\CreditNoteStatusFlow) {
            return $this
                ->addUsingAlias(CreditNoteStatusTableMap::ID, $creditNoteStatusFlow->getFromStatusId(), $comparison);
        } elseif ($creditNoteStatusFlow instanceof ObjectCollection) {
            return $this
                ->useCreditNoteStatusFlowRelatedByFromStatusIdQuery()
                ->filterByPrimaryKeys($creditNoteStatusFlow->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByCreditNoteStatusFlowRelatedByFromStatusId() only accepts arguments of type \CreditNote\Model\CreditNoteStatusFlow or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the CreditNoteStatusFlowRelatedByFromStatusId relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ChildCreditNoteStatusQuery The current query, for fluid interface
     */
    public function joinCreditNoteStatusFlowRelatedByFromStatusId($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('CreditNoteStatusFlowRelatedByFromStatusId');

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
            $this->addJoinObject($join, 'CreditNoteStatusFlowRelatedByFromStatusId');
        }

        return $this;
    }

    /**
     * Use the CreditNoteStatusFlowRelatedByFromStatusId relation CreditNoteStatusFlow object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \CreditNote\Model\CreditNoteStatusFlowQuery A secondary query class using the current class as primary query
     */
    public function useCreditNoteStatusFlowRelatedByFromStatusIdQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinCreditNoteStatusFlowRelatedByFromStatusId($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'CreditNoteStatusFlowRelatedByFromStatusId', '\CreditNote\Model\CreditNoteStatusFlowQuery');
    }

    /**
     * Filter the query by a related \CreditNote\Model\CreditNoteStatusFlow object
     *
     * @param \CreditNote\Model\CreditNoteStatusFlow|ObjectCollection $creditNoteStatusFlow  the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildCreditNoteStatusQuery The current query, for fluid interface
     */
    public function filterByCreditNoteStatusFlowRelatedByToStatusId($creditNoteStatusFlow, $comparison = null)
    {
        if ($creditNoteStatusFlow instanceof \CreditNote\Model\CreditNoteStatusFlow) {
            return $this
                ->addUsingAlias(CreditNoteStatusTableMap::ID, $creditNoteStatusFlow->getToStatusId(), $comparison);
        } elseif ($creditNoteStatusFlow instanceof ObjectCollection) {
            return $this
                ->useCreditNoteStatusFlowRelatedByToStatusIdQuery()
                ->filterByPrimaryKeys($creditNoteStatusFlow->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByCreditNoteStatusFlowRelatedByToStatusId() only accepts arguments of type \CreditNote\Model\CreditNoteStatusFlow or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the CreditNoteStatusFlowRelatedByToStatusId relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ChildCreditNoteStatusQuery The current query, for fluid interface
     */
    public function joinCreditNoteStatusFlowRelatedByToStatusId($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('CreditNoteStatusFlowRelatedByToStatusId');

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
            $this->addJoinObject($join, 'CreditNoteStatusFlowRelatedByToStatusId');
        }

        return $this;
    }

    /**
     * Use the CreditNoteStatusFlowRelatedByToStatusId relation CreditNoteStatusFlow object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \CreditNote\Model\CreditNoteStatusFlowQuery A secondary query class using the current class as primary query
     */
    public function useCreditNoteStatusFlowRelatedByToStatusIdQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinCreditNoteStatusFlowRelatedByToStatusId($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'CreditNoteStatusFlowRelatedByToStatusId', '\CreditNote\Model\CreditNoteStatusFlowQuery');
    }

    /**
     * Filter the query by a related \CreditNote\Model\CreditNoteStatusI18n object
     *
     * @param \CreditNote\Model\CreditNoteStatusI18n|ObjectCollection $creditNoteStatusI18n  the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildCreditNoteStatusQuery The current query, for fluid interface
     */
    public function filterByCreditNoteStatusI18n($creditNoteStatusI18n, $comparison = null)
    {
        if ($creditNoteStatusI18n instanceof \CreditNote\Model\CreditNoteStatusI18n) {
            return $this
                ->addUsingAlias(CreditNoteStatusTableMap::ID, $creditNoteStatusI18n->getId(), $comparison);
        } elseif ($creditNoteStatusI18n instanceof ObjectCollection) {
            return $this
                ->useCreditNoteStatusI18nQuery()
                ->filterByPrimaryKeys($creditNoteStatusI18n->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByCreditNoteStatusI18n() only accepts arguments of type \CreditNote\Model\CreditNoteStatusI18n or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the CreditNoteStatusI18n relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ChildCreditNoteStatusQuery The current query, for fluid interface
     */
    public function joinCreditNoteStatusI18n($relationAlias = null, $joinType = 'LEFT JOIN')
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('CreditNoteStatusI18n');

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
            $this->addJoinObject($join, 'CreditNoteStatusI18n');
        }

        return $this;
    }

    /**
     * Use the CreditNoteStatusI18n relation CreditNoteStatusI18n object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \CreditNote\Model\CreditNoteStatusI18nQuery A secondary query class using the current class as primary query
     */
    public function useCreditNoteStatusI18nQuery($relationAlias = null, $joinType = 'LEFT JOIN')
    {
        return $this
            ->joinCreditNoteStatusI18n($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'CreditNoteStatusI18n', '\CreditNote\Model\CreditNoteStatusI18nQuery');
    }

    /**
     * Exclude object from result
     *
     * @param   ChildCreditNoteStatus $creditNoteStatus Object to remove from the list of results
     *
     * @return ChildCreditNoteStatusQuery The current query, for fluid interface
     */
    public function prune($creditNoteStatus = null)
    {
        if ($creditNoteStatus) {
            $this->addUsingAlias(CreditNoteStatusTableMap::ID, $creditNoteStatus->getId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

    /**
     * Deletes all rows from the credit_note_status table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public function doDeleteAll(ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(CreditNoteStatusTableMap::DATABASE_NAME);
        }
        $affectedRows = 0; // initialize var to track total num of affected rows
        try {
            // use transaction because $criteria could contain info
            // for more than one table or we could emulating ON DELETE CASCADE, etc.
            $con->beginTransaction();
            $affectedRows += parent::doDeleteAll($con);
            // Because this db requires some delete cascade/set null emulation, we have to
            // clear the cached instance *after* the emulation has happened (since
            // instances get re-added by the select statement contained therein).
            CreditNoteStatusTableMap::clearInstancePool();
            CreditNoteStatusTableMap::clearRelatedInstancePool();

            $con->commit();
        } catch (PropelException $e) {
            $con->rollBack();
            throw $e;
        }

        return $affectedRows;
    }

    /**
     * Performs a DELETE on the database, given a ChildCreditNoteStatus or Criteria object OR a primary key value.
     *
     * @param mixed               $values Criteria or ChildCreditNoteStatus object or primary key or array of primary keys
     *              which is used to create the DELETE statement
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).  This includes CASCADE-related rows
     *                if supported by native driver or if emulated using Propel.
     * @throws PropelException Any exceptions caught during processing will be
     *         rethrown wrapped into a PropelException.
     */
     public function delete(ConnectionInterface $con = null)
     {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(CreditNoteStatusTableMap::DATABASE_NAME);
        }

        $criteria = $this;

        // Set the correct dbName
        $criteria->setDbName(CreditNoteStatusTableMap::DATABASE_NAME);

        $affectedRows = 0; // initialize var to track total num of affected rows

        try {
            // use transaction because $criteria could contain info
            // for more than one table or we could emulating ON DELETE CASCADE, etc.
            $con->beginTransaction();


        CreditNoteStatusTableMap::removeInstanceFromPool($criteria);

            $affectedRows += ModelCriteria::delete($con);
            CreditNoteStatusTableMap::clearRelatedInstancePool();
            $con->commit();

            return $affectedRows;
        } catch (PropelException $e) {
            $con->rollBack();
            throw $e;
        }
    }

    // i18n behavior

    /**
     * Adds a JOIN clause to the query using the i18n relation
     *
     * @param     string $locale Locale to use for the join condition, e.g. 'fr_FR'
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'. Defaults to left join.
     *
     * @return    ChildCreditNoteStatusQuery The current query, for fluid interface
     */
    public function joinI18n($locale = 'en_US', $relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        $relationName = $relationAlias ? $relationAlias : 'CreditNoteStatusI18n';

        return $this
            ->joinCreditNoteStatusI18n($relationAlias, $joinType)
            ->addJoinCondition($relationName, $relationName . '.Locale = ?', $locale);
    }

    /**
     * Adds a JOIN clause to the query and hydrates the related I18n object.
     * Shortcut for $c->joinI18n($locale)->with()
     *
     * @param     string $locale Locale to use for the join condition, e.g. 'fr_FR'
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'. Defaults to left join.
     *
     * @return    ChildCreditNoteStatusQuery The current query, for fluid interface
     */
    public function joinWithI18n($locale = 'en_US', $joinType = Criteria::LEFT_JOIN)
    {
        $this
            ->joinI18n($locale, null, $joinType)
            ->with('CreditNoteStatusI18n');
        $this->with['CreditNoteStatusI18n']->setIsWithOneToMany(false);

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
     * @return    ChildCreditNoteStatusI18nQuery A secondary query class using the current class as primary query
     */
    public function useI18nQuery($locale = 'en_US', $relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinI18n($locale, $relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'CreditNoteStatusI18n', '\CreditNote\Model\CreditNoteStatusI18nQuery');
    }

    // timestampable behavior

    /**
     * Filter by the latest updated
     *
     * @param      int $nbDays Maximum age of the latest update in days
     *
     * @return     ChildCreditNoteStatusQuery The current query, for fluid interface
     */
    public function recentlyUpdated($nbDays = 7)
    {
        return $this->addUsingAlias(CreditNoteStatusTableMap::UPDATED_AT, time() - $nbDays * 24 * 60 * 60, Criteria::GREATER_EQUAL);
    }

    /**
     * Filter by the latest created
     *
     * @param      int $nbDays Maximum age of in days
     *
     * @return     ChildCreditNoteStatusQuery The current query, for fluid interface
     */
    public function recentlyCreated($nbDays = 7)
    {
        return $this->addUsingAlias(CreditNoteStatusTableMap::CREATED_AT, time() - $nbDays * 24 * 60 * 60, Criteria::GREATER_EQUAL);
    }

    /**
     * Order by update date desc
     *
     * @return     ChildCreditNoteStatusQuery The current query, for fluid interface
     */
    public function lastUpdatedFirst()
    {
        return $this->addDescendingOrderByColumn(CreditNoteStatusTableMap::UPDATED_AT);
    }

    /**
     * Order by update date asc
     *
     * @return     ChildCreditNoteStatusQuery The current query, for fluid interface
     */
    public function firstUpdatedFirst()
    {
        return $this->addAscendingOrderByColumn(CreditNoteStatusTableMap::UPDATED_AT);
    }

    /**
     * Order by create date desc
     *
     * @return     ChildCreditNoteStatusQuery The current query, for fluid interface
     */
    public function lastCreatedFirst()
    {
        return $this->addDescendingOrderByColumn(CreditNoteStatusTableMap::CREATED_AT);
    }

    /**
     * Order by create date asc
     *
     * @return     ChildCreditNoteStatusQuery The current query, for fluid interface
     */
    public function firstCreatedFirst()
    {
        return $this->addAscendingOrderByColumn(CreditNoteStatusTableMap::CREATED_AT);
    }

} // CreditNoteStatusQuery
