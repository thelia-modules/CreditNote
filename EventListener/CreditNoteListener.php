<?php
/*************************************************************************************/
/*      This file is part of the module CreditNote                                   */
/*                                                                                   */
/*      For the full copyright and license information, please view the LICENSE.txt  */
/*      file that was distributed with this source code.                             */
/*************************************************************************************/

namespace CreditNote\EventListener;

use CreditNote\CreditNote;
use CreditNote\Event\CreditNoteEvents;
use CreditNote\Event\PropelEvent;
use CreditNote\Model\CreditNote as CreditNoteModel;
use CreditNote\Model\Map\CreditNoteTableMap;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Thelia\Model\ConfigQuery;

/**
 * @author Gilles Bourgeat <gilles.bourgeat@gmail.com>
 */
class CreditNoteListener implements EventSubscriberInterface
{
    /**
     * Add credit note ref before save
     * @param PropelEvent $event
     */
    public function generateCreditNoteInvoiceRef(PropelEvent $event)
    {
        /** @var CreditNoteModel $instance */
        $instance = $event->getInstance();

        if ($instance->isColumnModified(CreditNoteTableMap::STATUS_ID)) {
            if ($instance->getInvoiceRef() !== null && !$instance->getCreditNoteStatus()->getInvoiced()) {
                throw new \Exception('This credit note is already invoiced, you can not cancel it');
            }

            if ($instance->getCreditNoteStatus()->getInvoiced() && $instance->getInvoiceRef() === null) {
                if ((int) CreditNote::getConfigValue(CreditNote::CONFIG_KEY_INVOICE_REF_WITH_THELIA_ORDER)) {
                    if (!class_exists('\InvoiceRef\EventListeners\OrderListener')) {
                        throw new \Exception('Missing module InvoiceRef');
                    }

                    // dans le cas ou la facturation suit celle des commandes
                    $invoiceRef = ConfigQuery::create()
                        ->findOneByName('invoiceRef');

                    $value = $invoiceRef->getValue();

                    $instance->setInvoiceRef($value)
                        ->setInvoiceDate(new \DateTime())
                    ;

                    $invoiceRef->setValue(++$value)
                        ->save();
                } else {
                    // cas ou la facturation suit sa propre règle
                    $ref = CreditNote::getConfigValue(CreditNote::CONFIG_KEY_INVOICE_REF_PREFIX) . str_pad(
                            (int) CreditNote::getConfigValue(CreditNote::CONFIG_KEY_INVOICE_REF_INCREMENT, 1) + 1,
                            CreditNote::getConfigValue(CreditNote::CONFIG_KEY_INVOICE_REF_MIN_LENGTH, 8),
                            "0",
                            STR_PAD_LEFT
                        );

                    $instance->setInvoiceRef($ref);
                    $instance->setInvoiceDate(new \DateTime());

                    CreditNote::setConfigValue(
                        CreditNote::CONFIG_KEY_INVOICE_REF_INCREMENT,
                        (int) CreditNote::getConfigValue(CreditNote::CONFIG_KEY_INVOICE_REF_INCREMENT, 1) + 1
                    );
                }
            }
        }
    }

    /**
     * Add credit note ref before save
     * @param PropelEvent $event
     */
    public function generateCreditNoteRef(PropelEvent $event)
    {
        /** @var CreditNoteModel $instance */
        $instance = $event->getInstance();

        if ($instance->getRef() === null) {
            $ref = CreditNote::getConfigValue(CreditNote::CONFIG_KEY_REF_PREFIX) . str_pad(
                (int) CreditNote::getConfigValue(CreditNote::CONFIG_KEY_REF_INCREMENT, 1) + 1,
                CreditNote::getConfigValue(CreditNote::CONFIG_KEY_REF_MIN_LENGTH, 8),
                "0",
                STR_PAD_LEFT
            );

            $instance->setRef($ref);
            $instance->setInvoiceDate(new \DateTime());
        }

        $this->generateCreditNoteInvoiceRef($event);
    }

    public function incrementCreditNoteRef(PropelEvent $event)
    {
        CreditNote::setConfigValue(
            CreditNote::CONFIG_KEY_REF_INCREMENT,
            (int) CreditNote::getConfigValue(CreditNote::CONFIG_KEY_REF_INCREMENT, 1) + 1
        );
    }

    public static function getSubscribedEvents()
    {
        return array(
            CreditNoteEvents::preInsert(CreditNoteTableMap::TABLE_NAME)  => [
                'generateCreditNoteRef', 128
            ],
            CreditNoteEvents::postInsert(CreditNoteTableMap::TABLE_NAME)  => [
                'incrementCreditNoteRef', 128
            ],
            CreditNoteEvents::preUpdate(CreditNoteTableMap::TABLE_NAME)  => [
                'generateCreditNoteInvoiceRef', 128
            ]
        );
    }
}
