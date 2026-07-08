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
class CustomerEditHook extends BaseHook
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
            'customer.edit' => [
                ['type' => 'back', 'method' => 'onCustomerEdit'],
            ],
            'customer-edit.bottom' => [
                ['type' => 'back', 'method' => 'onCustomerEditBottom'],
            ],
            'customer.edit-js' => [
                ['type' => 'back', 'method' => 'onCustomerEditJs'],
            ],
        ];
    }

    public function onCustomerEdit(HookRenderEvent $event): void
    {
        $customerId = (int) $event->getArgument('customer_id');
        $locale = $this->getRequest()?->getLocale() ?? 'en_US';

        $event->add($this->render(
            'CreditNote/hook/customer.edit.html.twig',
            $event->getArguments() + [
                'customer_id' => $customerId,
                'credit_notes' => $this->presenter->tableRows($customerId, null, $locale),
            ]
        ));
    }

    public function onCustomerEditBottom(HookRenderEvent $event): void
    {
        $event->add($this->render(
            'CreditNote/includes/credit-note-modal.html.twig',
            $event->getArguments()
        ));
    }

    public function onCustomerEditJs(HookRenderEvent $event): void
    {
        $event->add($this->render(
            'CreditNote/includes/credit-note-js.html.twig',
            $event->getArguments()
        ));
    }
}
