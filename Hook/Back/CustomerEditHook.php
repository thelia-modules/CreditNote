<?php
/*************************************************************************************/
/*      This file is part of the module CreditNote                                   */
/*                                                                                   */
/*      For the full copyright and license information, please view the LICENSE.txt  */
/*      file that was distributed with this source code.                             */
/*************************************************************************************/

namespace CreditNote\Hook\Back;

use CreditNote\Service\CreditNoteAccess;
use CreditNote\Service\CreditNoteHookPresenter;
use CreditNote\Service\CreditNoteListReader;
use CreditNote\Service\CreditNoteListRowPresenter;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Thelia\Core\Security\AccessManager;
use Thelia\Core\Event\Hook\HookRenderEvent;
use Thelia\Core\Hook\BaseHook;
use Thelia\Core\Template\Parser\ParserResolver;

/**
 * @author Gilles Bourgeat >gilles.bourgeat@gmail.com>
 */
class CustomerEditHook extends BaseHook
{
    public function __construct(
        private readonly CreditNoteHookPresenter $presenter,
        private readonly CreditNoteAccess $access,
        private readonly CreditNoteListReader $reader,
        private readonly CreditNoteListRowPresenter $rowPresenter,
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
        if (!$this->access->isGranted(AccessManager::VIEW)) {
            return;
        }

        $customerId = (int) $event->getArgument('customer_id');
        $locale = $this->getRequest()?->getLocale() ?? 'en_US';

        $event->add($this->render(
            'CreditNote/hook/customer.edit.html.twig',
            $event->getArguments() + [
                'customer_id' => $customerId,
                'credit_notes' => $this->rowPresenter->presentAll($this->reader->ofCustomer($customerId), $locale),
            ]
        ));
    }

    public function onCustomerEditBottom(HookRenderEvent $event): void
    {
        if (!$this->access->isGranted(AccessManager::VIEW)) {
            return;
        }

        $event->add($this->render(
            'CreditNote/includes/credit-note-modal.html.twig',
            $event->getArguments()
        ));
    }

    public function onCustomerEditJs(HookRenderEvent $event): void
    {
        if (!$this->access->isGranted(AccessManager::VIEW)) {
            return;
        }

        $event->add($this->render(
            'CreditNote/includes/credit-note-js.html.twig',
            $event->getArguments()
        ));
    }
}
