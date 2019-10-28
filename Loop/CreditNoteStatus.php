<?php
/*************************************************************************************/
/*      This file is part of the module CreditNote                                   */
/*                                                                                   */
/*      For the full copyright and license information, please view the LICENSE.txt  */
/*      file that was distributed with this source code.                             */
/*************************************************************************************/

namespace CreditNote\Loop;

use CreditNote\Model\CreditNoteStatusFlowQuery;
use CreditNote\Model\CreditNoteStatusQuery;
use Propel\Runtime\ActiveQuery\Criteria;
use Thelia\Core\Template\Element\BaseI18nLoop;
use Thelia\Core\Template\Element\LoopResult;
use Thelia\Core\Template\Element\LoopResultRow;
use Thelia\Core\Template\Element\PropelSearchLoopInterface;
use Thelia\Core\Template\Loop\Argument\Argument;
use Thelia\Core\Template\Loop\Argument\ArgumentCollection;
use Thelia\Type\BooleanOrBothType;

/**
 * @author Gilles Bourgeat <gilles.bourgeat@gmail.com>
 *
 * @method string[] getCode()
 * @method int[] getId()
 * @method int[] getPosition()
 * @method int[] getCurrentStatusId()
 * @method string[] getOrder()
 * @method string|bool getInvoiced()
 * @method string|bool getUsed()
 */
class CreditNoteStatus extends BaseI18nLoop implements PropelSearchLoopInterface
{
    protected $timestampable = true;

    protected function getArgDefinitions()
    {
        return new ArgumentCollection(
            Argument::createAnyListTypeArgument('code'),
            Argument::createIntListTypeArgument("id"),
            Argument::createIntListTypeArgument("position"),
            Argument::createBooleanOrBothTypeArgument("invoiced", BooleanOrBothType::ANY),
            Argument::createBooleanOrBothTypeArgument("used", BooleanOrBothType::ANY),
            Argument::createIntListTypeArgument("current_status_id"),
            Argument::createEnumListTypeArgument(
                "order",
                [
                    "id", "id-reverse",
                    "create-date", "create-date-reverse",
                    "code", "code-reverse",
                    "position", "position-reverse"
                ],
                "position"
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
        $query = new CreditNoteStatusQuery();

        if (null !== $id = $this->getId()) {
            $query->filterById($id);
        }

        if (null !== $code = $this->getCode()) {
            $query->filterByCode($code);
        }

        if (null !== $position = $this->getPosition()) {
            $query->filterByPosition($position);
        }

        if (BooleanOrBothType::ANY !== $used = $this->getUsed()) {
            $query->filterByUsed($used);
        }

        if (BooleanOrBothType::ANY !== $invoiced = $this->getInvoiced()) {
            $query->filterByInvoiced($invoiced);
        }

        if (null !== $currentStatusId = $this->getCurrentStatusId()) {
            $flowIds = CreditNoteStatusFlowQuery::create()
                ->filterByFromStatusId($currentStatusId)
                ->find()->toArray('toStatusId');

            $flowIds = array_keys($flowIds);

            $query->filterById(array_merge($flowIds, $currentStatusId));
        }

        $this->configureI18nProcessing($query, ['TITLE', 'CHAPO', 'DESCRIPTION', 'POSTSCRIPTUM']);

        $this->buildModelCriteriaOrder($query);

        $query->groupById();

        return $query;
    }

    /**
     * @param CreditNoteStatusQuery $query
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
                case 'code':
                    $query->orderByCode(Criteria::ASC);
                    break;
                case 'code-reverse':
                    $query->orderByCode(Criteria::DESC);
                    break;
                case 'position':
                    $query->orderByPosition(Criteria::ASC);
                    break;
                case 'position-reverse':
                    $query->orderByPosition(Criteria::DESC);
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
        /** @var \CreditNote\Model\CreditNoteStatus $entry */
        foreach ($loopResult->getResultDataCollection() as $entry) {
            $row = new LoopResultRow($entry);
            $row
                ->set("ID", $entry->getId())
                ->set("CODE", $entry->getCode())
                ->set("COLOR", $entry->getColor())
                ->set("POSITION", $entry->getPosition())
                ->set("TITLE", $entry->getVirtualColumn('i18n_TITLE'))
                ->set("CHAPO", $entry->getVirtualColumn('i18n_CHAPO'))
                ->set("DESCRIPTION", $entry->getVirtualColumn('i18n_DESCRIPTION'))
                ->set("POSTSCRIPTUM", $entry->getVirtualColumn('i18n_POSTSCRIPTUM'))
            ;
            $this->addOutputFields($row, $entry);
            $loopResult->addRow($row);
        }

        return $loopResult;
    }
}
