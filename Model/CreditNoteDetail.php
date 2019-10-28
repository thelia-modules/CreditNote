<?php

namespace CreditNote\Model;

use CreditNote\Model\Base\CreditNoteDetail as BaseCreditNoteDetail;
use Propel\Runtime\Exception\PropelException;
use Thelia\Model\OrderProduct;

class CreditNoteDetail extends BaseCreditNoteDetail
{
    use \CreditNote\Model\Tools\ModelEventDispatcherTrait;

    /**
     * Declares an association between this object and a ChildOrderProduct object.
     *
     * @param OrderProduct $v
     * @return \CreditNote\Model\CreditNoteDetail The current object (for fluent API support)
     * @throws PropelException
     */
    public function setOrderProduct(OrderProduct $v = null)
    {
        if ($v === null) {
            $this->setOrderProductId(null);
        } else {
            $this->setOrderProductId($v->getId());
        }

        $this->aOrderProduct = $v;

        // Add binding for other direction of this n:n relationship.
        // If this object has already been added to the ChildCustomer object, it will not be re-added.
        if (method_exists($v, 'addCreditNote') && $v !== null) {
            $v->addCreditNote($this);
        }

        return $this;
    }
}
