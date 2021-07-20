<?php
/*************************************************************************************/
/*      This file is part of the module CreditNote                                   */
/*                                                                                   */
/*      For the full copyright and license information, please view the LICENSE.txt  */
/*      file that was distributed with this source code.                             */
/*************************************************************************************/

namespace CreditNote\Loop;

use CreditNote\Model\CreditNoteQuery;
use CreditNote\Model\CreditNote as CreditNoteModel;
use CreditNote\Model\Map\CreditNoteStatusI18nTableMap;
use CreditNote\Model\Map\CreditNoteStatusTableMap;
use CreditNote\Model\Map\CreditNoteTypeI18nTableMap;
use CreditNote\Model\Map\CreditNoteTypeTableMap;
use Propel\Runtime\ActiveQuery\Criteria;
use Thelia\Core\Template\Element\BaseLoop;
use Thelia\Core\Template\Element\LoopResult;
use Thelia\Core\Template\Element\LoopResultRow;
use Thelia\Core\Template\Element\PropelSearchLoopInterface;
use Thelia\Core\Template\Loop\Argument\Argument;
use Thelia\Core\Template\Loop\Argument\ArgumentCollection;
use Thelia\Model\Map\CustomerTableMap;
use Thelia\Model\Map\OrderTableMap;

/**
 * @author Gilles Bourgeat <gilles.bourgeat@gmail.com>
 *
 * @method string[] getRef()
 * @method string[] getInvoiceRef()
 * @method int[] getId()
 * @method int[] getStatusId()
 * @method int[] getOrderProductId()
 * @method int[] getTypeId()
 * @method int[] getOrderId()
 * @method int[] getCustomerId()
 * @method int[] getParentId()
 * @method string[] getOrder()
 * @method boolean|string getUsed()
 * @method boolean|string getInvoiced()
 */
class CreditNote extends BaseLoop implements PropelSearchLoopInterface
{
    protected $timestampable = true;

    protected function getArgDefinitions()
    {
        return new ArgumentCollection(
            Argument::createAnyListTypeArgument('ref'),
            Argument::createAnyListTypeArgument('invoice_ref'),
            Argument::createIntListTypeArgument("id"),
            Argument::createAnyListTypeArgument("status_id"),
            Argument::createAnyListTypeArgument("order_product_id"),
            Argument::createAnyListTypeArgument("type_id"),
            Argument::createIntListTypeArgument("order_id"),
            Argument::createIntListTypeArgument("customer_id"),
            Argument::createIntListTypeArgument("parent_id"),
            Argument::createBooleanOrBothTypeArgument("used"),
            Argument::createBooleanOrBothTypeArgument("invoiced"),
            Argument::createEnumListTypeArgument(
                "order",
                [
                    "id", "id-reverse",
                    "create-date", "create-date-reverse",
                    "status", "status-reverse",
                    'update-date', 'update-date-reverse',
                ],
                "id"
            )
        );
    }

    /**
     * this method returns a Propel ModelCriteria
     *
     * @return \Propel\Runtime\ActiveQuery\ModelCriteria
     */
    public function buildModelCriteria()
    {
        $query = new CreditNoteQuery();

        if (null !== $id = $this->getId()) {
            $query->filterById($id);
        }

        if (null !== $invoiceRef = $this->getInvoiceRef()) {
            $query->filterByInvoiceRef($invoiceRef);
        }

        if (null !== $ref = $this->getRef()) {
            $query->filterByRef($ref);
        }

        if (null !== $type = $this->getTypeId()) {
            $query->FilterByTypeId($type);
        }

        if (null !== $status = $this->getStatusId()) {
            $query->filterByStatusId($status);
        }

        if (null !== $orderProduct = $this->getOrderProductId()) {
            $query->useCreditNoteDetailQuery()
                ->filterByOrderProductId($orderProduct)
            ->endUse();
        }

        if (null !== $order = $this->getOrderId()) {
            $query->filterByOrderId($order);
        }

        if (null !== $parent = $this->getParentId()) {
            $query->filterByParentId($parent);
        }

        if (null !== $customer = $this->getCustomerId()) {
            $query->filterByCustomerId($customer);
        }

        if (is_bool($this->getUsed())) {
            $query->useCreditNoteStatusQuery()
                ->filterByUsed($this->getUsed())
                ->endUse();
        }

        if (is_bool($this->getInvoiced())) {
            $query->useCreditNoteStatusQuery()
                ->filterByInvoiced($this->getInvoiced())
                ->endUse();
        }

        $this->addJoin($query);

        $this->addVirtualColumn($query);

        $this->buildModelCriteriaOrder($query);

        $query->groupById();

        return $query;
    }

