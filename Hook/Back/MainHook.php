<?php
/*************************************************************************************/
/*      This file is part of the module CreditNote                                   */
/*                                                                                   */
/*      For the full copyright and license information, please view the LICENSE.txt  */
/*      file that was distributed with this source code.                             */
/*************************************************************************************/

namespace CreditNote\Hook\Back;

use CreditNote\Service\CreditNoteHookPresenter;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Thelia\Core\Event\Hook\HookRenderEvent;
use Thelia\Core\Hook\BaseHook;
use Thelia\Core\Template\Parser\ParserResolver;

/**
 * @author Gilles Bourgeat >gilles.bourgeat@gmail.com>
 */
class MainHook extends BaseHook
{
    public function __construct(
        private readonly CreditNoteHookPresenter $presenter,
        ?EventDispatcherInterface $dispatcher = null,
        ?ParserResolver $parserResolver = null,
    ) {
        parent::__construct($dispatcher, $parserResolver);
    }

    public static function getSubscribedHooks(): array
    {
        return [
            'main.footer-js' => [
                ['type' => 'back', 'method' => 'onMainFooterJs'],
            ],
        ];
    }

    public function onMainFooterJs(HookRenderEvent $event): void
    {
        $request = $this->getRequest();
        $locale = $request?->getLocale() ?? 'en_US';
        $currentRoute = $request?->attributes->get('_route');

        $event->add($this->render(
            'CreditNote/hook/main.footer-js.html.twig',
            $event->getArguments() + [
                'admin_current_location' => $currentRoute === 'credit_note_list' ? 'credit-note' : '',
                'menu_statuses' => $this->presenter->menuStatuses($locale),
                'total_count' => $this->presenter->totalCount(),
            ]
        ));
    }
}
