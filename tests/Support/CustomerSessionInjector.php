<?php

declare(strict_types=1);

namespace CreditNote\Tests\Support;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Thelia\Model\Customer;

/**
 * Puts a customer in the session of every request the test client sends, after the session
 * is initialized and before the controller checks the sign-in: what the login form does,
 * without the form.
 */
final class CustomerSessionInjector implements EventSubscriberInterface
{
    private ?Customer $customer = null;

    public function setCustomer(Customer $customer): void
    {
        $this->customer = $customer;
    }

    public function clear(): void
    {
        $this->customer = null;
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        if (null === $this->customer) {
            return;
        }

        $request = $event->getRequest();

        if (!$request->hasSession()) {
            return;
        }

        $session = $request->getSession();

        if (!$session->isStarted()) {
            $session->start();
        }

        $session->set('thelia.customer_user', $this->customer);
    }

    public static function getSubscribedEvents(): array
    {
        return [KernelEvents::REQUEST => ['onKernelRequest', 16]];
    }
}
