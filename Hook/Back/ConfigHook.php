<?php
/*************************************************************************************/
/*      This file is part of the module CreditNote                                   */
/*                                                                                   */
/*      For the full copyright and license information, please view the LICENSE.txt  */
/*      file that was distributed with this source code.                             */
/*************************************************************************************/

namespace CreditNote\Hook\Back;

use CreditNote\CreditNote;
use CreditNote\Form\CreditNoteConfigForm;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Thelia\Core\Event\Hook\HookRenderEvent;
use Thelia\Core\Form\TheliaFormFactory;
use Thelia\Core\Hook\BaseHook;
use Thelia\Core\Template\Parser\ParserResolver;

class ConfigHook extends BaseHook
{
    public function __construct(
        private readonly TheliaFormFactory $formFactory,
        ?EventDispatcherInterface $dispatcher = null,
        ?ParserResolver $parserResolver = null,
    ) {
        parent::__construct($dispatcher, $parserResolver);
    }

    public static function getSubscribedHooks(): array
    {
        return [
            'module.configuration' => [
                ['type' => 'back', 'method' => 'onModuleConfiguration'],
            ],
        ];
    }

    public function onModuleConfiguration(HookRenderEvent $event): void
    {
        $form = $this->formFactory->createForm(CreditNoteConfigForm::getName(), data: [
            CreditNote::CONFIG_KEY_REF_PREFIX => CreditNote::getConfigValue(CreditNote::CONFIG_KEY_REF_PREFIX, 'CN'),
            CreditNote::CONFIG_KEY_REF_MIN_LENGTH => (int) CreditNote::getConfigValue(CreditNote::CONFIG_KEY_REF_MIN_LENGTH, 8),
            CreditNote::CONFIG_KEY_REF_INCREMENT => (int) CreditNote::getConfigValue(CreditNote::CONFIG_KEY_REF_INCREMENT, 1),
            CreditNote::CONFIG_KEY_INVOICE_REF_PREFIX => CreditNote::getConfigValue(CreditNote::CONFIG_KEY_INVOICE_REF_PREFIX, 'FA'),
            CreditNote::CONFIG_KEY_INVOICE_REF_MIN_LENGTH => (int) CreditNote::getConfigValue(CreditNote::CONFIG_KEY_INVOICE_REF_MIN_LENGTH, 8),
            CreditNote::CONFIG_KEY_INVOICE_REF_INCREMENT => (int) CreditNote::getConfigValue(CreditNote::CONFIG_KEY_INVOICE_REF_INCREMENT, 1),
            CreditNote::CONFIG_KEY_INVOICE_REF_WITH_THELIA_ORDER => (bool) CreditNote::getConfigValue(CreditNote::CONFIG_KEY_INVOICE_REF_WITH_THELIA_ORDER, false),
        ]);

        $event->add($this->render('CreditNote/module-configuration.html.twig', [
            'form' => $form->createView()->getView(),
        ]));
    }
}
