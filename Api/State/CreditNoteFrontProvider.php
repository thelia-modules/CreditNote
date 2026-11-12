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

/**
 * The credit notes of the signed-in customer: the accepted ones and the ones already used, as
 * in their account. The customer is the one of the token, never a parameter: every query is
 * held to it here, so the credit note of someone else, or a draft of the merchant, is not found.
 *
 * @implements ProviderInterface<CreditNoteResource>
 */
final readonly class CreditNoteFrontProvider implements ProviderInterface
{
    public function __construct(
        private CreditNoteApiAccess $access,
        private CreditNoteResourceMapper $mapper,
        private Pagination $pagination,
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $query = CreditNoteQuery::create()
            ->filterByCustomerId($this->access->customer()->getId())
            ->useCreditNoteStatusQuery()
                ->filterByInvoiced(true)
            ->endUse();

        if (!$operation instanceof CollectionOperationInterface) {
            $creditNote = $query->filterById((int) ($uriVariables['id'] ?? 0))->findOne();

            return null === $creditNote ? null : $this->mapper->mapOne($creditNote);
        }

        $page = $this->pagination->getPage($context);
        $limit = $this->pagination->getLimit($operation, $context);
        $pager = $query->orderByInvoiceDate(Criteria::DESC)->orderById(Criteria::DESC)->paginate($page, $limit);

        return new TraversablePaginator(
            new \ArrayIterator($this->mapper->mapAll($pager->getResults())),
            $page,
            $limit,
            $pager->getNbResults(),
        );
    }
}
