<?php
/*************************************************************************************/
/*      This file is part of the module CreditNote                                   */
/*                                                                                   */
/*      For the full copyright and license information, please view the LICENSE.txt  */
/*      file that was distributed with this source code.                             */
/*************************************************************************************/

namespace CreditNote\Model;

use CreditNote\Model\Base\CreditNote as BaseCreditNote;
use CreditNote\Model\CreditNoteStatus as ChildCreditNoteStatus;
use Propel\Runtime\Exception\PropelException;
use Thelia\Model\Currency;
use Thelia\Model\Customer;
use Thelia\Model\Order;

class CreditNote extends BaseCreditNote
{
    use \CreditNote\Model\Tools\ModelEventDispatcherTrait;

    /**
     * Declares an association between this object and a ChildOrder object.
     *
     * @param Order $v
     * @return \CreditNote\Model\CreditNote The current object (for fluent API support)
     * @throws PropelException
     */
    public function setOrder(Order $v = null)
    {
        if ($v === null) {
            $this->setOrderId(null);
        } else {
            $this->setOrderId($v->getId());
        }

        $this->aOrder = $v;

        // Add binding for other direction of this n:n relationship.
        // If this object has already been added to the ChildCustomer object, it will not be re-added.
        if (method_exists($v, 'addCreditNote') && $v !== null) {
            $v->addCreditNote($this);
        }

        return $this;
    }

    /**
     * Declares an association between this object and a ChildCurrency object.
     *
     * @param Currency $v
     * @return \CreditNote\Model\CreditNote The current object (for fluent API support)
     * @throws PropelException
     */
    public function setCurrency(Currency $v = null)
    {
        if ($v === null) {
            $this->setCurrencyId(null);
        } else {
            $this->setCurrencyId($v->getId());
        }

        $this->aCurrency = $v;

        // Add binding for other direction of this n:n relationship.
        // If this object has already been added to the ChildCustomer object, it will not be re-added.
        if (method_exists($v, 'addCreditNote') && $v !== null) {
            $v->addCreditNote($this);
        }

        return $this;
    }

    /**
     * Declares an association between this object and a ChildCustomer object.
     *
     * @param Customer $v
     * @return \CreditNote\Model\CreditNote The current object (for fluent API support)
     * @throws PropelException
     */
    public function setCustomer(Customer $v = null)
    {
        if ($v === null) {
            $this->setCustomerId(null);
        } else {
            $this->setCustomerId($v->getId());
        }

        $this->aCustomer = $v;

        // Add binding for other direction of this n:n relationship.
        // If this object has already been added to the ChildCustomer object, it will not be re-added.
        if (method_exists($v, 'addCreditNote') && $v !== null) {
            $v->addCreditNote($this);
        }

        return $this;
    }

    public function setStatusId($v)
    {
        // check status flow
        if (null !== $this->getStatusId() && (int) $v !== (int) $this->getStatusId()) {
            if (!CreditNoteStatusFlowQuery::create()
                ->filterByFromStatusId($this->getStatusId())
                ->filterByToStatusId($v)
                ->findOne()) {
                throw new \Exception('You do not respect the status flow');
            }
        }

        return parent::setStatusId($v);
    }

    public function setCreditNoteStatus(ChildCreditNoteStatus $v = null)
    {
        // check status flow
        if (null !== $v && null !== $this->getCreditNoteStatus() && (int) $v->getId() !== (int) $this->getCreditNoteStatus()->getId()) {
            if (!CreditNoteStatusFlowQuery::create()
                ->filterByFromStatusId($this->getCreditNoteStatus()->getId())
                ->filterByToStatusId($v->getId())
                ->findOne()) {
                throw new \Exception('You do not respect the status flow');
            }
        }

        return parent::setCreditNoteStatus($v);
    }
}
