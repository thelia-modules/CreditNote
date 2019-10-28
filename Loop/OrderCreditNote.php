<?php
/*************************************************************************************/
/*      This file is part of the module CreditNote                                   */
/*                                                                                   */
/*      For the full copyright and license information, please view the LICENSE.txt  */
/*      file that was distributed with this source code.                             */
/*************************************************************************************/

namespace CreditNote\Loop;

use CreditNote\Model\OrderCreditNoteQuery;
use Thelia\Core\Template\Element\BaseI18nLoop;
use Thelia\Core\Template\Element\LoopResult;
use Thelia\Core\Template\Element\LoopResultRow;
use Thelia\Core\Template\Element\PropelSearchLoopInterface;
use Thelia\Core\Template\Loop\Argument\Argument;
use Thelia\Core\Template\Loop\Argument\ArgumentCollection;

/**
 * @author Gilles Bourgeat <gilles.bourgeat@gmail.com>
 *
 * @method int[] getCreditNoteId()
 * @method int[] getOrderId()
 */
class OrderCreditNote extends BaseI18nLoop implements PropelSearchLoopInterface
{
    protected $timestampable = true;

    protected function getArgDefinitions()
    {
        return new ArgumentCollection(
            Argument::createIntListTypeArgument("credit_note_id"),
            Argument::createIntListTypeArgument("order_id")
        );
    }

    /**
     * this method returns a Propel ModelCriteria
     *
     * @return \Propel\Runtime\ActiveQuery\ModelCriteria
     */
    public function buildModelCriteria()
    {
        $query = new OrderCreditNoteQuery();

        if (null !== $creditNoteId = $this->getCreditNoteId()) {
            $query->filterByCreditNoteId($creditNoteId);
        }

        if (null !== $orderId = $this->getOrderId()) {
            $query->filterByOrderId($orderId);
        }

        return $query;
    }

    /**
     * @param LoopResult $loopResult
     *
     * @return LoopResult
     */
    public function parseResults(LoopResult $loopResult)
    {
        /** @var \CreditNote\Model\OrderCreditNote $entry */
        foreach ($loopResult->getResultDataCollection() as $entry) {
            $row = new LoopResultRow($entry);
            $row
                ->set("ORDER_ID", $entry->getOrderId())
                ->set("CREDIT_NOTE_ID", $entry->getCreditNoteId())
                ->set("AMOUNT", $entry->getAmountPrice())
            ;
            $this->addOutputFields($row, $entry);
            $loopResult->addRow($row);
        }

        return $loopResult;
    }
}
