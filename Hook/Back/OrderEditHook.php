<?php
/*************************************************************************************/
/*      This file is part of the module CreditNote                                   */
/*                                                                                   */
/*      For the full copyright and license information, please view the LICENSE.txt  */
/*      file that was distributed with this source code.                             */
/*************************************************************************************/

namespace CreditNote\Hook\Back;

use CreditNote\CreditNote;
use CreditNote\Service\CreditNoteHookPresenter;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Thelia\Core\Event\Hook\HookRenderBlockEvent;
use Thelia\Core\Event\Hook\HookRenderEvent;
use Thelia\Core\Hook\BaseHook;
use Thelia\Core\Template\Parser\ParserResolver;
use Thelia\Core\Translation\Translator;

/**
 * @author Gilles Bourgeat >gilles.bourgeat@gmail.com>
 */
class OrderEditHook extends BaseHook
{
    use TemplateFallbackTrait;

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
            'order.tab' => [
                ['type' => 'back', 'method' => 'onOrderTab'],
            ],
            'order-edit.bottom' => [
                ['type' => 'back', 'method' => 'onOrderEditBottom'],
            ],
            'order.edit-js' => [
                ['type' => 'back', 'method' => 'onOrderEditJs'],
            ],
            'order-edit.product-list' => [
                ['type' => 'back', 'method' => 'onOrderEditProductList'],
            ],
        ];
    }

    public function onOrderEditProductList(HookRenderEvent $event): void
    {
        $locale = $this->getRequest()?->getLocale() ?? 'en_US';

        $event->add($this->render(
            'CreditNote/hook/order-edit.product-list.html.twig',
            $event->getArguments() + [
                'creditNoteDetails' => $this->presenter->detailRowsForOrderProduct(
                    (int) $event->getArgument('order_product_id'),
                    $locale
                ),
            ]
        ));
    }

    public function onOrderTab(HookRenderBlockEvent $event): void
    {
        $orderId = (int) $event->getArgument('id');
        $locale = $this->getRequest()?->getLocale() ?? 'en_US';
        $count = $this->presenter->countForOrder($orderId);

        $event->add([
            'id' => 'credit-note',
            'title' => Translator::getInstance()->trans('Credit Note', [], CreditNote::DOMAIN_MESSAGE)
                . ($count ? ' (' . $count . ')' : ''),
            'content' => $this->render('CreditNote/hook/order.tab.html.twig', $event->getArguments() + [
                'credit_notes' => $this->presenter->tableRows(null, $orderId, $locale),
                'order_id' => $orderId,
            ]),
        ]);
    }

    public function onOrderEditBottom(HookRenderEvent $event): void
    {
        $event->add($this->render(
            'CreditNote/includes/credit-note-modal.html.twig',
            $event->getArguments()
        ));
    }

    public function onOrderEditJs(HookRenderEvent $event): void
    {
        $orderId = (int) $event->getArgument('order_id');

        $event->add($this->render(
            'CreditNote/hook/order.edit-js.html.twig',
            $event->getArguments() + [
                'order_id' => $orderId,
                'credit_notes_used' => $this->presenter->creditNotesUsedOnOrder($orderId),
            ]
        ));
    }
}
