<?php

namespace CreditNote\Model\Base;

use \Exception;
use \PDO;
use CreditNote\Model\CartCreditNote as ChildCartCreditNote;
use CreditNote\Model\CartCreditNoteQuery as ChildCartCreditNoteQuery;
use CreditNote\Model\Map\CartCreditNoteTableMap;
use Propel\Runtime\Propel;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\ActiveQuery\ModelJoin;
use Propel\Runtime\Collection\ObjectCollection;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\Exception\PropelException;
use Thelia\Model\Cart;

/**
 * Base class that represents a query for the 'cart_credit_note' table.
 *
 *
 *
 * @method     ChildCartCreditNoteQuery orderByCartId($order = Criteria::ASC) Order by the cart_id column
 * @method     ChildCartCreditNoteQuery orderByCreditNoteId($order = Criteria::ASC) Order by the credit_note_id column
 * @method     ChildCartCreditNoteQuery orderByAmountPrice($order = Criteria::ASC) Order by the amount_price column
 * @method     ChildCartCreditNoteQuery orderByCreatedAt($order = Criteria::ASC) Order by the created_at column
 * @method     ChildCartCreditNoteQuery orderByUpdatedAt($order = Criteria::ASC) Order by the updated_at column
 *
 * @method     ChildCartCreditNoteQuery groupByCartId() Group by the cart_id column
 * @method     ChildCartCreditNoteQuery groupByCreditNoteId() Group by the credit_note_id column
 * @method     ChildCartCreditNoteQuery groupByAmountPrice() Group by the amount_price column
 * @method     ChildCartCreditNoteQuery groupByCreatedAt() Group by the created_at column
 * @method     ChildCartCreditNoteQuery groupByUpdatedAt() Group by the updated_at column
 *
 * @method     ChildCartCreditNoteQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     ChildCartCreditNoteQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     ChildCartCreditNoteQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     ChildCartCreditNoteQuery leftJoinWith($relation) Adds a LEFT JOIN clause and with to the query
 * @method     ChildCartCreditNoteQuery rightJoinWith($relation) Adds a RIGHT JOIN clause and with to the query
 * @method     ChildCartCreditNoteQuery innerJoinWith($relation) Adds a INNER JOIN clause and with to the query
 *
 * @method     ChildCartCreditNoteQuery leftJoinCart($relationAlias = null) Adds a LEFT JOIN clause to the query using the Cart relation
 * @method     ChildCartCreditNoteQuery rightJoinCart($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Cart relation
 * @method     ChildCartCreditNoteQuery innerJoinCart($relationAlias = null) Adds a INNER JOIN clause to the query using the Cart relation
 *
 * @method     ChildCartCreditNoteQuery joinWithCart($joinType = Criteria::INNER_JOIN) Adds a join clause and with to the query using the Cart relation
 *
 * @method     ChildCartCreditNoteQuery leftJoinWithCart() Adds a LEFT JOIN clause and with to the query using the Cart relation
 * @method     ChildCartCreditNoteQuery rightJoinWithCart() Adds a RIGHT JOIN clause and with to the query using the Cart relation
 * @method     ChildCartCreditNoteQuery innerJoinWithCart() Adds a INNER JOIN clause and with to the query using the Cart relation
 *
 * @method     ChildCartCreditNoteQuery leftJoinCreditNote($relationAlias = null) Adds a LEFT JOIN clause to the query using the CreditNote relation
 * @method     ChildCartCreditNoteQuery rightJoinCreditNote($relationAlias = null) Adds a RIGHT JOIN clause to the query using the CreditNote relation
 * @method     ChildCartCreditNoteQuery innerJoinCreditNote($relationAlias = null) Adds a INNER JOIN clause to the query using the CreditNote relation
 *
 * @method     ChildCartCreditNoteQuery joinWithCreditNote($joinType = Criteria::INNER_JOIN) Adds a join clause and with to the query using the CreditNote relation
 *
 * @method     ChildCartCreditNoteQuery leftJoinWithCreditNote() Adds a LEFT JOIN clause and with to the query using the CreditNote relation
 * @method     ChildCartCreditNoteQuery rightJoinWithCreditNote() Adds a RIGHT JOIN clause and with to the query using the CreditNote relation
 * @method     ChildCartCreditNoteQuery innerJoinWithCreditNote() Adds a INNER JOIN clause and with to the query using the CreditNote relation
 *
 * @method     \Thelia\Model\CartQuery|\CreditNote\Model\CreditNoteQuery endUse() Finalizes a secondary criteria and merges it with its primary Criteria
 *
 * @method     ChildCartCreditNote findOne(ConnectionInterface $con = null) Return the first ChildCartCreditNote matching the query
 * @method     ChildCartCreditNote findOneOrCreate(ConnectionInterface $con = null) Return the first ChildCartCreditNote matching the query, or a new ChildCartCreditNote object populated from the query conditions when no match is found
 *
 * @method     ChildCartCreditNote findOneByCartId(int $cart_id) Return the first ChildCartCreditNote filtered by the cart_id column
 * @method     ChildCartCreditNote findOneByCreditNoteId(int $credit_note_id) Return the first ChildCartCreditNote filtered by the credit_note_id column
 * @method     ChildCartCreditNote findOneByAmountPrice(string $amount_price) Return the first ChildCartCreditNote filtered by the amount_price column
 * @method     ChildCartCreditNote findOneByCreatedAt(string $created_at) Return the first ChildCartCreditNote filtered by the created_at column
 * @method     ChildCartCreditNote findOneByUpdatedAt(string $updated_at) Return the first ChildCartCreditNote filtered by the updated_at column *

 * @method     ChildCartCreditNote requirePk($key, ConnectionInterface $con = null) Return the ChildCartCreditNote by primary key and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildCartCreditNote requireOne(ConnectionInterface $con = null) Return the first ChildCartCreditNote matching the query and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 *
 * @method     ChildCartCreditNote requireOneByCartId(int $cart_id) Return the first ChildCartCreditNote filtered by the cart_id column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildCartCreditNote requireOneByCreditNoteId(int $credit_note_id) Return the first ChildCartCreditNote filtered by the credit_note_id column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildCartCreditNote requireOneByAmountPrice(string $amount_price) Return the first ChildCartCreditNote filtered by the amount_price column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildCartCreditNote requireOneByCreatedAt(string $created_at) Return the first ChildCartCreditNote filtered by the created_at column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildCartCreditNote requireOneByUpdatedAt(string $updated_at) Return the first ChildCartCreditNote filtered by the updated_at column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 *
 * @method     ChildCartCreditNote[]|ObjectCollection find(ConnectionInterface $con = null) Return ChildCartCreditNote objects based on current ModelCriteria
 * @method     ChildCartCreditNote[]|ObjectCollection findByCartId(int $cart_id) Return ChildCartCreditNote objects filtered by the cart_id column
 * @method     ChildCartCreditNote[]|ObjectCollection findByCreditNoteId(int $credit_note_id) Return ChildCartCreditNote objects filtered by the credit_note_id column
 * @method     ChildCartCreditNote[]|ObjectCollection findByAmountPrice(string $amount_price) Return ChildCartCreditNote objects filtered by the amount_price column
 * @method     ChildCartCreditNote[]|ObjectCollection findByCreatedAt(string $created_at) Return ChildCartCreditNote objects filtered by the created_at column
 * @method     ChildCartCreditNote[]|ObjectCollection findByUpdatedAt(string $updated_at) Return ChildCartCreditNote objects filtered by the updated_at column
 * @method     ChildCartCreditNote[]|\Propel\Runtime\Util\PropelModelPager paginate($page = 1, $maxPerPage = 10, ConnectionInterface $con = null) Issue a SELECT query based on the current ModelCriteria and uses a page and a maximum number of results per page to compute an offset and a limit
 *
 */
