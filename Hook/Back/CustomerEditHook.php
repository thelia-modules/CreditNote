<?php
/*************************************************************************************/
/*      This file is part of the module CreditNote                                   */
/*                                                                                   */
/*      For the full copyright and license information, please view the LICENSE.txt  */
/*      file that was distributed with this source code.                             */
/*************************************************************************************/

namespace CreditNote\Hook\Back;

use CreditNote\CreditNote;
use Thelia\Core\Event\Hook\HookRenderEvent;
use Thelia\Core\Hook\BaseHook;
use Thelia\Core\Thelia;

/**
 * @author Gilles Bourgeat >gilles.bourgeat@gmail.com>
 */
class CustomerEditHook extends BaseHook
{
    public function onCustomerEdit(HookRenderEvent $event)
    {
        $event->add($this->render(
            'hook/customer.edit.html',
            array_merge($event->getArguments(), [

            ])
        ));
    }

    public function onCustomerEditBottom(HookRenderEvent $event)
    {
        $event->add($this->render(
            'includes/credit-note-modal.html',
            array_merge($event->getArguments(), [])
        ));
    }

    public function onCustomerEditJs(HookRenderEvent $event)
    {
        $event->add($this->render(
            'includes/credit-note-js.html',
            array_merge($event->getArguments(), [])
        ));
    }
}
