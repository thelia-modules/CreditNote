<?php
/*************************************************************************************/
/*      This file is part of the module CreditNote                                   */
/*                                                                                   */
/*      For the full copyright and license information, please view the LICENSE.txt  */
/*      file that was distributed with this source code.                             */
/*************************************************************************************/

namespace CreditNote\Loop;

use CreditNote\Model\CreditNoteDetailQuery;
use Propel\Runtime\ActiveQuery\Criteria;
use Thelia\Core\Template\Element\BaseI18nLoop;
use Thelia\Core\Template\Element\LoopResult;
use Thelia\Core\Template\Element\LoopResultRow;
use Thelia\Core\Template\Element\PropelSearchLoopInterface;
use Thelia\Core\Template\Loop\Argument\Argument;
use Thelia\Core\Template\Loop\Argument\ArgumentCollection;

/**
 * @author Gilles Bourgeat <gilles.bourgeat@gmail.com>
 *
 * @method int[] getId()
 * @method int[] getCreditNoteId()
 * @method string[] getOrder()
 */
class CreditNoteDetail extends BaseI18nLoop implements PropelSearchLoopInterface
{
    protected $timestampable = true;

    protected function getArgDefinitions()
    {
        return new ArgumentCollection(
            Argument::createIntListTypeArgument("id"),
            Argument::createIntListTypeArgument("credit_note_id"),
            Argument::createEnumListTypeArgument(
                "order",
                [
                    "id", "id-reverse"
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
        $query = new CreditNoteDetailQuery();

        if (null !== $id = $this->getId()) {
            $query->filterById($id);
        }

        if (null !== $creditNoteId = $this->getCreditNoteId()) {
            $query->filterByCreditNoteId($creditNoteId);
        }

        $this->buildModelCriteriaOrder($query);

        return $query;
    }

    /**
     * @param CreditNoteDetailQuery $query
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
            }
        }
    }

    /**
     * @param LoopResult $loopResult
     *
     * @return LoopResult
     */
    public function parseResults(LoopResult $loopResult)
    {
        /** @var \CreditNote\Model\CreditNoteDetail $entry */
        foreach ($loopResult->getResultDataCollection() as $entry) {
            $row = new LoopResultRow($entry);
            $row
                ->set("ID", $entry->getId())
                ->set("CREDIT_NOTE_ID", $entry->getCreditNoteId())
                ->set("PRICE", $entry->getPrice())
                ->set("PRICE_WITH_TAX", $entry->getPriceWithTax())
                ->set("TAX_RULE_ID", $entry->getTaxRuleId())
                ->set("ORDER_PRODUCT_ID", $entry->getOrderProductId())
                ->set("TYPE", $entry->getType())
                ->set("QUANTITY", $entry->getQuantity())
                ->set("TITLE", $entry->getTitle())
            ;
            $this->addOutputFields($row, $entry);
            $loopResult->addRow($row);
        }

        return $loopResult;
    }
}
