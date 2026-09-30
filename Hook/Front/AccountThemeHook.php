<?php

declare(strict_types=1);

namespace CreditNote\Hook\Front;

use CreditNote\Service\CreditNoteAccountPresenter;
use CreditNote\Service\FrontTemplateRenderer;
use Symfony\Contracts\Translation\TranslatorInterface;
use Thelia\Core\Hook\Theme\ThemeHookInterface;
use Thelia\Core\Security\SecurityContext;
use Thelia\Domain\Localization\Service\LangService;
use Thelia\Model\Customer;
use Thelia\Model\OrderQuery;

/**
 * The credit notes of the signed-in customer in their account: every accepted credit note
 * with its balance at the bottom of the account page (`account.bottom`), the ones attached
 * to an order at the bottom of that order page (`account-order.bottom`). Nothing is rendered
 * when there is nothing to show, and nothing is ever read for another customer: the
 * customer is the one of the session, never a parameter of the page.
 */
final readonly class AccountThemeHook implements ThemeHookInterface
{
    private const ACCOUNT = 'account.bottom';

    private const ORDER = 'account-order.bottom';

    private const TEMPLATE = 'account-credit-notes.html.twig';

    private const DOMAIN = 'creditnote.fo.default';

    public function __construct(
        private SecurityContext $securityContext,
        private LangService $langService,
        private CreditNoteAccountPresenter $presenter,
        private FrontTemplateRenderer $renderer,
        private TranslatorInterface $translator,
    ) {
    }

    public function supports(string $hookName): bool
    {
        return \in_array($hookName, [self::ACCOUNT, self::ORDER], true);
    }

    public function render(string $hookName, array $parameters): string
    {
        if (!$this->securityContext->hasAuthenticatedCustomerUser()) {
            return '';
        }

        $customer = $this->securityContext->getCustomerUser();

        if (!$customer instanceof Customer) {
            return '';
        }

        $orderId = null;

        if (self::ORDER === $hookName) {
            $orderId = $this->orderOfTheCustomer($parameters['order'] ?? null, $customer);

            if (null === $orderId) {
                return '';
            }
        }

        $locale = $this->langService->getLang()?->getLocale() ?? 'en_US';
        $rows = $this->presenter->rows((int) $customer->getId(), $orderId, $locale);

        if ([] === $rows) {
            return '';
        }

        return $this->renderer->render(self::TEMPLATE, [
            'hook' => $hookName,
            'credit_notes' => $rows,
            'texts' => [
                'title' => self::ORDER === $hookName
                    ? $this->translator->trans('Credit notes on this order', [], 'creditnote.fo.default')
                    : $this->translator->trans('My credit notes', [], 'creditnote.fo.default'),
                'credit_note' => $this->translator->trans('Credit note', [], 'creditnote.fo.default'),
                'issued_on' => $this->translator->trans('issued on', [], 'creditnote.fo.default'),
                'order' => $this->translator->trans('Order', [], 'creditnote.fo.default'),
                'amount' => $this->translator->trans('Amount', [], 'creditnote.fo.default'),
                'balance' => $this->translator->trans('Remaining balance', [], 'creditnote.fo.default'),
                'partial_use' => $this->translator->trans('Usable in several purchases', [], 'creditnote.fo.default'),
                'single_use' => $this->translator->trans('Usable in a single purchase', [], 'creditnote.fo.default'),
                'download' => $this->translator->trans('Download the credit note', [], 'creditnote.fo.default'),
            ],
        ]);
    }

    /**
     * The order the page shows, as the theme hands it over (an API resource array, a model or
     * an id), kept only when it belongs to the customer of the session.
     */
    private function orderOfTheCustomer(mixed $order, Customer $customer): ?int
    {
        $orderId = match (true) {
            \is_int($order) => $order,
            \is_string($order) && ctype_digit($order) => (int) $order,
            \is_array($order) => (int) ($order['id'] ?? 0),
            \is_object($order) && method_exists($order, 'getId') => (int) $order->getId(),
            default => 0,
        };

        if ($orderId <= 0) {
            return null;
        }

        return OrderQuery::create()->filterById($orderId)->filterByCustomerId($customer->getId())->exists() ? $orderId : null;
    }
}
