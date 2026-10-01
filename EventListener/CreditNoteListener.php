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
use CreditNote\Service\CreditNoteOrderCeiling;
use CreditNote\Service\SharedInvoiceNumber;
use Propel\Runtime\Propel;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * @author Gilles Bourgeat <gilles.bourgeat@gmail.com>
 */
class CreditNoteListener implements EventSubscriberInterface
{
    public function __construct(
        private readonly CreditNoteOrderCeiling $orderCeiling,
        private readonly SharedInvoiceNumber $sharedInvoiceNumber,
    ) {
    }

    /**
     * Before an update: the ceiling of the order, then the accounting number when the
     * credit note gets accepted.
     */
    public function generateCreditNoteInvoiceRef(PropelEvent $event)
    {
        /** @var CreditNoteModel $instance */
        $instance = $event->getInstance();

        $this->enforceOrderCeiling($instance);
        $this->assignInvoiceRef($instance);
    }

    /**
     * Before an insert: the ceiling of the order, then the reference, then the accounting
     * number when the credit note is created accepted.
     */
    public function generateCreditNoteRef(PropelEvent $event)
    {
        /** @var CreditNoteModel $instance */
        $instance = $event->getInstance();

        $this->enforceOrderCeiling($instance);

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

        $this->assignInvoiceRef($instance);
    }

    public function incrementCreditNoteRef(PropelEvent $event)
    {
        CreditNote::setConfigValue(
            CreditNote::CONFIG_KEY_REF_INCREMENT,
            (int) CreditNote::getConfigValue(CreditNote::CONFIG_KEY_REF_INCREMENT, 1) + 1
        );
    }

    /**
     * Whatever writes a credit note (back-office, API, a command) goes through here: the
     * credit notes of an order never refund more than the order was worth. Checked before a
     * number is drawn, so a refused credit note leaves the numbering alone.
     */
    private function enforceOrderCeiling(CreditNoteModel $instance): void
    {
        if (
            $instance->isNew()
            || $instance->isColumnModified(CreditNoteTableMap::COL_TOTAL_PRICE_WITH_TAX)
            || $instance->isColumnModified(CreditNoteTableMap::COL_ORDER_ID)
            || $instance->isColumnModified(CreditNoteTableMap::COL_STATUS_ID)
        ) {
            $this->orderCeiling->assertWithinOrder($instance);
        }
    }

    private function assignInvoiceRef(CreditNoteModel $instance): void
    {
        if (!$instance->isColumnModified(CreditNoteTableMap::COL_STATUS_ID)) {
            return;
        }

        if ($instance->getInvoiceRef() !== null && !$instance->getCreditNoteStatus()->getInvoiced()) {
            throw new \Exception('This credit note is already invoiced, you can not cancel it');
        }

        if (!$instance->getCreditNoteStatus()->getInvoiced() || $instance->getInvoiceRef() !== null) {
            return;
        }

        if ((int) CreditNote::getConfigValue(CreditNote::CONFIG_KEY_INVOICE_REF_WITH_THELIA_ORDER)) {
            // The invoicing follows the one of the orders: same series, same lock.
            $instance->setInvoiceRef($this->sharedInvoiceNumber->next(Propel::getConnection(CreditNoteTableMap::DATABASE_NAME)))
                ->setInvoiceDate(new \DateTime())
            ;

            return;
        }

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