abstract class CartCreditNoteQuery extends ModelCriteria
{
    protected $entityNotFoundExceptionClass = '\\Propel\\Runtime\\Exception\\EntityNotFoundException';

    /**
     * Initializes internal state of \CreditNote\Model\Base\CartCreditNoteQuery object.
     *
     * @param     string $dbName The database name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = 'thelia', $modelName = '\\CreditNote\\Model\\CartCreditNote', $modelAlias = null)
    {
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new ChildCartCreditNoteQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param     Criteria $criteria Optional Criteria to build the query from
     *
     * @return ChildCartCreditNoteQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof ChildCartCreditNoteQuery) {
            return $criteria;
        }
        $query = new ChildCartCreditNoteQuery();
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
     * $obj = $c->findPk(array(12, 34), $con);
     * </code>
     *
     * @param array[$cart_id, $credit_note_id] $key Primary key to use for the query
     * @param ConnectionInterface $con an optional connection object
     *
     * @return ChildCartCreditNote|array|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }

        if ($con === null) {
            $con = Propel::getServiceContainer()->getReadConnection(CartCreditNoteTableMap::DATABASE_NAME);
        }

        $this->basePreSelect($con);

        if (
            $this->formatter || $this->modelAlias || $this->with || $this->select
            || $this->selectColumns || $this->asColumns || $this->selectModifiers
            || $this->map || $this->having || $this->joins
        ) {
            return $this->findPkComplex($key, $con);
        }

        if ((null !== ($obj = CartCreditNoteTableMap::getInstanceFromPool(serialize([(null === $key[0] || is_scalar($key[0]) || is_callable([$key[0], '__toString']) ? (string) $key[0] : $key[0]), (null === $key[1] || is_scalar($key[1]) || is_callable([$key[1], '__toString']) ? (string) $key[1] : $key[1])]))))) {
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
     * @return ChildCartCreditNote A model object, or null if the key is not found
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT `cart_id`, `credit_note_id`, `amount_price`, `created_at`, `updated_at` FROM `cart_credit_note` WHERE `cart_id` = :p0 AND `credit_note_id` = :p1';
        try {
            $stmt = $con->prepare($sql);
            $stmt->bindValue(':p0', $key[0], PDO::PARAM_INT);
            $stmt->bindValue(':p1', $key[1], PDO::PARAM_INT);
            $stmt->execute();
        } catch (Exception $e) {
            Propel::log($e->getMessage(), Propel::LOG_ERR);
            throw new PropelException(sprintf('Unable to execute SELECT statement [%s]', $sql), 0, $e);
        }
        $obj = null;
        if ($row = $stmt->fetch(\PDO::FETCH_NUM)) {
            /** @var ChildCartCreditNote $obj */
            $obj = new ChildCartCreditNote();
            $obj->hydrate($row);
            CartCreditNoteTableMap::addInstanceToPool($obj, serialize([(null === $key[0] || is_scalar($key[0]) || is_callable([$key[0], '__toString']) ? (string) $key[0] : $key[0]), (null === $key[1] || is_scalar($key[1]) || is_callable([$key[1], '__toString']) ? (string) $key[1] : $key[1])]));
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
     * @return ChildCartCreditNote|array|mixed the result, formatted by the current formatter
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
     * $objs = $c->findPks(array(array(12, 56), array(832, 123), array(123, 456)), $con);
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
     * @return $this|ChildCartCreditNoteQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {
        $this->addUsingAlias(CartCreditNoteTableMap::COL_CART_ID, $key[0], Criteria::EQUAL);
        $this->addUsingAlias(CartCreditNoteTableMap::COL_CREDIT_NOTE_ID, $key[1], Criteria::EQUAL);

        return $this;
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return $this|ChildCartCreditNoteQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {
        if (empty($keys)) {
            return $this->add(null, '1<>1', Criteria::CUSTOM);
        }
        foreach ($keys as $key) {
            $cton0 = $this->getNewCriterion(CartCreditNoteTableMap::COL_CART_ID, $key[0], Criteria::EQUAL);
            $cton1 = $this->getNewCriterion(CartCreditNoteTableMap::COL_CREDIT_NOTE_ID, $key[1], Criteria::EQUAL);
            $cton0->addAnd($cton1);
            $this->addOr($cton0);
        }

        return $this;
    }

    /**
     * Filter the query on the cart_id column
     *
     * Example usage:
     * <code>
     * $query->filterByCartId(1234); // WHERE cart_id = 1234
     * $query->filterByCartId(array(12, 34)); // WHERE cart_id IN (12, 34)
     * $query->filterByCartId(array('min' => 12)); // WHERE cart_id > 12
     * </code>
     *
     * @see       filterByCart()
     *
     * @param     mixed $cartId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for \in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildCartCreditNoteQuery The current query, for fluid interface
     */
    public function filterByCartId($cartId = null, $comparison = null)
    {
        if (\is_array($cartId)) {
            $useMinMax = false;
            if (isset($cartId['min'])) {
                $this->addUsingAlias(CartCreditNoteTableMap::COL_CART_ID, $cartId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($cartId['max'])) {
                $this->addUsingAlias(CartCreditNoteTableMap::COL_CART_ID, $cartId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CartCreditNoteTableMap::COL_CART_ID, $cartId, $comparison);
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
     * @return $this|ChildCartCreditNoteQuery The current query, for fluid interface
     */
    public function filterByCreditNoteId($creditNoteId = null, $comparison = null)
    {
        if (\is_array($creditNoteId)) {
            $useMinMax = false;
            if (isset($creditNoteId['min'])) {
                $this->addUsingAlias(CartCreditNoteTableMap::COL_CREDIT_NOTE_ID, $creditNoteId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($creditNoteId['max'])) {
                $this->addUsingAlias(CartCreditNoteTableMap::COL_CREDIT_NOTE_ID, $creditNoteId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CartCreditNoteTableMap::COL_CREDIT_NOTE_ID, $creditNoteId, $comparison);
    }

    /**
     * Filter the query on the amount_price column
     *
     * Example usage:
     * <code>
     * $query->filterByAmountPrice(1234); // WHERE amount_price = 1234
     * $query->filterByAmountPrice(array(12, 34)); // WHERE amount_price IN (12, 34)
     * $query->filterByAmountPrice(array('min' => 12)); // WHERE amount_price > 12
     * </code>
     *
     * @param     mixed $amountPrice The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for \in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildCartCreditNoteQuery The current query, for fluid interface
     */
    public function filterByAmountPrice($amountPrice = null, $comparison = null)
    {
        if (\is_array($amountPrice)) {
            $useMinMax = false;
            if (isset($amountPrice['min'])) {
                $this->addUsingAlias(CartCreditNoteTableMap::COL_AMOUNT_PRICE, $amountPrice['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($amountPrice['max'])) {
                $this->addUsingAlias(CartCreditNoteTableMap::COL_AMOUNT_PRICE, $amountPrice['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CartCreditNoteTableMap::COL_AMOUNT_PRICE, $amountPrice, $comparison);
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
     * @return $this|ChildCartCreditNoteQuery The current query, for fluid interface
     */
    public function filterByCreatedAt($createdAt = null, $comparison = null)
    {
        if (\is_array($createdAt)) {
            $useMinMax = false;
            if (isset($createdAt['min'])) {
                $this->addUsingAlias(CartCreditNoteTableMap::COL_CREATED_AT, $createdAt['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($createdAt['max'])) {
                $this->addUsingAlias(CartCreditNoteTableMap::COL_CREATED_AT, $createdAt['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CartCreditNoteTableMap::COL_CREATED_AT, $createdAt, $comparison);
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
     * @return $this|ChildCartCreditNoteQuery The current query, for fluid interface
     */
    public function filterByUpdatedAt($updatedAt = null, $comparison = null)
    {
        if (\is_array($updatedAt)) {
            $useMinMax = false;
            if (isset($updatedAt['min'])) {
                $this->addUsingAlias(CartCreditNoteTableMap::COL_UPDATED_AT, $updatedAt['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($updatedAt['max'])) {
                $this->addUsingAlias(CartCreditNoteTableMap::COL_UPDATED_AT, $updatedAt['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CartCreditNoteTableMap::COL_UPDATED_AT, $updatedAt, $comparison);
    }

    /**
     * Filter the query by a related \Thelia\Model\Cart object
     *
     * @param \Thelia\Model\Cart|ObjectCollection $cart The related object(s) to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @throws \Propel\Runtime\Exception\PropelException
     *
     * @return ChildCartCreditNoteQuery The current query, for fluid interface
     */
    public function filterByCart($cart, $comparison = null)
    {
        if ($cart instanceof \Thelia\Model\Cart) {
            return $this
                ->addUsingAlias(CartCreditNoteTableMap::COL_CART_ID, $cart->getId(), $comparison);
        } elseif ($cart instanceof ObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(CartCreditNoteTableMap::COL_CART_ID, $cart->toKeyValue('PrimaryKey', 'Id'), $comparison);
        } else {
            throw new PropelException('filterByCart() only accepts arguments of type \Thelia\Model\Cart or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the Cart relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return $this|ChildCartCreditNoteQuery The current query, for fluid interface
     */
    public function joinCart($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('Cart');

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
            $this->addJoinObject($join, 'Cart');
        }

        return $this;
    }

    /**
     * Use the Cart relation Cart object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return \Thelia\Model\CartQuery A secondary query class using the current class as primary query
     */
    public function useCartQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinCart($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'Cart', '\Thelia\Model\CartQuery');
    }

    /**
     * Filter the query by a related \CreditNote\Model\CreditNote object
     *
     * @param \CreditNote\Model\CreditNote|ObjectCollection $creditNote The related object(s) to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @throws \Propel\Runtime\Exception\PropelException
     *
     * @return ChildCartCreditNoteQuery The current query, for fluid interface
     */
    public function filterByCreditNote($creditNote, $comparison = null)
    {
        if ($creditNote instanceof \CreditNote\Model\CreditNote) {
            return $this
                ->addUsingAlias(CartCreditNoteTableMap::COL_CREDIT_NOTE_ID, $creditNote->getId(), $comparison);
        } elseif ($creditNote instanceof ObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(CartCreditNoteTableMap::COL_CREDIT_NOTE_ID, $creditNote->toKeyValue('PrimaryKey', 'Id'), $comparison);
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
     * @return $this|ChildCartCreditNoteQuery The current query, for fluid interface
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
     * Exclude object from result
     *
     * @param   ChildCartCreditNote $cartCreditNote Object to remove from the list of results
     *
     * @return $this|ChildCartCreditNoteQuery The current query, for fluid interface
     */
    public function prune($cartCreditNote = null)
    {
        if ($cartCreditNote) {
            $this->addCond('pruneCond0', $this->getAliasedColName(CartCreditNoteTableMap::COL_CART_ID), $cartCreditNote->getCartId(), Criteria::NOT_EQUAL);
            $this->addCond('pruneCond1', $this->getAliasedColName(CartCreditNoteTableMap::COL_CREDIT_NOTE_ID), $cartCreditNote->getCreditNoteId(), Criteria::NOT_EQUAL);
            $this->combine(array('pruneCond0', 'pruneCond1'), Criteria::LOGICAL_OR);
        }

        return $this;
    }

    /**
     * Deletes all rows from the cart_credit_note table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public function doDeleteAll(ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(CartCreditNoteTableMap::DATABASE_NAME);
        }

        // use transaction because $criteria could contain info
        // for more than one table or we could emulating ON DELETE CASCADE, etc.
        return $con->transaction(function () use ($con) {
            $affectedRows = 0; // initialize var to track total num of affected rows
            $affectedRows += parent::doDeleteAll($con);
            // Because this db requires some delete cascade/set null emulation, we have to
            // clear the cached instance *after* the emulation has happened (since
            // instances get re-added by the select statement contained therein).
            CartCreditNoteTableMap::clearInstancePool();
            CartCreditNoteTableMap::clearRelatedInstancePool();

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
            $con = Propel::getServiceContainer()->getWriteConnection(CartCreditNoteTableMap::DATABASE_NAME);
        }

        $criteria = $this;

        // Set the correct dbName
        $criteria->setDbName(CartCreditNoteTableMap::DATABASE_NAME);

        // use transaction because $criteria could contain info
        // for more than one table or we could emulating ON DELETE CASCADE, etc.
        return $con->transaction(function () use ($con, $criteria) {
            $affectedRows = 0; // initialize var to track total num of affected rows

            CartCreditNoteTableMap::removeInstanceFromPool($criteria);

            $affectedRows += ModelCriteria::delete($con);
            CartCreditNoteTableMap::clearRelatedInstancePool();

            return $affectedRows;
        });
    }

    // timestampable behavior

    /**
     * Filter by the latest updated
     *
     * @param      int $nbDays Maximum age of the latest update in days
     *
     * @return     $this|ChildCartCreditNoteQuery The current query, for fluid interface
     */
    public function recentlyUpdated($nbDays = 7)
    {
        return $this->addUsingAlias(CartCreditNoteTableMap::COL_UPDATED_AT, time() - $nbDays * 24 * 60 * 60, Criteria::GREATER_EQUAL);
    }

    /**
     * Order by update date desc
     *
     * @return     $this|ChildCartCreditNoteQuery The current query, for fluid interface
     */
    public function lastUpdatedFirst()
    {
        return $this->addDescendingOrderByColumn(CartCreditNoteTableMap::COL_UPDATED_AT);
    }

    /**
     * Order by update date asc
     *
     * @return     $this|ChildCartCreditNoteQuery The current query, for fluid interface
     */
    public function firstUpdatedFirst()
    {
        return $this->addAscendingOrderByColumn(CartCreditNoteTableMap::COL_UPDATED_AT);
    }

    /**
     * Order by create date desc
     *
     * @return     $this|ChildCartCreditNoteQuery The current query, for fluid interface
     */
    public function lastCreatedFirst()
    {
        return $this->addDescendingOrderByColumn(CartCreditNoteTableMap::COL_CREATED_AT);
    }

    /**
     * Filter by the latest created
     *
     * @param      int $nbDays Maximum age of in days
     *
     * @return     $this|ChildCartCreditNoteQuery The current query, for fluid interface
     */
    public function recentlyCreated($nbDays = 7)
    {
        return $this->addUsingAlias(CartCreditNoteTableMap::COL_CREATED_AT, time() - $nbDays * 24 * 60 * 60, Criteria::GREATER_EQUAL);
    }

    /**
     * Order by create date asc
     *
     * @return     $this|ChildCartCreditNoteQuery The current query, for fluid interface
     */
    public function firstCreatedFirst()
    {
        return $this->addAscendingOrderByColumn(CartCreditNoteTableMap::COL_CREATED_AT);
    }

} // CartCreditNoteQuery
