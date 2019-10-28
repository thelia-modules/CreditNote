<?php
/*************************************************************************************/
/*      This file is part of the module CreditNote                                   */
/*                                                                                   */
/*      For the full copyright and license information, please view the LICENSE.txt  */
/*      file that was distributed with this source code.                             */
/*************************************************************************************/

namespace CreditNote\Loop;

use CreditNote\Model\CreditNoteAddressQuery;
use Propel\Runtime\ActiveQuery\Criteria;
use Thelia\Core\Template\Element\BaseLoop;
use Thelia\Core\Template\Element\LoopResult;
use Thelia\Core\Template\Element\LoopResultRow;
use Thelia\Core\Template\Element\PropelSearchLoopInterface;
use Thelia\Core\Template\Loop\Argument\ArgumentCollection;
use Thelia\Core\Template\Loop\Argument\Argument;

/**
 * @author Gilles Bourgeat >gilles.bourgeat@gmail.com>
 */
class CreditNoteAddress extends BaseLoop implements PropelSearchLoopInterface
{
    protected $timestampable = true;

    /**
     * @return ArgumentCollection
     */
    protected function getArgDefinitions()
    {
        return new ArgumentCollection(
            Argument::createIntTypeArgument('id', null, true)
        );
    }

    public function buildModelCriteria()
    {
        $search = CreditNoteAddressQuery::create();

        $id = $this->getId();

        $search->filterById($id, Criteria::IN);

        return $search;
    }

    public function parseResults(LoopResult $loopResult)
    {
        /** @var \CreditNote\Model\CreditNoteAddress $creditNoteAddress */
        foreach ($loopResult->getResultDataCollection() as $creditNoteAddress) {
            $loopResultRow = new LoopResultRow($creditNoteAddress);
            $loopResultRow
                ->set("ID", $creditNoteAddress->getId())
                ->set("TITLE", $creditNoteAddress->getCustomerTitleId())
                ->set("COMPANY", $creditNoteAddress->getCompany())
                ->set("FIRSTNAME", $creditNoteAddress->getFirstname())
                ->set("LASTNAME", $creditNoteAddress->getLastname())
                ->set("ADDRESS1", $creditNoteAddress->getAddress1())
                ->set("ADDRESS2", $creditNoteAddress->getAddress2())
                ->set("ADDRESS3", $creditNoteAddress->getAddress3())
                ->set("ZIPCODE", $creditNoteAddress->getZipcode())
                ->set("CITY", $creditNoteAddress->getCity())
                ->set("COUNTRY", $creditNoteAddress->getCountryId())
                ->set("STATE", $creditNoteAddress->getStateId())
                ->set("PHONE", $creditNoteAddress->getPhone())
                ->set("CELLPHONE", $creditNoteAddress->getCellphone())
            ;
            $this->addOutputFields($loopResultRow, $creditNoteAddress);

            $loopResult->addRow($loopResultRow);
        }

        return $loopResult;
    }
}
