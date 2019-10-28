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

/**
 * @author Gilles Bourgeat >gilles.bourgeat@gmail.com>
 */
class MainHook extends BaseHook
{
    public function onMainFooterJs(HookRenderEvent $event)
    {
        $event->add($this->render(
            'hook/main.footer-js.html',
            $event->getArguments() + [
                'admin_current_location' => ($this->getRequest()->get('_route') == 'creditnote.list' ? 'credit-note' : '')
            ]
        ));
    }
}
