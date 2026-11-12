<?php

declare(strict_types=1);

namespace CreditNote\Api\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use CreditNote\Api\Dto\CreditNoteCreation;
use CreditNote\Api\Resource\CreditNote as CreditNoteResource;
use CreditNote\Exception\CreditNoteCreationException;
use CreditNote\Exception\CreditNoteExceedsOrderException;
use CreditNote\Service\CreditNoteCreator;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Thelia\Core\Security\AccessManager;
use Thelia\Core\Security\Resource\AdminResources;
use Thelia\Model\AdminLog;

/**
 * Creates a credit note from an order for an administrator, and records it in the
 * administration log like the back-office does. A credit note that cannot be made from what
 * was sent, or that would refund more than its order was worth, is answered 422 and nothing is
 * written.
 *
 * @implements ProcessorInterface<CreditNoteCreation, CreditNoteResource>
 */
final readonly class CreditNoteAdminCreateProcessor implements ProcessorInterface
{
    public function __construct(
        private CreditNoteApiAccess $access,
        private CreditNoteCreator $creator,
        private CreditNoteResourceMapper $mapper,
        private RequestStack $requestStack,
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): CreditNoteResource
    {
        $administrator = $this->access->administrator(AccessManager::UPDATE);

        if (!$data instanceof CreditNoteCreation) {
            throw new BadRequestHttpException('A credit note creation was expected.');
        }

        $quantities = [];

        foreach ($data->lines as $line) {
            $quantities[(int) $line['orderProductId']] = (int) $line['quantity'];
        }

        try {
            $creditNote = $this->creator->fromOrder(
                (int) $data->orderId,
                $data->type,
                $quantities,
                $data->status,
                $data->allowPartialUse,
                $data->comment,
                (int) $administrator->getId(),
            );
        } catch (CreditNoteCreationException $exception) {
            throw new UnprocessableEntityHttpException($exception->getMessage(), $exception);
        } catch (CreditNoteExceedsOrderException $exception) {
            throw new UnprocessableEntityHttpException(
                \sprintf(
                    'The credit notes of order %s cannot refund more than it was worth: %.2f, %.2f already granted, %.2f asked.',
                    $exception->orderRef,
                    $exception->ceiling,
                    $exception->granted,
                    $exception->asked,
                ),
                $exception,
            );
        }

        $request = $this->requestStack->getMainRequest();

        if (null !== $request) {
            AdminLog::append(
                AdminResources::ORDER,
                AccessManager::CREATE,
                \sprintf(
                    'Credit note %s (ID %d) created through the API: status %s, %s %s with tax, order %s',
                    $creditNote->getRef(),
                    $creditNote->getId(),
                    (string) $creditNote->getCreditNoteStatus()?->getCode(),
                    number_format((float) $creditNote->getTotalPriceWithTax(), 2, '.', ''),
                    (string) $creditNote->getCurrency()?->getCode(),
                    (string) $creditNote->getOrder()?->getRef(),
                ),
                $request,
                $administrator,
                false,
                $creditNote->getOrderId(),
            );
        }

        return $this->mapper->mapOne($creditNote);
    }
}
