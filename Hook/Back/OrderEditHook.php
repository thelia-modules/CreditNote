<?php
/*************************************************************************************/
/*      This file is part of the module CreditNote                                   */
/*                                                                                   */
/*      For the full copyright and license information, please view the LICENSE.txt  */
/*      file that was distributed with this source code.                             */
/*************************************************************************************/

namespace CreditNote\Hook\Back;

use CreditNote\CreditNote;
use CreditNote\Model\CreditNoteDetailQuery;
use CreditNote\Model\CreditNoteQuery;
use Propel\Runtime\ActiveQuery\Criteria;
use Thelia\Core\Event\Hook\HookRenderBlockEvent;
use Thelia\Core\Event\Hook\HookRenderEvent;
use Thelia\Core\Hook\BaseHook;
use Thelia\Core\Thelia;
use Thelia\Core\Translation\Translator;

/**
 * @author Gilles Bourgeat >gilles.bourgeat@gmail.com>
 */
class OrderEditHook extends BaseHook
{
    public function onOrderEditProductList(HookRenderEvent $event)
    {
        $creditNoteDetails = CreditNoteDetailQuery::create()
            ->filterByQuantity(0, Criteria::GREATER_THAN)
            ->filterByOrderProductId($event->getArgument('order_product_id'))
            ->find();

        $event->add($this->render(
            'hook/order-edit.product-list.html',
            $event->getArguments() + ['creditNoteDetails' => $creditNoteDetails]
        ));
    }

    public function onOrderTab(HookRenderBlockEvent $event)
    {
        $count = CreditNoteQuery::create()
            ->filterByOrderId($event->getArgument('id'))
            ->count();

        $event->add(
            [
                "id" => "credit-note",
                "title" => Translator::getInstance()->trans("Credit Note", [], CreditNote::DOMAIN_MESSAGE) . ($count ? ' (' . $count . ')' : ''),
                "content" => $this->render('hook/order.tab.html', array_merge($event->getArguments(), [

                ]))
            ]
        );
    }

    public function onOrderEditBottom(HookRenderEvent $event)
    {
        $event->add($this->render(
            'includes/credit-note-modal.html',
            array_merge($event->getArguments(), [

                ])
        ));
    }

    public function onOrderEditJs(HookRenderEvent $event)
    {
        $event->add($this->render(
            'hook/order.edit-js.html',
            array_merge($event->getArguments(), [])
        ));
    }
}
