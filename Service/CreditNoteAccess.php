<?php

declare(strict_types=1);

namespace CreditNote\Service;

use CreditNote\CreditNote;
use Thelia\Core\Security\Resource\AdminResources;
use Thelia\Core\Security\SecurityContext;

/**
 * What the administrator may do with credit notes: the right on the orders and on the module,
 * both, as the back-office routes require. The hooks ask it before rendering anything.
 */
final readonly class CreditNoteAccess
{
    public function __construct(
        private SecurityContext $securityContext,
    ) {
    }

    public function isGranted(string $access): bool
    {
        return $this->securityContext->isGranted(
            ['ADMIN'],
            [AdminResources::ORDER],
            [CreditNote::DOMAIN_MESSAGE],
            [$access],
        );
    }
}
