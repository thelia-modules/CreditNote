<?php
/*************************************************************************************/
/*      This file is part of the module CreditNote                                   */
/*                                                                                   */
/*      For the full copyright and license information, please view the LICENSE.txt  */
/*      file that was distributed with this source code.                             */
/*************************************************************************************/

namespace CreditNote\Model\Tools;

use CreditNote\Event\PropelEvent;
use Propel\Runtime\ActiveRecord\ActiveRecordInterface;
use Propel\Runtime\Connection\ConnectionInterface;
use Thelia\Model\Tools\ModelEventDispatcherTrait as TheliaModelEventDispatcherTrait;
use CreditNote\Event\CreditNoteEvents;

/**
 * @author Gilles Bourgeat >gilles.bourgeat@gmail.com>
 */
trait ModelEventDispatcherTrait
{
    use TheliaModelEventDispatcherTrait;

    protected function getTableName()
    {
        $tableMapClass = self::TABLE_MAP;
        return $tableMapClass::TABLE_NAME;
    }

    /**
     * Code to be run before inserting to database
     * @param  ConnectionInterface $con
     * @return boolean
     */
    public function preInsert(ConnectionInterface $con = null)
    {
        parent::preInsert($con);

        if ($this->getDispatcher() !== null) {
            /** @var ActiveRecordInterface $this */
            $event = new PropelEvent($this);
            /** @var ModelEventDispatcherTrait $this */
            $this->getDispatcher()->dispatch(CreditNoteEvents::PROPEL_PRE_INSERT . $this->getTableName(), $event);

            if ($event->isPropagationStopped()) {
                return false;
            }
        }
        return true;
    }

    /**
     * Code to be run after inserting to database
     * @param ConnectionInterface $con
     */
    public function postInsert(ConnectionInterface $con = null)
    {
        parent::postInsert($con);

        if ($this->getDispatcher() !== null) {
            /** @var ActiveRecordInterface $this */
            $event = new PropelEvent($this);
            /** @var ModelEventDispatcherTrait $this */
            $this->getDispatcher()->dispatch(CreditNoteEvents::PROPEL_POST_INSERT . $this->getTableName(), $event);
        }
    }

    /**
     * Code to be run before updating the object in database
     * @param  ConnectionInterface $con
     * @return boolean
     */
    public function preUpdate(ConnectionInterface $con = null)
    {
        parent::preUpdate($con);

        if ($this->getDispatcher() !== null) {
            /** @var ActiveRecordInterface $this */
            $event = new PropelEvent($this);
            /** @var ModelEventDispatcherTrait $this */
            $this->getDispatcher()->dispatch(CreditNoteEvents::PROPEL_PRE_UPDATE . $this->getTableName(), $event);

            if ($event->isPropagationStopped()) {
                return false;
            }
        }
        return true;
    }

    /**
     * Code to be run after updating the object in database
     * @param ConnectionInterface $con
     */
    public function postUpdate(ConnectionInterface $con = null)
    {
        parent::postUpdate($con);

        if ($this->getDispatcher() !== null) {
            /** @var ActiveRecordInterface $this */
            $event = new PropelEvent($this);
            /** @var ModelEventDispatcherTrait $this */
            $this->getDispatcher()->dispatch(CreditNoteEvents::PROPEL_POST_UPDATE . $this->getTableName(), $event);
        }
    }

    /**
     * Code to be run before deleting the object in database
     * @param  ConnectionInterface $con
     * @return boolean
     */
    public function preDelete(ConnectionInterface $con = null)
    {
        parent::preDelete($con);

        if ($this->getDispatcher() !== null) {
            /** @var ActiveRecordInterface $this */
            $event = new PropelEvent($this);
            /** @var ModelEventDispatcherTrait $this */
            $this->getDispatcher()->dispatch(CreditNoteEvents::PROPEL_PRE_DELETE . $this->getTableName(), $event);

            if ($event->isPropagationStopped()) {
                return false;
            }
        }
        return true;
    }

    /**
     * Code to be run after deleting the object in database
     * @param ConnectionInterface $con
     */
    public function postDelete(ConnectionInterface $con = null)
    {
        parent::postDelete($con);

        if ($this->getDispatcher() !== null) {
            /** @var ActiveRecordInterface $this */
            $event = new PropelEvent($this);
            /** @var ModelEventDispatcherTrait $this */
            $this->getDispatcher()->dispatch(CreditNoteEvents::PROPEL_POST_DELETE . $this->getTableName(), $event);
        }
    }
}
