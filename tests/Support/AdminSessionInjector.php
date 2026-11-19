<?php

declare(strict_types=1);

namespace CreditNote\Tests\Support;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Thelia\Model\Admin;
use Thelia\Model\Lang;

/**
 * Puts an administrator in the session of every request the test client sends, after the
 * session is initialized and before the admin firewall reads it: what the login form does,
 * without the form.
 */
final class AdminSessionInjector implements EventSubscriberInterface
{
    private ?Admin $admin = null;

    private ?Lang $lang = null;

    /**
     * @param Lang|null $lang the language the administrator picked for the back-office, as the
     *                        login stores it in the session; none leaves it to the session
     */
    public function setAdmin(Admin $admin, ?Lang $lang = null): void
    {
        $this->admin = $admin;
        $this->lang = $lang;
    }

    public function clear(): void
    {
        $this->admin = null;
        $this->lang = null;
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        if (null === $this->admin) {
            return;
        }

        $request = $event->getRequest();

        // The session factory is attached to the request by the framework session listener
        // (priority 128): asking for the session afterwards initializes it.
        if (!$request->hasSession()) {
            return;
        }

        $session = $request->getSession();

        if (!$session->isStarted()) {
            $session->start();
        }

        $session->set('thelia.admin_user', $this->admin);

        if (null !== $this->lang) {
            $session->set('thelia.current.admin_lang', $this->lang);
        }
    }

    public static function getSubscribedEvents(): array
    {
        // After the framework session listener (128) and the router (32), before the
        // controller is resolved: the admin firewall reads the session on kernel.controller.
        return [KernelEvents::REQUEST => ['onKernelRequest', 16]];
    }
}