    /**
     * @param CreditNoteQuery $query
     */
    protected function buildModelCriteriaOrder($query)
    {
        foreach ($this->getOrder() as $order) {
            switch ($order) {
                case "id":
                    $query->orderById();
                    break;
                case "id-reverse":
                    $query->orderById(Criteria::DESC);
                    break;
                case 'create-date':
                    $query->orderByCreatedAt(Criteria::ASC);
                    break;
                case 'create-date-reverse':
                    $query->orderByCreatedAt(Criteria::DESC);
                    break;
                case 'status':
                    $query->orderByStatusId(Criteria::ASC);
                    break;
                case 'status-reverse':
                    $query->orderByStatusId(Criteria::DESC);
                    break;
                case 'update-date':
                    $query->orderByUpdatedAt(Criteria::ASC);
                    break;
                case 'update-date-reverse':
                    $query->orderByUpdatedAt(Criteria::DESC);
                    break;
            }
        }
    }

    /**
     * @param CreditNoteQuery $query
     */
    protected function addJoin($query)
    {
        $query->useCustomerQuery()
            ->endUse();

        $query->useOrderQuery()
            ->endUse();

        $query->useCreditNoteTypeQuery()
            ->leftJoinCreditNoteTypeI18n(CreditNoteTypeI18nTableMap::TABLE_NAME)
            ->endUse();

        $query->addJoinCondition(CreditNoteTypeI18nTableMap::TABLE_NAME, CreditNoteTypeI18nTableMap::LOCALE . '=?', $this->getLocale());

        $query->useCreditNoteStatusQuery()
            ->leftJoinCreditNoteStatusI18n(CreditNoteStatusI18nTableMap::TABLE_NAME)
            ->endUse();

        $query->addJoinCondition(CreditNoteStatusI18nTableMap::TABLE_NAME, CreditNoteStatusI18nTableMap::LOCALE . '=?', $this->getLocale());
    }

    /**
     * @param CreditNoteQuery $query
     */
    protected function addVirtualColumn($query)
    {
        $query
            ->withColumn('CONCAT_WS(" ",' . CustomerTableMap::FIRSTNAME . ',' . CustomerTableMap::LASTNAME .')', 'CUSTOMER_NAME')
            ->withColumn(OrderTableMap::REF, 'ORDER_REF')
            ->withColumn(CreditNoteStatusI18nTableMap::TITLE, 'STATUS_TITLE')
            ->withColumn(CreditNoteStatusTableMap::COLOR, 'STATUS_COLOR')
            ->withColumn(CreditNoteTypeI18nTableMap::TITLE, 'TYPE_TITLE')
            ->withColumn(CreditNoteTypeTableMap::COLOR, 'TYPE_COLOR');
    }

    /**
     * @param LoopResult $loopResult
     *
     * @return LoopResult
     */
    public function parseResults(LoopResult $loopResult)
    {
        /** @var CreditNoteModel $entry */
        foreach ($loopResult->getResultDataCollection() as $entry) {
            $row = new LoopResultRow($entry);
            $row
                ->set("ID", $entry->getId())
                ->set("REF", $entry->getRef())
                ->set("INVOICE_REF", $entry->getInvoiceRef())
                ->set("INVOICE_DATE", $entry->getInvoiceDate())
                ->set("TYPE_ID", $entry->getTypeId())
                ->set("STATUS_ID", $entry->getStatusId())
                ->set("ORDER_ID", $entry->getOrderId())
                ->set("PARENT_ID", $entry->getParentId())
                ->set("CUSTOMER_ID", $entry->getCustomerId())
                ->set('CURRENCY_ID', $entry->getCurrencyId())
                ->set("TOTAL_PRICE", $entry->getTotalPrice())
                ->set("TOTAL_PRICE_WITH_TAX", $entry->getTotalPriceWithTax())

                ->set("DISCOUNT_WITHOUT_TAX", $entry->getDiscountWithoutTax())
                ->set("DISCOUNT_WITH_TAX", $entry->getDiscountWithTax())

                ->set('CUSTOMER_NAME', $entry->getVirtualColumn('CUSTOMER_NAME'))
                ->set('ORDER_REF', $entry->getVirtualColumn('ORDER_REF'))

                ->set('STATUS_TITLE', $entry->getVirtualColumn('STATUS_TITLE'))
                ->set('STATUS_COLOR', $entry->getVirtualColumn('STATUS_COLOR'))
                ->set('TYPE_TITLE', $entry->getVirtualColumn('TYPE_TITLE'))
                ->set('TYPE_COLOR', $entry->getVirtualColumn('TYPE_COLOR'))
            ;
            $this->addOutputFields($row, $entry);
            $loopResult->addRow($row);
        }

        return $loopResult;
    }

    /**
     * @return string
     */
    protected function getLocale()
    {
        if ($this->requestStack->getCurrentRequest()->fromAdmin()) {
            return $this->requestStack->getCurrentRequest()->getSession()->getAdminUser()->getLocale();
        } else {
            return $this->requestStack->getCurrentRequest()->getSession()->getLang()->getLocale();
        }
    }
}
