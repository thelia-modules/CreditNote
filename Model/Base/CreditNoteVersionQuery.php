<?php

namespace CreditNote\Model\Base;

use \Exception;
use \PDO;
use CreditNote\Model\CreditNoteVersion as ChildCreditNoteVersion;
use CreditNote\Model\CreditNoteVersionQuery as ChildCreditNoteVersionQuery;
use CreditNote\Model\Map\CreditNoteVersionTableMap;
use Propel\Runtime\Propel;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\ActiveQuery\ModelJoin;
use Propel\Runtime\Collection\Collection;
use Propel\Runtime\Collection\ObjectCollection;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\Exception\PropelException;

/**
 * Base class that represents a query for the 'credit_note_version' table.
 *
 *
 *
 * @method     ChildCreditNoteVersionQuery orderById($order = Criteria::ASC) Order by the id column
 * @method     ChildCreditNoteVersionQuery orderByRef($order = Criteria::ASC) Order by the ref column
 * @method     ChildCreditNoteVersionQuery orderByInvoiceRef($order = Criteria::ASC) Order by the invoice_ref column
 * @method     ChildCreditNoteVersionQuery orderByInvoiceAddressId($order = Criteria::ASC) Order by the invoice_address_id column
 * @method     ChildCreditNoteVersionQuery orderByInvoiceDate($order = Criteria::ASC) Order by the invoice_date column
 * @method     ChildCreditNoteVersionQuery orderByOrderId($order = Criteria::ASC) Order by the order_id column
 * @method     ChildCreditNoteVersionQuery orderByCustomerId($order = Criteria::ASC) Order by the customer_id column
 * @method     ChildCreditNoteVersionQuery orderByParentId($order = Criteria::ASC) Order by the parent_id column
 * @method     ChildCreditNoteVersionQuery orderByTypeId($order = Criteria::ASC) Order by the type_id column
 * @method     ChildCreditNoteVersionQuery orderByStatusId($order = Criteria::ASC) Order by the status_id column
 * @method     ChildCreditNoteVersionQuery orderByCurrencyId($order = Criteria::ASC) Order by the currency_id column
 * @method     ChildCreditNoteVersionQuery orderByCurrencyRate($order = Criteria::ASC) Order by the currency_rate column
 * @method     ChildCreditNoteVersionQuery orderByTotalPrice($order = Criteria::ASC) Order by the total_price column
 * @method     ChildCreditNoteVersionQuery orderByTotalPriceWithTax($order = Criteria::ASC) Order by the total_price_with_tax column
 * @method     ChildCreditNoteVersionQuery orderByDiscountWithoutTax($order = Criteria::ASC) Order by the discount_without_tax column
 * @method     ChildCreditNoteVersionQuery orderByDiscountWithTax($order = Criteria::ASC) Order by the discount_with_tax column
 * @method     ChildCreditNoteVersionQuery orderByAllowPartialUse($order = Criteria::ASC) Order by the allow_partial_use column
 * @method     ChildCreditNoteVersionQuery orderByCreatedAt($order = Criteria::ASC) Order by the created_at column
 * @method     ChildCreditNoteVersionQuery orderByUpdatedAt($order = Criteria::ASC) Order by the updated_at column
 * @method     ChildCreditNoteVersionQuery orderByVersion($order = Criteria::ASC) Order by the version column
 * @method     ChildCreditNoteVersionQuery orderByVersionCreatedAt($order = Criteria::ASC) Order by the version_created_at column
 * @method     ChildCreditNoteVersionQuery orderByVersionCreatedBy($order = Criteria::ASC) Order by the version_created_by column
 * @method     ChildCreditNoteVersionQuery orderByOrderIdVersion($order = Criteria::ASC) Order by the order_id_version column
 * @method     ChildCreditNoteVersionQuery orderByCustomerIdVersion($order = Criteria::ASC) Order by the customer_id_version column
 * @method     ChildCreditNoteVersionQuery orderByParentIdVersion($order = Criteria::ASC) Order by the parent_id_version column
 * @method     ChildCreditNoteVersionQuery orderByCreditNoteIds($order = Criteria::ASC) Order by the credit_note_ids column
 * @method     ChildCreditNoteVersionQuery orderByCreditNoteVersions($order = Criteria::ASC) Order by the credit_note_versions column
 *
 * @method     ChildCreditNoteVersionQuery groupById() Group by the id column
 * @method     ChildCreditNoteVersionQuery groupByRef() Group by the ref column
 * @method     ChildCreditNoteVersionQuery groupByInvoiceRef() Group by the invoice_ref column
 * @method     ChildCreditNoteVersionQuery groupByInvoiceAddressId() Group by the invoice_address_id column
 * @method     ChildCreditNoteVersionQuery groupByInvoiceDate() Group by the invoice_date column
 * @method     ChildCreditNoteVersionQuery groupByOrderId() Group by the order_id column
 * @method     ChildCreditNoteVersionQuery groupByCustomerId() Group by the customer_id column
 * @method     ChildCreditNoteVersionQuery groupByParentId() Group by the parent_id column
 * @method     ChildCreditNoteVersionQuery groupByTypeId() Group by the type_id column
 * @method     ChildCreditNoteVersionQuery groupByStatusId() Group by the status_id column
 * @method     ChildCreditNoteVersionQuery groupByCurrencyId() Group by the currency_id column
 * @method     ChildCreditNoteVersionQuery groupByCurrencyRate() Group by the currency_rate column
 * @method     ChildCreditNoteVersionQuery groupByTotalPrice() Group by the total_price column
 * @method     ChildCreditNoteVersionQuery groupByTotalPriceWithTax() Group by the total_price_with_tax column
 * @method     ChildCreditNoteVersionQuery groupByDiscountWithoutTax() Group by the discount_without_tax column
 * @method     ChildCreditNoteVersionQuery groupByDiscountWithTax() Group by the discount_with_tax column
 * @method     ChildCreditNoteVersionQuery groupByAllowPartialUse() Group by the allow_partial_use column
 * @method     ChildCreditNoteVersionQuery groupByCreatedAt() Group by the created_at column
 * @method     ChildCreditNoteVersionQuery groupByUpdatedAt() Group by the updated_at column
 * @method     ChildCreditNoteVersionQuery groupByVersion() Group by the version column
 * @method     ChildCreditNoteVersionQuery groupByVersionCreatedAt() Group by the version_created_at column
 * @method     ChildCreditNoteVersionQuery groupByVersionCreatedBy() Group by the version_created_by column
 * @method     ChildCreditNoteVersionQuery groupByOrderIdVersion() Group by the order_id_version column
 * @method     ChildCreditNoteVersionQuery groupByCustomerIdVersion() Group by the customer_id_version column
 * @method     ChildCreditNoteVersionQuery groupByParentIdVersion() Group by the parent_id_version column
 * @method     ChildCreditNoteVersionQuery groupByCreditNoteIds() Group by the credit_note_ids column
 * @method     ChildCreditNoteVersionQuery groupByCreditNoteVersions() Group by the credit_note_versions column
 *
 * @method     ChildCreditNoteVersionQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     ChildCreditNoteVersionQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     ChildCreditNoteVersionQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     ChildCreditNoteVersionQuery leftJoinCreditNote($relationAlias = null) Adds a LEFT JOIN clause to the query using the CreditNote relation
 * @method     ChildCreditNoteVersionQuery rightJoinCreditNote($relationAlias = null) Adds a RIGHT JOIN clause to the query using the CreditNote relation
 * @method     ChildCreditNoteVersionQuery innerJoinCreditNote($relationAlias = null) Adds a INNER JOIN clause to the query using the CreditNote relation
 *
 * @method     ChildCreditNoteVersion findOne(ConnectionInterface $con = null) Return the first ChildCreditNoteVersion matching the query
 * @method     ChildCreditNoteVersion findOneOrCreate(ConnectionInterface $con = null) Return the first ChildCreditNoteVersion matching the query, or a new ChildCreditNoteVersion object populated from the query conditions when no match is found
 *
 * @method     ChildCreditNoteVersion findOneById(int $id) Return the first ChildCreditNoteVersion filtered by the id column
 * @method     ChildCreditNoteVersion findOneByRef(string $ref) Return the first ChildCreditNoteVersion filtered by the ref column
 * @method     ChildCreditNoteVersion findOneByInvoiceRef(string $invoice_ref) Return the first ChildCreditNoteVersion filtered by the invoice_ref column
 * @method     ChildCreditNoteVersion findOneByInvoiceAddressId(int $invoice_address_id) Return the first ChildCreditNoteVersion filtered by the invoice_address_id column
 * @method     ChildCreditNoteVersion findOneByInvoiceDate(string $invoice_date) Return the first ChildCreditNoteVersion filtered by the invoice_date column
 * @method     ChildCreditNoteVersion findOneByOrderId(int $order_id) Return the first ChildCreditNoteVersion filtered by the order_id column
 * @method     ChildCreditNoteVersion findOneByCustomerId(int $customer_id) Return the first ChildCreditNoteVersion filtered by the customer_id column
 * @method     ChildCreditNoteVersion findOneByParentId(int $parent_id) Return the first ChildCreditNoteVersion filtered by the parent_id column
 * @method     ChildCreditNoteVersion findOneByTypeId(int $type_id) Return the first ChildCreditNoteVersion filtered by the type_id column
 * @method     ChildCreditNoteVersion findOneByStatusId(int $status_id) Return the first ChildCreditNoteVersion filtered by the status_id column
 * @method     ChildCreditNoteVersion findOneByCurrencyId(int $currency_id) Return the first ChildCreditNoteVersion filtered by the currency_id column
 * @method     ChildCreditNoteVersion findOneByCurrencyRate(double $currency_rate) Return the first ChildCreditNoteVersion filtered by the currency_rate column
 * @method     ChildCreditNoteVersion findOneByTotalPrice(string $total_price) Return the first ChildCreditNoteVersion filtered by the total_price column
 * @method     ChildCreditNoteVersion findOneByTotalPriceWithTax(string $total_price_with_tax) Return the first ChildCreditNoteVersion filtered by the total_price_with_tax column
 * @method     ChildCreditNoteVersion findOneByDiscountWithoutTax(string $discount_without_tax) Return the first ChildCreditNoteVersion filtered by the discount_without_tax column
 * @method     ChildCreditNoteVersion findOneByDiscountWithTax(string $discount_with_tax) Return the first ChildCreditNoteVersion filtered by the discount_with_tax column
 * @method     ChildCreditNoteVersion findOneByAllowPartialUse(boolean $allow_partial_use) Return the first ChildCreditNoteVersion filtered by the allow_partial_use column
 * @method     ChildCreditNoteVersion findOneByCreatedAt(string $created_at) Return the first ChildCreditNoteVersion filtered by the created_at column
 * @method     ChildCreditNoteVersion findOneByUpdatedAt(string $updated_at) Return the first ChildCreditNoteVersion filtered by the updated_at column
 * @method     ChildCreditNoteVersion findOneByVersion(int $version) Return the first ChildCreditNoteVersion filtered by the version column
 * @method     ChildCreditNoteVersion findOneByVersionCreatedAt(string $version_created_at) Return the first ChildCreditNoteVersion filtered by the version_created_at column
 * @method     ChildCreditNoteVersion findOneByVersionCreatedBy(string $version_created_by) Return the first ChildCreditNoteVersion filtered by the version_created_by column
 * @method     ChildCreditNoteVersion findOneByOrderIdVersion(int $order_id_version) Return the first ChildCreditNoteVersion filtered by the order_id_version column
 * @method     ChildCreditNoteVersion findOneByCustomerIdVersion(int $customer_id_version) Return the first ChildCreditNoteVersion filtered by the customer_id_version column
 * @method     ChildCreditNoteVersion findOneByParentIdVersion(int $parent_id_version) Return the first ChildCreditNoteVersion filtered by the parent_id_version column
 * @method     ChildCreditNoteVersion findOneByCreditNoteIds(array $credit_note_ids) Return the first ChildCreditNoteVersion filtered by the credit_note_ids column
 * @method     ChildCreditNoteVersion findOneByCreditNoteVersions(array $credit_note_versions) Return the first ChildCreditNoteVersion filtered by the credit_note_versions column
 *
 * @method     array findById(int $id) Return ChildCreditNoteVersion objects filtered by the id column
 * @method     array findByRef(string $ref) Return ChildCreditNoteVersion objects filtered by the ref column
 * @method     array findByInvoiceRef(string $invoice_ref) Return ChildCreditNoteVersion objects filtered by the invoice_ref column
 * @method     array findByInvoiceAddressId(int $invoice_address_id) Return ChildCreditNoteVersion objects filtered by the invoice_address_id column
 * @method     array findByInvoiceDate(string $invoice_date) Return ChildCreditNoteVersion objects filtered by the invoice_date column
 * @method     array findByOrderId(int $order_id) Return ChildCreditNoteVersion objects filtered by the order_id column
 * @method     array findByCustomerId(int $customer_id) Return ChildCreditNoteVersion objects filtered by the customer_id column
 * @method     array findByParentId(int $parent_id) Return ChildCreditNoteVersion objects filtered by the parent_id column
 * @method     array findByTypeId(int $type_id) Return ChildCreditNoteVersion objects filtered by the type_id column
 * @method     array findByStatusId(int $status_id) Return ChildCreditNoteVersion objects filtered by the status_id column
 * @method     array findByCurrencyId(int $currency_id) Return ChildCreditNoteVersion objects filtered by the currency_id column
 * @method     array findByCurrencyRate(double $currency_rate) Return ChildCreditNoteVersion objects filtered by the currency_rate column
 * @method     array findByTotalPrice(string $total_price) Return ChildCreditNoteVersion objects filtered by the total_price column
 * @method     array findByTotalPriceWithTax(string $total_price_with_tax) Return ChildCreditNoteVersion objects filtered by the total_price_with_tax column
 * @method     array findByDiscountWithoutTax(string $discount_without_tax) Return ChildCreditNoteVersion objects filtered by the discount_without_tax column
 * @method     array findByDiscountWithTax(string $discount_with_tax) Return ChildCreditNoteVersion objects filtered by the discount_with_tax column
 * @method     array findByAllowPartialUse(boolean $allow_partial_use) Return ChildCreditNoteVersion objects filtered by the allow_partial_use column
 * @method     array findByCreatedAt(string $created_at) Return ChildCreditNoteVersion objects filtered by the created_at column
 * @method     array findByUpdatedAt(string $updated_at) Return ChildCreditNoteVersion objects filtered by the updated_at column
 * @method     array findByVersion(int $version) Return ChildCreditNoteVersion objects filtered by the version column
 * @method     array findByVersionCreatedAt(string $version_created_at) Return ChildCreditNoteVersion objects filtered by the version_created_at column
 * @method     array findByVersionCreatedBy(string $version_created_by) Return ChildCreditNoteVersion objects filtered by the version_created_by column
 * @method     array findByOrderIdVersion(int $order_id_version) Return ChildCreditNoteVersion objects filtered by the order_id_version column
 * @method     array findByCustomerIdVersion(int $customer_id_version) Return ChildCreditNoteVersion objects filtered by the customer_id_version column
 * @method     array findByParentIdVersion(int $parent_id_version) Return ChildCreditNoteVersion objects filtered by the parent_id_version column
 * @method     array findByCreditNoteIds(array $credit_note_ids) Return ChildCreditNoteVersion objects filtered by the credit_note_ids column
 * @method     array findByCreditNoteVersions(array $credit_note_versions) Return ChildCreditNoteVersion objects filtered by the credit_note_versions column
 *
 */
