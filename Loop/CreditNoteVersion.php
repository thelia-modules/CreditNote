<?php
/*************************************************************************************/
/*      This file is part of the module CreditNote                                   */
/*                                                                                   */
/*      For the full copyright and license information, please view the LICENSE.txt  */
/*      file that was distributed with this source code.                             */
/*************************************************************************************/

namespace CreditNote\Loop;

use CreditNote\Model\CreditNoteVersionQuery;
use Propel\Runtime\ActiveQuery\Criteria;
use Thelia\Core\Template\Element\BaseLoop;
use Thelia\Core\Template\Element\LoopResult;
use Thelia\Core\Template\Element\LoopResultRow;
use Thelia\Core\Template\Element\PropelSearchLoopInterface;
use Thelia\Core\Template\Loop\Argument\Argument;
use Thelia\Core\Template\Loop\Argument\ArgumentCollection;

/**
 * @author Gilles Bourgeat <gilles.bourgeat@gmail.com>
 *
 * @method string[] getRef()
 * @method int[] getId()
 * @method string[] getOrder()
 */
class CreditNoteVersion extends BaseLoop implements PropelSearchLoopInterface
{
    protected $timestampable = true;

    protected function getArgDefinitions()
    {
        return new ArgumentCollection(
            Argument::createAnyListTypeArgument('ref'),
            Argument::createIntListTypeArgument("id"),
            Argument::createEnumListTypeArgument(
                "order",
                [
                    "id", "id-reverse",
                    "create-date", "create-date-reverse"
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
        $query = new CreditNoteVersionQuery();

        if (null !== $id = $this->getId()) {
            $query->filterById($id);
        }

        if (null !== $ref = $this->getRef()) {
            $query->filterByRef($ref);
        }

        $this->addJoin($query);

        $this->addVirtualColumn($query);

        $this->buildModelCriteriaOrder($query);

        $query->groupById();

        return $query;
    }

    /**
     * @param CreditNoteVersionQuery $query
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
            }
        }
    }

    /**
     * @param CreditNoteVersionQuery $query
     */
    protected function addJoin($query)
    {

    }

    /**
     * @param CreditNoteVersionQuery $query
     */
    protected function addVirtualColumn($query)
    {

    }

    /**
     * @param LoopResult $loopResult
     *
     * @return LoopResult
     */
    public function parseResults(LoopResult $loopResult)
    {
        /** @var \CreditNote\Model\CreditNoteVersion $entry */
        foreach ($loopResult->getResultDataCollection() as $entry) {
            $row = new LoopResultRow($entry);
            $row
                ->set("ID", $entry->getId())
                ->set("REF", $entry->getRef())
                ->set("TYPE_ID", $entry->getTypeId())
                ->set("STATUS_ID", $entry->getStatusId())
                ->set("ORDER_ID", $entry->getOrderId())
                ->set("PARENT_ID", $entry->getParentId())
                ->set("CUSTOMER_ID", $entry->getCustomerId())
                ->set("TOTAL_AMOUNT_PRICE", $entry->getTotalPriceWithTax())
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
        return $this->request->getSession()->getAdminEditionLang()->getLocale();
    }
}
