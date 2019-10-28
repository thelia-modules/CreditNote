<?php
/*************************************************************************************/
/*      This file is part of the module CreditNote                                   */
/*                                                                                   */
/*      For the full copyright and license information, please view the LICENSE.txt  */
/*      file that was distributed with this source code.                             */
/*************************************************************************************/

namespace CreditNote\Loop;

use CreditNote\Model\CreditNoteCommentQuery;
use Propel\Runtime\ActiveQuery\Criteria;
use Thelia\Core\Template\Element\BaseI18nLoop;
use Thelia\Core\Template\Element\LoopResult;
use Thelia\Core\Template\Element\LoopResultRow;
use Thelia\Core\Template\Element\PropelSearchLoopInterface;
use Thelia\Core\Template\Loop\Argument\Argument;
use Thelia\Core\Template\Loop\Argument\ArgumentCollection;
use Thelia\Model\Map\AdminTableMap;

/**
 * @author Gilles Bourgeat <gilles.bourgeat@gmail.com>
 *
 * @method string[] getCode()
 * @method int[] getId()
 * @method int[] getCreditNoteId()
 * @method string[] getOrder()
 */
class CreditNoteComment extends BaseI18nLoop implements PropelSearchLoopInterface
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
                    "id", "id-reverse",
                    "create-date", "create-date-reverse"
                ],
                "create-date"
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
        $query = new CreditNoteCommentQuery();

        if (null !== $id = $this->getId()) {
            $query->filterById($id);
        }

        if (null !== $creditNoteId = $this->getCreditNoteId()) {
            $query->filterByCreditNoteId($creditNoteId);
        }

        $query->useAdminQuery()
            ->endUse();

        $query->withColumn(AdminTableMap::LOGIN, 'ADMIN_LOGIN');
        $query->withColumn(AdminTableMap::FIRSTNAME, 'ADMIN_FIRST_NAME');
        $query->withColumn(AdminTableMap::LASTNAME, 'ADMIN_LAST_NAME');

        $this->buildModelCriteriaOrder($query);

        return $query;
    }

    /**
     * @param CreditNoteCommentQuery $query
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
     * @param LoopResult $loopResult
     *
     * @return LoopResult
     */
    public function parseResults(LoopResult $loopResult)
    {
        /** @var \CreditNote\Model\CreditNoteComment $entry */
        foreach ($loopResult->getResultDataCollection() as $entry) {
            $row = new LoopResultRow($entry);
            $row
                ->set("ID", $entry->getId())
                ->set("CREDIT_NOTE_ID", $entry->getCreditNoteId())
                ->set("ADMIN_ID", $entry->getAdminId())
                ->set("ADMIN_LOGIN", $entry->getVirtualColumn('ADMIN_LOGIN'))
                ->set("ADMIN_FIRST_NAME", $entry->getVirtualColumn('ADMIN_FIRST_NAME'))
                ->set("ADMIN_LAST_NAME", $entry->getVirtualColumn('ADMIN_LAST_NAME'))
                ->set("COMMENT", $entry->getComment())
            ;
            $this->addOutputFields($row, $entry);
            $loopResult->addRow($row);
        }

        return $loopResult;
    }
}
