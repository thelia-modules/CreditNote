<?php

declare(strict_types=1);

namespace CreditNote\Api\State;

use CreditNote\CreditNote;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Thelia\Core\Security\Resource\AdminResources;
use Thelia\Core\Security\SecurityContext;
use Thelia\Model\Admin;
use Thelia\Model\Customer;

/**
 * Who is calling the credit note API, and what they may do. The firewall and the core already
 * hold `/api/admin` to an administrator with the right on the orders, and `/api/front/account`
 * to a signed-in customer; the module right is asked here, so the API reaches what the
 * back-office reaches, no more.
 */
final readonly class CreditNoteApiAccess
{
    public function __construct(
        private Security $security,
        private SecurityContext $securityContext,
    ) {
    }

    /**
     * @throws AccessDeniedHttpException when the caller is no administrator allowed to do this
     */
    public function administrator(string $access): Admin
    {
        $user = $this->security->getUser();

        if (!$user instanceof Admin
            || !$this->securityContext->isUserGranted(['ADMIN'], [AdminResources::ORDER], [CreditNote::DOMAIN_MESSAGE], [$access], $user)
        ) {
            throw new AccessDeniedHttpException('You are not allowed to do this on credit notes.');
        }

        return $user;
    }

    /**
     * @throws AccessDeniedHttpException when the caller is no signed-in customer
     */
    public function customer(): Customer
    {
        $user = $this->security->getUser();

        if (!$user instanceof Customer) {
            throw new AccessDeniedHttpException('Sign in to read your credit notes.');
        }

        return $user;
    }
}
