<?php

declare(strict_types=1);

namespace CreditNote\Api\State;

use ApiPlatform\Metadata\CollectionOperationInterface;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\Pagination\Pagination;
use ApiPlatform\State\Pagination\TraversablePaginator;
use ApiPlatform\State\ProviderInterface;
use CreditNote\Api\Resource\CreditNote as CreditNoteResource;
use CreditNote\Model\CreditNoteQuery;
use Propel\Runtime\ActiveQuery\Criteria;
use Thelia\Core\Security\AccessManager;

/**
 * The credit notes for an administrator: a page of the collection, filtered by customer, order,
 * status code and part of the reference, or one credit note with its lines.
 *
 * @implements ProviderInterface<CreditNoteResource>
 */
final readonly class CreditNoteAdminProvider implements ProviderInterface
{
    public function __construct(
        private CreditNoteApiAccess $access,
        private CreditNoteResourceMapper $mapper,
        private Pagination $pagination,
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $this->access->administrator(AccessManager::VIEW);

        if (!$operation instanceof CollectionOperationInterface) {
            $creditNote = CreditNoteQuery::create()->findPk((int) ($uriVariables['id'] ?? 0));

            return null === $creditNote ? null : $this->mapper->mapOne($creditNote);
        }

        $filters = $context['filters'] ?? [];
        $query = CreditNoteQuery::create()->orderById(Criteria::DESC);

        if (is_numeric($filters['customerId'] ?? null)) {
            $query->filterByCustomerId((int) $filters['customerId']);
        }

        if (is_numeric($filters['orderId'] ?? null)) {
            $query->filterByOrderId((int) $filters['orderId']);
        }

        if (\is_string($filters['status'] ?? null) && '' !== $filters['status']) {
            $query->useCreditNoteStatusQuery()->filterByCode($filters['status'])->endUse();
        }

        if (\is_string($filters['ref'] ?? null) && '' !== $filters['ref']) {
            $query->filterByRef('%'.addcslashes($filters['ref'], '%_\\').'%', Criteria::LIKE);
        }

        $page = $this->pagination->getPage($context);
        $limit = $this->pagination->getLimit($operation, $context);
        $pager = $query->paginate($page, $limit);

        return new TraversablePaginator(
            new \ArrayIterator($this->mapper->mapAll($pager->getResults())),
            $page,
            $limit,
            $pager->getNbResults(),
        );
    }
}
