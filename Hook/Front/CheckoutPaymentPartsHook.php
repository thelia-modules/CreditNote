<?php

declare(strict_types=1);

namespace CreditNote\Hook\Front;

use CreditNote\CreditNote;
use CreditNote\Service\FrontTemplateRenderer;
use Thelia\Core\Hook\Theme\ThemeHookInterface;
use Thelia\Core\Security\SecurityContext;

/**
 * The line of the credit notes in the "Pay part of your order with" block of the payment step
 * (`checkout.payment.parts`). The theme calls the hook once per means that pays inside the
 * shop, always under the same name: only the call made for this module is answered, or the
 * credit notes would show up in the line of every other means.
 */
final readonly class CheckoutPaymentPartsHook implements ThemeHookInterface
{
    private const HOOK = 'checkout.payment.parts';

    private const TEMPLATE = 'checkout-payment-part.html.twig';

    public function __construct(
        private SecurityContext $securityContext,
        private FrontTemplateRenderer $renderer,
    ) {
    }

    public function supports(string $hookName): bool
    {
        return self::HOOK === $hookName;
    }

    public function render(string $hookName, array $parameters): string
    {
        if ((int) ($parameters['module'] ?? 0) !== CreditNote::getModuleId()) {
            return '';
        }

        if (!$this->securityContext->hasAuthenticatedCustomerUser()) {
            return '';
        }

        return $this->renderer->render(self::TEMPLATE, []);
    }
}
