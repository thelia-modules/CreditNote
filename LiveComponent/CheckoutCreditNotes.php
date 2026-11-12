<?php

declare(strict_types=1);

namespace CreditNote\LiveComponent;

use CreditNote\CreditNote as CreditNoteModule;
use CreditNote\Exception\CreditNoteReservationException;
use CreditNote\Service\CartCreditNoteReservation;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveArg;
use Symfony\UX\LiveComponent\ComponentToolsTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Thelia\Core\Security\SecurityContext;
use Thelia\Domain\Cart\CartFacade;
use Thelia\Model\Cart;
use Thelia\Model\CurrencyQuery;
use Thelia\Model\Customer;
use Thelia\Tools\MoneyFormat;

/**
 * The credit notes of the buyer on the payment step, in the line the theme draws for the module
 * (`checkout.payment.parts`): one box per credit note with something left. Ticking one puts it
 * on the cart, unticking takes it off; the step then reads the composition of the payment again.
 *
 * The cart and the customer are the ones of the session, never a parameter: a credit note id
 * sent by the browser is only ever looked up among the credit notes of that customer. A live
 * action carries its own protection against cross-site calls.
 */
#[AsLiveComponent(name: 'CreditNote:CheckoutCreditNotes', template: '@CreditNoteModule/frontOffice/default/CreditNote/checkout-credit-notes.html.twig')]
final class CheckoutCreditNotes
{
    use ComponentToolsTrait;
    use DefaultActionTrait;

    /**
     * The live event of the checkout of Flexy that makes the payment step read the parts again
     * (FlexyBundle\Event\CheckoutEvents::PAYMENT_PARTS_CHANGED), named here so the module does
     * not depend on the classes of one theme.
     */
    private const PAYMENT_PARTS_CHANGED = 'PAYMENT_PARTS_CHANGED';

    public ?string $error = null;

    public function __construct(
        private readonly CartFacade $cartFacade,
        private readonly SecurityContext $securityContext,
        private readonly CartCreditNoteReservation $reservation,
        private readonly TranslatorInterface $translator,
        private readonly RequestStack $requestStack,
    ) {
    }

    /**
     * @return list<array{id: int, ref: string, balance: string, reserved: bool, whole_only: bool}>
     */
    public function getChoices(): array
    {
        $cart = $this->cartOfTheCustomer();

        if (null === $cart) {
            return [];
        }

        // A credit note to spend whole that the cart is no longer worth cannot pay it: off the
        // cart now, with a word, rather than a refusal once the order is placed.
        if ([] !== $this->reservation->dropWhatNoLongerFits($cart)) {
            $this->error ??= $this->translator->trans('A credit note usable in a single purchase was taken off: your cart is now worth less than it.', [], 'creditnote.fo.default');
        }

        $choices = [];

        foreach ($this->reservation->choicesFor($cart) as $choice) {
            $creditNote = $choice['creditNote'];
            $choices[] = [
                'id' => (int) $creditNote->getId(),
                'ref' => (string) $creditNote->getRef(),
                'balance' => $this->money($choice['balance'], $creditNote->getCurrencyId()),
                'reserved' => $choice['reserved'],
                'whole_only' => !$creditNote->getAllowPartialUse(),
            ];
        }

        return $choices;
    }

    /**
     * @return array<string, string>
     */
    public function getTexts(): array
    {
        return [
            'legend' => $this->translator->trans('Your credit notes', [], 'creditnote.fo.default'),
            'credit_note' => $this->translator->trans('Credit note', [], 'creditnote.fo.default'),
            'left' => $this->translator->trans('left', [], 'creditnote.fo.default'),
            'whole_only' => $this->translator->trans('Usable in a single purchase', [], 'creditnote.fo.default'),
        ];
    }

    #[LiveAction]
    public function toggle(#[LiveArg] int $creditNoteId): void
    {
        $this->error = null;
        $cart = $this->cartOfTheCustomer();

        if (null === $cart) {
            return;
        }

        try {
            if ($this->isOnTheCart($cart, $creditNoteId)) {
                $this->reservation->release($cart, $creditNoteId);
            } else {
                $this->reservation->reserve($cart, $creditNoteId);
            }
        } catch (CreditNoteReservationException $exception) {
            $this->error = CreditNoteReservationException::WHOLE_BALANCE_EXCEEDS_CART === $exception->reason
                ? $this->translator->trans('This credit note can only be used in a single purchase, and your cart is worth less than it: add products or choose another credit note.', [], 'creditnote.fo.default')
                : $this->translator->trans('This credit note cannot be used.', [], 'creditnote.fo.default');

            return;
        }

        $this->emit(self::PAYMENT_PARTS_CHANGED);
    }

    private function isOnTheCart(Cart $cart, int $creditNoteId): bool
    {
        foreach ($this->reservation->reservationsOf((int) $cart->getId()) as $reservation) {
            if ((int) $reservation->getCreditNoteId() === $creditNoteId) {
                return true;
            }
        }

        return false;
    }

    /**
     * The cart of the session, when it is the one of the signed-in customer.
     */
    private function cartOfTheCustomer(): ?Cart
    {
        $customer = $this->securityContext->hasAuthenticatedCustomerUser() ? $this->securityContext->getCustomerUser() : null;

        if (!$customer instanceof Customer) {
            return null;
        }

        $cart = $this->cartFacade->getOrCreateFromSession();

        return (int) $cart->getCustomerId() === (int) $customer->getId() ? $cart : null;
    }

    private function money(float $amount, ?int $currencyId): string
    {
        $symbol = null !== $currencyId ? (CurrencyQuery::create()->findPk($currencyId)?->getSymbol() ?? '') : '';
        $request = $this->requestStack->getCurrentRequest();

        if (null === $request) {
            return number_format($amount, 2, '.', ' ').('' === $symbol ? '' : ' '.$symbol);
        }

        return MoneyFormat::getInstance($request)->format($amount, null, null, null, $symbol);
    }
}
