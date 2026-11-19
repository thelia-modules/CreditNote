<?php

declare(strict_types=1);

namespace CreditNote\DependencyInjection;

use CreditNote\Api\Resource\CreditNote as CreditNoteResource;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Thelia\Core\Security\Resource\AdminResources;

/**
 * Tells the core which administration right guards the credit notes under `/api/admin`: the
 * right on the orders, as in the back-office. The core refuses to every administrator but the
 * superadministrator a resource no one declared, so the declaration is added to the list the
 * other modules fill, never set in its place.
 */
final class AdminApiResourcePass implements CompilerPassInterface
{
    private const PARAMETER = 'thelia.api.admin_resources';

    public function process(ContainerBuilder $container): void
    {
        $resources = $container->hasParameter(self::PARAMETER) ? (array) $container->getParameter(self::PARAMETER) : [];
        $resources[CreditNoteResource::class] = AdminResources::ORDER;

        $container->setParameter(self::PARAMETER, $resources);
    }
}