abstract class CreditNoteVersionQuery extends ModelCriteria
{

    /**
     * Initializes internal state of \CreditNote\Model\Base\CreditNoteVersionQuery object.
     *
     * @param     string $dbName The database name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = 'thelia', $modelName = '\\CreditNote\\Model\\CreditNoteVersion', $modelAlias = null)
    {
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new ChildCreditNoteVersionQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param     Criteria $criteria Optional Criteria to build the query from
     *
     * @return ChildCreditNoteVersionQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof \CreditNote\Model\CreditNoteVersionQuery) {
            return $criteria;
        }
        $query = new \CreditNote\Model\CreditNoteVersionQuery();
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
     * @param array[$id, $version] $key Primary key to use for the query
     * @param ConnectionInterface $con an optional connection object
     *
     * @return ChildCreditNoteVersion|array|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = CreditNoteVersionTableMap::getInstanceFromPool(serialize(array((string) $key[0], (string) $key[1]))))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getServiceContainer()->getReadConnection(CreditNoteVersionTableMap::DATABASE_NAME);
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
     * @return   ChildCreditNoteVersion A model object, or null if the key is not found
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT ID, REF, INVOICE_REF, INVOICE_ADDRESS_ID, INVOICE_DATE, ORDER_ID, CUSTOMER_ID, PARENT_ID, TYPE_ID, STATUS_ID, CURRENCY_ID, CURRENCY_RATE, TOTAL_PRICE, TOTAL_PRICE_WITH_TAX, DISCOUNT_WITHOUT_TAX, DISCOUNT_WITH_TAX, ALLOW_PARTIAL_USE, CREATED_AT, UPDATED_AT, VERSION, VERSION_CREATED_AT, VERSION_CREATED_BY, ORDER_ID_VERSION, CUSTOMER_ID_VERSION, PARENT_ID_VERSION, CREDIT_NOTE_IDS, CREDIT_NOTE_VERSIONS FROM credit_note_version WHERE ID = :p0 AND VERSION = :p1';
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
            $obj = new ChildCreditNoteVersion();
            $obj->hydrate($row);
            CreditNoteVersionTableMap::addInstanceToPool($obj, serialize(array((string) $key[0], (string) $key[1])));
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
     * @return ChildCreditNoteVersion|array|mixed the result, formatted by the current formatter
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
     * @return ChildCreditNoteVersionQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {
        $this->addUsingAlias(CreditNoteVersionTableMap::ID, $key[0], Criteria::EQUAL);
        $this->addUsingAlias(CreditNoteVersionTableMap::VERSION, $key[1], Criteria::EQUAL);

        return $this;
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return ChildCreditNoteVersionQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {
        if (empty($keys)) {
            return $this->add(null, '1<>1', Criteria::CUSTOM);
        }
        foreach ($keys as $key) {
            $cton0 = $this->getNewCriterion(CreditNoteVersionTableMap::ID, $key[0], Criteria::EQUAL);
            $cton1 = $this->getNewCriterion(CreditNoteVersionTableMap::VERSION, $key[1], Criteria::EQUAL);
            $cton0->addAnd($cton1);
            $this->addOr($cton0);
        }

        return $this;
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
     * @see       filterByCreditNote()
     *
     * @param     mixed $id The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildCreditNoteVersionQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id)) {
            $useMinMax = false;
            if (isset($id['min'])) {
                $this->addUsingAlias(CreditNoteVersionTableMap::ID, $id['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($id['max'])) {
                $this->addUsingAlias(CreditNoteVersionTableMap::ID, $id['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CreditNoteVersionTableMap::ID, $id, $comparison);
    }

    /**
     * Filter the query on the ref column
     *
     * Example usage:
     * <code>
     * $query->filterByRef('fooValue');   // WHERE ref = 'fooValue'
     * $query->filterByRef('%fooValue%'); // WHERE ref LIKE '%fooValue%'
     * </code>
     *
     * @param     string $ref The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildCreditNoteVersionQuery The current query, for fluid interface
     */
    public function filterByRef($ref = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($ref)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $ref)) {
                $ref = str_replace('*', '%', $ref);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(CreditNoteVersionTableMap::REF, $ref, $comparison);
    }

    /**
     * Filter the query on the invoice_ref column
     *
     * Example usage:
     * <code>
     * $query->filterByInvoiceRef('fooValue');   // WHERE invoice_ref = 'fooValue'
     * $query->filterByInvoiceRef('%fooValue%'); // WHERE invoice_ref LIKE '%fooValue%'
     * </code>
     *
     * @param     string $invoiceRef The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildCreditNoteVersionQuery The current query, for fluid interface
     */
    public function filterByInvoiceRef($invoiceRef = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($invoiceRef)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $invoiceRef)) {
                $invoiceRef = str_replace('*', '%', $invoiceRef);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(CreditNoteVersionTableMap::INVOICE_REF, $invoiceRef, $comparison);
    }

    /**
     * Filter the query on the invoice_address_id column
     *
     * Example usage:
     * <code>
     * $query->filterByInvoiceAddressId(1234); // WHERE invoice_address_id = 1234
     * $query->filterByInvoiceAddressId(array(12, 34)); // WHERE invoice_address_id IN (12, 34)
     * $query->filterByInvoiceAddressId(array('min' => 12)); // WHERE invoice_address_id > 12
     * </code>
     *
     * @param     mixed $invoiceAddressId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildCreditNoteVersionQuery The current query, for fluid interface
     */
    public function filterByInvoiceAddressId($invoiceAddressId = null, $comparison = null)
    {
        if (is_array($invoiceAddressId)) {
            $useMinMax = false;
            if (isset($invoiceAddressId['min'])) {
                $this->addUsingAlias(CreditNoteVersionTableMap::INVOICE_ADDRESS_ID, $invoiceAddressId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($invoiceAddressId['max'])) {
                $this->addUsingAlias(CreditNoteVersionTableMap::INVOICE_ADDRESS_ID, $invoiceAddressId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CreditNoteVersionTableMap::INVOICE_ADDRESS_ID, $invoiceAddressId, $comparison);
    }

    /**
     * Filter the query on the invoice_date column
     *
     * Example usage:
     * <code>
     * $query->filterByInvoiceDate('2011-03-14'); // WHERE invoice_date = '2011-03-14'
     * $query->filterByInvoiceDate('now'); // WHERE invoice_date = '2011-03-14'
     * $query->filterByInvoiceDate(array('max' => 'yesterday')); // WHERE invoice_date > '2011-03-13'
     * </code>
     *
     * @param     mixed $invoiceDate The value to use as filter.
     *              Values can be integers (unix timestamps), DateTime objects, or strings.
     *              Empty strings are treated as NULL.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildCreditNoteVersionQuery The current query, for fluid interface
     */
    public function filterByInvoiceDate($invoiceDate = null, $comparison = null)
    {
        if (is_array($invoiceDate)) {
            $useMinMax = false;
            if (isset($invoiceDate['min'])) {
                $this->addUsingAlias(CreditNoteVersionTableMap::INVOICE_DATE, $invoiceDate['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($invoiceDate['max'])) {
                $this->addUsingAlias(CreditNoteVersionTableMap::INVOICE_DATE, $invoiceDate['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CreditNoteVersionTableMap::INVOICE_DATE, $invoiceDate, $comparison);
    }

    /**
     * Filter the query on the order_id column
     *
     * Example usage:
     * <code>
     * $query->filterByOrderId(1234); // WHERE order_id = 1234
     * $query->filterByOrderId(array(12, 34)); // WHERE order_id IN (12, 34)
     * $query->filterByOrderId(array('min' => 12)); // WHERE order_id > 12
     * </code>
     *
     * @param     mixed $orderId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildCreditNoteVersionQuery The current query, for fluid interface
     */
    public function filterByOrderId($orderId = null, $comparison = null)
    {
        if (is_array($orderId)) {
            $useMinMax = false;
            if (isset($orderId['min'])) {
                $this->addUsingAlias(CreditNoteVersionTableMap::ORDER_ID, $orderId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($orderId['max'])) {
                $this->addUsingAlias(CreditNoteVersionTableMap::ORDER_ID, $orderId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CreditNoteVersionTableMap::ORDER_ID, $orderId, $comparison);
    }

    /**
     * Filter the query on the customer_id column
     *
     * Example usage:
     * <code>
     * $query->filterByCustomerId(1234); // WHERE customer_id = 1234
     * $query->filterByCustomerId(array(12, 34)); // WHERE customer_id IN (12, 34)
     * $query->filterByCustomerId(array('min' => 12)); // WHERE customer_id > 12
     * </code>
     *
     * @param     mixed $customerId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildCreditNoteVersionQuery The current query, for fluid interface
     */
    public function filterByCustomerId($customerId = null, $comparison = null)
    {
        if (is_array($customerId)) {
            $useMinMax = false;
            if (isset($customerId['min'])) {
                $this->addUsingAlias(CreditNoteVersionTableMap::CUSTOMER_ID, $customerId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($customerId['max'])) {
                $this->addUsingAlias(CreditNoteVersionTableMap::CUSTOMER_ID, $customerId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CreditNoteVersionTableMap::CUSTOMER_ID, $customerId, $comparison);
    }

    /**
     * Filter the query on the parent_id column
     *
     * Example usage:
     * <code>
     * $query->filterByParentId(1234); // WHERE parent_id = 1234
     * $query->filterByParentId(array(12, 34)); // WHERE parent_id IN (12, 34)
     * $query->filterByParentId(array('min' => 12)); // WHERE parent_id > 12
     * </code>
     *
     * @param     mixed $parentId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildCreditNoteVersionQuery The current query, for fluid interface
     */
    public function filterByParentId($parentId = null, $comparison = null)
    {
        if (is_array($parentId)) {
            $useMinMax = false;
            if (isset($parentId['min'])) {
                $this->addUsingAlias(CreditNoteVersionTableMap::PARENT_ID, $parentId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($parentId['max'])) {
                $this->addUsingAlias(CreditNoteVersionTableMap::PARENT_ID, $parentId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CreditNoteVersionTableMap::PARENT_ID, $parentId, $comparison);
    }

    /**
     * Filter the query on the type_id column
     *
     * Example usage:
     * <code>
     * $query->filterByTypeId(1234); // WHERE type_id = 1234
     * $query->filterByTypeId(array(12, 34)); // WHERE type_id IN (12, 34)
     * $query->filterByTypeId(array('min' => 12)); // WHERE type_id > 12
     * </code>
     *
     * @param     mixed $typeId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildCreditNoteVersionQuery The current query, for fluid interface
     */
    public function filterByTypeId($typeId = null, $comparison = null)
    {
        if (is_array($typeId)) {
            $useMinMax = false;
            if (isset($typeId['min'])) {
                $this->addUsingAlias(CreditNoteVersionTableMap::TYPE_ID, $typeId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($typeId['max'])) {
                $this->addUsingAlias(CreditNoteVersionTableMap::TYPE_ID, $typeId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CreditNoteVersionTableMap::TYPE_ID, $typeId, $comparison);
    }

    /**
     * Filter the query on the status_id column
     *
     * Example usage:
     * <code>
     * $query->filterByStatusId(1234); // WHERE status_id = 1234
     * $query->filterByStatusId(array(12, 34)); // WHERE status_id IN (12, 34)
     * $query->filterByStatusId(array('min' => 12)); // WHERE status_id > 12
     * </code>
     *
     * @param     mixed $statusId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildCreditNoteVersionQuery The current query, for fluid interface
     */
    public function filterByStatusId($statusId = null, $comparison = null)
    {
        if (is_array($statusId)) {
            $useMinMax = false;
            if (isset($statusId['min'])) {
                $this->addUsingAlias(CreditNoteVersionTableMap::STATUS_ID, $statusId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($statusId['max'])) {
                $this->addUsingAlias(CreditNoteVersionTableMap::STATUS_ID, $statusId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CreditNoteVersionTableMap::STATUS_ID, $statusId, $comparison);
    }

    /**
     * Filter the query on the currency_id column
     *
     * Example usage:
     * <code>
     * $query->filterByCurrencyId(1234); // WHERE currency_id = 1234
     * $query->filterByCurrencyId(array(12, 34)); // WHERE currency_id IN (12, 34)
     * $query->filterByCurrencyId(array('min' => 12)); // WHERE currency_id > 12
     * </code>
     *
     * @param     mixed $currencyId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildCreditNoteVersionQuery The current query, for fluid interface
     */
    public function filterByCurrencyId($currencyId = null, $comparison = null)
    {
        if (is_array($currencyId)) {
            $useMinMax = false;
            if (isset($currencyId['min'])) {
                $this->addUsingAlias(CreditNoteVersionTableMap::CURRENCY_ID, $currencyId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($currencyId['max'])) {
                $this->addUsingAlias(CreditNoteVersionTableMap::CURRENCY_ID, $currencyId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CreditNoteVersionTableMap::CURRENCY_ID, $currencyId, $comparison);
    }

    /**
     * Filter the query on the currency_rate column
     *
     * Example usage:
     * <code>
     * $query->filterByCurrencyRate(1234); // WHERE currency_rate = 1234
     * $query->filterByCurrencyRate(array(12, 34)); // WHERE currency_rate IN (12, 34)
     * $query->filterByCurrencyRate(array('min' => 12)); // WHERE currency_rate > 12
     * </code>
     *
     * @param     mixed $currencyRate The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildCreditNoteVersionQuery The current query, for fluid interface
     */
    public function filterByCurrencyRate($currencyRate = null, $comparison = null)
    {
        if (is_array($currencyRate)) {
            $useMinMax = false;
            if (isset($currencyRate['min'])) {
                $this->addUsingAlias(CreditNoteVersionTableMap::CURRENCY_RATE, $currencyRate['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($currencyRate['max'])) {
                $this->addUsingAlias(CreditNoteVersionTableMap::CURRENCY_RATE, $currencyRate['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CreditNoteVersionTableMap::CURRENCY_RATE, $currencyRate, $comparison);
    }

    /**
     * Filter the query on the total_price column
     *
     * Example usage:
     * <code>
     * $query->filterByTotalPrice(1234); // WHERE total_price = 1234
     * $query->filterByTotalPrice(array(12, 34)); // WHERE total_price IN (12, 34)
     * $query->filterByTotalPrice(array('min' => 12)); // WHERE total_price > 12
     * </code>
     *
     * @param     mixed $totalPrice The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildCreditNoteVersionQuery The current query, for fluid interface
     */
    public function filterByTotalPrice($totalPrice = null, $comparison = null)
    {
        if (is_array($totalPrice)) {
            $useMinMax = false;
            if (isset($totalPrice['min'])) {
                $this->addUsingAlias(CreditNoteVersionTableMap::TOTAL_PRICE, $totalPrice['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($totalPrice['max'])) {
                $this->addUsingAlias(CreditNoteVersionTableMap::TOTAL_PRICE, $totalPrice['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CreditNoteVersionTableMap::TOTAL_PRICE, $totalPrice, $comparison);
    }

    /**
     * Filter the query on the total_price_with_tax column
     *
     * Example usage:
     * <code>
     * $query->filterByTotalPriceWithTax(1234); // WHERE total_price_with_tax = 1234
     * $query->filterByTotalPriceWithTax(array(12, 34)); // WHERE total_price_with_tax IN (12, 34)
     * $query->filterByTotalPriceWithTax(array('min' => 12)); // WHERE total_price_with_tax > 12
     * </code>
     *
     * @param     mixed $totalPriceWithTax The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildCreditNoteVersionQuery The current query, for fluid interface
     */
    public function filterByTotalPriceWithTax($totalPriceWithTax = null, $comparison = null)
    {
        if (is_array($totalPriceWithTax)) {
            $useMinMax = false;
            if (isset($totalPriceWithTax['min'])) {
                $this->addUsingAlias(CreditNoteVersionTableMap::TOTAL_PRICE_WITH_TAX, $totalPriceWithTax['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($totalPriceWithTax['max'])) {
                $this->addUsingAlias(CreditNoteVersionTableMap::TOTAL_PRICE_WITH_TAX, $totalPriceWithTax['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CreditNoteVersionTableMap::TOTAL_PRICE_WITH_TAX, $totalPriceWithTax, $comparison);
    }

    /**
     * Filter the query on the discount_without_tax column
     *
     * Example usage:
     * <code>
     * $query->filterByDiscountWithoutTax(1234); // WHERE discount_without_tax = 1234
     * $query->filterByDiscountWithoutTax(array(12, 34)); // WHERE discount_without_tax IN (12, 34)
     * $query->filterByDiscountWithoutTax(array('min' => 12)); // WHERE discount_without_tax > 12
     * </code>
     *
     * @param     mixed $discountWithoutTax The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildCreditNoteVersionQuery The current query, for fluid interface
     */
    public function filterByDiscountWithoutTax($discountWithoutTax = null, $comparison = null)
    {
        if (is_array($discountWithoutTax)) {
            $useMinMax = false;
            if (isset($discountWithoutTax['min'])) {
                $this->addUsingAlias(CreditNoteVersionTableMap::DISCOUNT_WITHOUT_TAX, $discountWithoutTax['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($discountWithoutTax['max'])) {
                $this->addUsingAlias(CreditNoteVersionTableMap::DISCOUNT_WITHOUT_TAX, $discountWithoutTax['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CreditNoteVersionTableMap::DISCOUNT_WITHOUT_TAX, $discountWithoutTax, $comparison);
    }

    /**
     * Filter the query on the discount_with_tax column
     *
     * Example usage:
     * <code>
     * $query->filterByDiscountWithTax(1234); // WHERE discount_with_tax = 1234
     * $query->filterByDiscountWithTax(array(12, 34)); // WHERE discount_with_tax IN (12, 34)
     * $query->filterByDiscountWithTax(array('min' => 12)); // WHERE discount_with_tax > 12
     * </code>
     *
     * @param     mixed $discountWithTax The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildCreditNoteVersionQuery The current query, for fluid interface
     */
    public function filterByDiscountWithTax($discountWithTax = null, $comparison = null)
    {
        if (is_array($discountWithTax)) {
            $useMinMax = false;
            if (isset($discountWithTax['min'])) {
                $this->addUsingAlias(CreditNoteVersionTableMap::DISCOUNT_WITH_TAX, $discountWithTax['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($discountWithTax['max'])) {
                $this->addUsingAlias(CreditNoteVersionTableMap::DISCOUNT_WITH_TAX, $discountWithTax['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CreditNoteVersionTableMap::DISCOUNT_WITH_TAX, $discountWithTax, $comparison);
    }

    /**
     * Filter the query on the allow_partial_use column
     *
     * Example usage:
     * <code>
     * $query->filterByAllowPartialUse(true); // WHERE allow_partial_use = true
     * $query->filterByAllowPartialUse('yes'); // WHERE allow_partial_use = true
     * </code>
     *
     * @param     boolean|string $allowPartialUse The value to use as filter.
     *              Non-boolean arguments are converted using the following rules:
     *                * 1, '1', 'true',  'on',  and 'yes' are converted to boolean true
     *                * 0, '0', 'false', 'off', and 'no'  are converted to boolean false
     *              Check on string values is case insensitive (so 'FaLsE' is seen as 'false').
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildCreditNoteVersionQuery The current query, for fluid interface
     */
    public function filterByAllowPartialUse($allowPartialUse = null, $comparison = null)
    {
        if (is_string($allowPartialUse)) {
            $allow_partial_use = in_array(strtolower($allowPartialUse), array('false', 'off', '-', 'no', 'n', '0', '')) ? false : true;
        }

        return $this->addUsingAlias(CreditNoteVersionTableMap::ALLOW_PARTIAL_USE, $allowPartialUse, $comparison);
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
     * @return ChildCreditNoteVersionQuery The current query, for fluid interface
     */
    public function filterByCreatedAt($createdAt = null, $comparison = null)
    {
        if (is_array($createdAt)) {
            $useMinMax = false;
            if (isset($createdAt['min'])) {
                $this->addUsingAlias(CreditNoteVersionTableMap::CREATED_AT, $createdAt['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($createdAt['max'])) {
                $this->addUsingAlias(CreditNoteVersionTableMap::CREATED_AT, $createdAt['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CreditNoteVersionTableMap::CREATED_AT, $createdAt, $comparison);
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
     * @return ChildCreditNoteVersionQuery The current query, for fluid interface
     */
    public function filterByUpdatedAt($updatedAt = null, $comparison = null)
    {
        if (is_array($updatedAt)) {
            $useMinMax = false;
            if (isset($updatedAt['min'])) {
                $this->addUsingAlias(CreditNoteVersionTableMap::UPDATED_AT, $updatedAt['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($updatedAt['max'])) {
                $this->addUsingAlias(CreditNoteVersionTableMap::UPDATED_AT, $updatedAt['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CreditNoteVersionTableMap::UPDATED_AT, $updatedAt, $comparison);
    }

    /**
     * Filter the query on the version column
     *
     * Example usage:
     * <code>
     * $query->filterByVersion(1234); // WHERE version = 1234
     * $query->filterByVersion(array(12, 34)); // WHERE version IN (12, 34)
     * $query->filterByVersion(array('min' => 12)); // WHERE version > 12
     * </code>
     *
     * @param     mixed $version The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildCreditNoteVersionQuery The current query, for fluid interface
     */
    public function filterByVersion($version = null, $comparison = null)
    {
        if (is_array($version)) {
            $useMinMax = false;
            if (isset($version['min'])) {
                $this->addUsingAlias(CreditNoteVersionTableMap::VERSION, $version['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($version['max'])) {
                $this->addUsingAlias(CreditNoteVersionTableMap::VERSION, $version['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CreditNoteVersionTableMap::VERSION, $version, $comparison);
    }

    /**
     * Filter the query on the version_created_at column
     *
     * Example usage:
     * <code>
     * $query->filterByVersionCreatedAt('2011-03-14'); // WHERE version_created_at = '2011-03-14'
     * $query->filterByVersionCreatedAt('now'); // WHERE version_created_at = '2011-03-14'
     * $query->filterByVersionCreatedAt(array('max' => 'yesterday')); // WHERE version_created_at > '2011-03-13'
     * </code>
     *
     * @param     mixed $versionCreatedAt The value to use as filter.
     *              Values can be integers (unix timestamps), DateTime objects, or strings.
     *              Empty strings are treated as NULL.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildCreditNoteVersionQuery The current query, for fluid interface
     */
    public function filterByVersionCreatedAt($versionCreatedAt = null, $comparison = null)
    {
        if (is_array($versionCreatedAt)) {
            $useMinMax = false;
            if (isset($versionCreatedAt['min'])) {
                $this->addUsingAlias(CreditNoteVersionTableMap::VERSION_CREATED_AT, $versionCreatedAt['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($versionCreatedAt['max'])) {
                $this->addUsingAlias(CreditNoteVersionTableMap::VERSION_CREATED_AT, $versionCreatedAt['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CreditNoteVersionTableMap::VERSION_CREATED_AT, $versionCreatedAt, $comparison);
    }

    /**
     * Filter the query on the version_created_by column
     *
     * Example usage:
     * <code>
     * $query->filterByVersionCreatedBy('fooValue');   // WHERE version_created_by = 'fooValue'
     * $query->filterByVersionCreatedBy('%fooValue%'); // WHERE version_created_by LIKE '%fooValue%'
     * </code>
     *
     * @param     string $versionCreatedBy The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildCreditNoteVersionQuery The current query, for fluid interface
     */
    public function filterByVersionCreatedBy($versionCreatedBy = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($versionCreatedBy)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $versionCreatedBy)) {
                $versionCreatedBy = str_replace('*', '%', $versionCreatedBy);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(CreditNoteVersionTableMap::VERSION_CREATED_BY, $versionCreatedBy, $comparison);
    }

    /**
     * Filter the query on the order_id_version column
     *
     * Example usage:
     * <code>
     * $query->filterByOrderIdVersion(1234); // WHERE order_id_version = 1234
     * $query->filterByOrderIdVersion(array(12, 34)); // WHERE order_id_version IN (12, 34)
     * $query->filterByOrderIdVersion(array('min' => 12)); // WHERE order_id_version > 12
     * </code>
     *
     * @param     mixed $orderIdVersion The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildCreditNoteVersionQuery The current query, for fluid interface
     */
    public function filterByOrderIdVersion($orderIdVersion = null, $comparison = null)
    {
        if (is_array($orderIdVersion)) {
            $useMinMax = false;
            if (isset($orderIdVersion['min'])) {
                $this->addUsingAlias(CreditNoteVersionTableMap::ORDER_ID_VERSION, $orderIdVersion['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($orderIdVersion['max'])) {
                $this->addUsingAlias(CreditNoteVersionTableMap::ORDER_ID_VERSION, $orderIdVersion['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CreditNoteVersionTableMap::ORDER_ID_VERSION, $orderIdVersion, $comparison);
    }

    /**
     * Filter the query on the customer_id_version column
     *
     * Example usage:
     * <code>
     * $query->filterByCustomerIdVersion(1234); // WHERE customer_id_version = 1234
     * $query->filterByCustomerIdVersion(array(12, 34)); // WHERE customer_id_version IN (12, 34)
     * $query->filterByCustomerIdVersion(array('min' => 12)); // WHERE customer_id_version > 12
     * </code>
     *
     * @param     mixed $customerIdVersion The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildCreditNoteVersionQuery The current query, for fluid interface
     */
    public function filterByCustomerIdVersion($customerIdVersion = null, $comparison = null)
    {
        if (is_array($customerIdVersion)) {
            $useMinMax = false;
            if (isset($customerIdVersion['min'])) {
                $this->addUsingAlias(CreditNoteVersionTableMap::CUSTOMER_ID_VERSION, $customerIdVersion['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($customerIdVersion['max'])) {
                $this->addUsingAlias(CreditNoteVersionTableMap::CUSTOMER_ID_VERSION, $customerIdVersion['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CreditNoteVersionTableMap::CUSTOMER_ID_VERSION, $customerIdVersion, $comparison);
    }

    /**
     * Filter the query on the parent_id_version column
     *
     * Example usage:
     * <code>
     * $query->filterByParentIdVersion(1234); // WHERE parent_id_version = 1234
     * $query->filterByParentIdVersion(array(12, 34)); // WHERE parent_id_version IN (12, 34)
     * $query->filterByParentIdVersion(array('min' => 12)); // WHERE parent_id_version > 12
     * </code>
     *
     * @param     mixed $parentIdVersion The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildCreditNoteVersionQuery The current query, for fluid interface
     */
    public function filterByParentIdVersion($parentIdVersion = null, $comparison = null)
    {
        if (is_array($parentIdVersion)) {
            $useMinMax = false;
            if (isset($parentIdVersion['min'])) {
                $this->addUsingAlias(CreditNoteVersionTableMap::PARENT_ID_VERSION, $parentIdVersion['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($parentIdVersion['max'])) {
                $this->addUsingAlias(CreditNoteVersionTableMap::PARENT_ID_VERSION, $parentIdVersion['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CreditNoteVersionTableMap::PARENT_ID_VERSION, $parentIdVersion, $comparison);
    }

    /**
     * Filter the query on the credit_note_ids column
     *
     * @param     array $creditNoteIds The values to use as filter.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildCreditNoteVersionQuery The current query, for fluid interface
     */
    public function filterByCreditNoteIds($creditNoteIds = null, $comparison = null)
    {
        $key = $this->getAliasedColName(CreditNoteVersionTableMap::CREDIT_NOTE_IDS);
        if (null === $comparison || $comparison == Criteria::CONTAINS_ALL) {
            foreach ($creditNoteIds as $value) {
                $value = '%| ' . $value . ' |%';
                if ($this->containsKey($key)) {
                    $this->addAnd($key, $value, Criteria::LIKE);
                } else {
                    $this->add($key, $value, Criteria::LIKE);
                }
            }

            return $this;
        } elseif ($comparison == Criteria::CONTAINS_SOME) {
            foreach ($creditNoteIds as $value) {
                $value = '%| ' . $value . ' |%';
                if ($this->containsKey($key)) {
                    $this->addOr($key, $value, Criteria::LIKE);
                } else {
                    $this->add($key, $value, Criteria::LIKE);
                }
            }

            return $this;
        } elseif ($comparison == Criteria::CONTAINS_NONE) {
            foreach ($creditNoteIds as $value) {
                $value = '%| ' . $value . ' |%';
                if ($this->containsKey($key)) {
                    $this->addAnd($key, $value, Criteria::NOT_LIKE);
                } else {
                    $this->add($key, $value, Criteria::NOT_LIKE);
                }
            }
            $this->addOr($key, null, Criteria::ISNULL);

            return $this;
        }

        return $this->addUsingAlias(CreditNoteVersionTableMap::CREDIT_NOTE_IDS, $creditNoteIds, $comparison);
    }

    /**
     * Filter the query on the credit_note_ids column
     * @param     mixed $creditNoteIds The value to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::CONTAINS_ALL
     *
     * @return ChildCreditNoteVersionQuery The current query, for fluid interface
     */
    public function filterByCreditNoteId($creditNoteIds = null, $comparison = null)
    {
        if (null === $comparison || $comparison == Criteria::CONTAINS_ALL) {
            if (is_scalar($creditNoteIds)) {
                $creditNoteIds = '%| ' . $creditNoteIds . ' |%';
                $comparison = Criteria::LIKE;
            }
        } elseif ($comparison == Criteria::CONTAINS_NONE) {
            $creditNoteIds = '%| ' . $creditNoteIds . ' |%';
            $comparison = Criteria::NOT_LIKE;
            $key = $this->getAliasedColName(CreditNoteVersionTableMap::CREDIT_NOTE_IDS);
            if ($this->containsKey($key)) {
                $this->addAnd($key, $creditNoteIds, $comparison);
            } else {
                $this->addAnd($key, $creditNoteIds, $comparison);
            }
            $this->addOr($key, null, Criteria::ISNULL);

            return $this;
        }

        return $this->addUsingAlias(CreditNoteVersionTableMap::CREDIT_NOTE_IDS, $creditNoteIds, $comparison);
    }

    /**
     * Filter the query on the credit_note_versions column
     *
     * @param     array $creditNoteVersions The values to use as filter.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildCreditNoteVersionQuery The current query, for fluid interface
     */
    public function filterByCreditNoteVersions($creditNoteVersions = null, $comparison = null)
    {
        $key = $this->getAliasedColName(CreditNoteVersionTableMap::CREDIT_NOTE_VERSIONS);
        if (null === $comparison || $comparison == Criteria::CONTAINS_ALL) {
            foreach ($creditNoteVersions as $value) {
                $value = '%| ' . $value . ' |%';
                if ($this->containsKey($key)) {
                    $this->addAnd($key, $value, Criteria::LIKE);
                } else {
                    $this->add($key, $value, Criteria::LIKE);
                }
            }

            return $this;
        } elseif ($comparison == Criteria::CONTAINS_SOME) {
            foreach ($creditNoteVersions as $value) {
                $value = '%| ' . $value . ' |%';
                if ($this->containsKey($key)) {
                    $this->addOr($key, $value, Criteria::LIKE);
                } else {
                    $this->add($key, $value, Criteria::LIKE);
                }
            }

            return $this;
        } elseif ($comparison == Criteria::CONTAINS_NONE) {
            foreach ($creditNoteVersions as $value) {
                $value = '%| ' . $value . ' |%';
                if ($this->containsKey($key)) {
                    $this->addAnd($key, $value, Criteria::NOT_LIKE);
                } else {
                    $this->add($key, $value, Criteria::NOT_LIKE);
                }
            }
            $this->addOr($key, null, Criteria::ISNULL);

            return $this;
        }

        return $this->addUsingAlias(CreditNoteVersionTableMap::CREDIT_NOTE_VERSIONS, $creditNoteVersions, $comparison);
    }

    /**
     * Filter the query on the credit_note_versions column
     * @param     mixed $creditNoteVersions The value to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::CONTAINS_ALL
     *
     * @return ChildCreditNoteVersionQuery The current query, for fluid interface
     */
    public function filterByCreditNoteVersion($creditNoteVersions = null, $comparison = null)
    {
        if (null === $comparison || $comparison == Criteria::CONTAINS_ALL) {
            if (is_scalar($creditNoteVersions)) {
                $creditNoteVersions = '%| ' . $creditNoteVersions . ' |%';
                $comparison = Criteria::LIKE;
            }
        } elseif ($comparison == Criteria::CONTAINS_NONE) {
            $creditNoteVersions = '%| ' . $creditNoteVersions . ' |%';
            $comparison = Criteria::NOT_LIKE;
            $key = $this->getAliasedColName(CreditNoteVersionTableMap::CREDIT_NOTE_VERSIONS);
            if ($this->containsKey($key)) {
                $this->addAnd($key, $creditNoteVersions, $comparison);
            } else {
                $this->addAnd($key, $creditNoteVersions, $comparison);
            }
            $this->addOr($key, null, Criteria::ISNULL);

            return $this;
        }

        return $this->addUsingAlias(CreditNoteVersionTableMap::CREDIT_NOTE_VERSIONS, $creditNoteVersions, $comparison);
    }

    /**
     * Filter the query by a related \CreditNote\Model\CreditNote object
     *
     * @param \CreditNote\Model\CreditNote|ObjectCollection $creditNote The related object(s) to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildCreditNoteVersionQuery The current query, for fluid interface
     */
    public function filterByCreditNote($creditNote, $comparison = null)
    {
        if ($creditNote instanceof \CreditNote\Model\CreditNote) {
            return $this
                ->addUsingAlias(CreditNoteVersionTableMap::ID, $creditNote->getId(), $comparison);
        } elseif ($creditNote instanceof ObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(CreditNoteVersionTableMap::ID, $creditNote->toKeyValue('PrimaryKey', 'Id'), $comparison);
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
     * @return ChildCreditNoteVersionQuery The current query, for fluid interface
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
     * Exclude object from result
     *
     * @param   ChildCreditNoteVersion $creditNoteVersion Object to remove from the list of results
     *
     * @return ChildCreditNoteVersionQuery The current query, for fluid interface
     */
    public function prune($creditNoteVersion = null)
    {
        if ($creditNoteVersion) {
            $this->addCond('pruneCond0', $this->getAliasedColName(CreditNoteVersionTableMap::ID), $creditNoteVersion->getId(), Criteria::NOT_EQUAL);
            $this->addCond('pruneCond1', $this->getAliasedColName(CreditNoteVersionTableMap::VERSION), $creditNoteVersion->getVersion(), Criteria::NOT_EQUAL);
            $this->combine(array('pruneCond0', 'pruneCond1'), Criteria::LOGICAL_OR);
        }

        return $this;
    }

    /**
     * Deletes all rows from the credit_note_version table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public function doDeleteAll(ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(CreditNoteVersionTableMap::DATABASE_NAME);
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
            CreditNoteVersionTableMap::clearInstancePool();
            CreditNoteVersionTableMap::clearRelatedInstancePool();

            $con->commit();
        } catch (PropelException $e) {
            $con->rollBack();
            throw $e;
        }

        return $affectedRows;
    }

    /**
     * Performs a DELETE on the database, given a ChildCreditNoteVersion or Criteria object OR a primary key value.
     *
     * @param mixed               $values Criteria or ChildCreditNoteVersion object or primary key or array of primary keys
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
            $con = Propel::getServiceContainer()->getWriteConnection(CreditNoteVersionTableMap::DATABASE_NAME);
        }

        $criteria = $this;

        // Set the correct dbName
        $criteria->setDbName(CreditNoteVersionTableMap::DATABASE_NAME);

        $affectedRows = 0; // initialize var to track total num of affected rows

        try {
            // use transaction because $criteria could contain info
            // for more than one table or we could emulating ON DELETE CASCADE, etc.
            $con->beginTransaction();


        CreditNoteVersionTableMap::removeInstanceFromPool($criteria);

            $affectedRows += ModelCriteria::delete($con);
            CreditNoteVersionTableMap::clearRelatedInstancePool();
            $con->commit();

            return $affectedRows;
        } catch (PropelException $e) {
            $con->rollBack();
            throw $e;
        }
    }

} // CreditNoteVersionQuery
