<?php

declare(strict_types=1);

namespace CreditNote\Api\Resource;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\QueryParameter;
use CreditNote\Api\Dto\CreditNoteCreation;
use CreditNote\Api\State\CreditNoteAdminCreateProcessor;
use CreditNote\Api\State\CreditNoteAdminProvider;
use CreditNote\Api\State\CreditNoteFrontProvider;
use Symfony\Component\Serializer\Attribute\Groups;
use Thelia\Core\Security\AccessManager;

/**
 * A credit note over the API, with what is left of it.
 *
 * Administration (`/api/admin/credit_notes`): the right on the orders and on the module, as in
 * the back-office; a collection filtered by customer, order, status and reference, an item with
 * its lines, and the creation of a credit note from an order.
 *
 * Customer account (`/api/front/account/credit-notes`): the accepted credit notes of the
 * signed-in customer only. The customer is the one of the token, read in the provider: the id
 * of another customer's credit note answers 404, like one that does not exist.
 */
#[ApiResource(
    shortName: 'CreditNote',
    operations: [
        new GetCollection(
            uriTemplate: '/admin/credit_notes',
            normalizationContext: ['groups' => [self::GROUP_ADMIN_READ]],
            provider: CreditNoteAdminProvider::class,
            parameters: [
                'customerId' => new QueryParameter(description: 'The id of the customer the credit notes were granted to.'),
                'orderId' => new QueryParameter(description: 'The id of the order the credit notes refund.'),
                'status' => new QueryParameter(description: 'The code of a status: proposed, refused, accepted, used.'),
                'ref' => new QueryParameter(description: 'A part of the reference.'),
            ],
        ),
        new Get(
            uriTemplate: '/admin/credit_notes/{id}',
            normalizationContext: ['groups' => [self::GROUP_ADMIN_READ, self::GROUP_ADMIN_READ_SINGLE]],
            provider: CreditNoteAdminProvider::class,
        ),
        new Post(
            uriTemplate: '/admin/credit_notes',
            normalizationContext: ['groups' => [self::GROUP_ADMIN_READ, self::GROUP_ADMIN_READ_SINGLE]],
            denormalizationContext: ['groups' => [CreditNoteCreation::GROUP_WRITE]],
            input: CreditNoteCreation::class,
            processor: CreditNoteAdminCreateProcessor::class,
            // Creating a credit note is an update of its order, as in the back-office.
            extraProperties: ['admin_access' => AccessManager::UPDATE],
        ),
        new GetCollection(
            uriTemplate: '/front/account/credit-notes',
            normalizationContext: ['groups' => [self::GROUP_FRONT_READ]],
            provider: CreditNoteFrontProvider::class,
        ),
        new Get(
            uriTemplate: '/front/account/credit-notes/{id}',
            normalizationContext: ['groups' => [self::GROUP_FRONT_READ]],
            provider: CreditNoteFrontProvider::class,
        ),
    ],
)]
final class CreditNote
{
    public const GROUP_ADMIN_READ = 'admin:credit_note:read';
    public const GROUP_ADMIN_READ_SINGLE = 'admin:credit_note:read:single';
    public const GROUP_FRONT_READ = 'front:credit_note:read';

    #[ApiProperty(identifier: true)]
    #[Groups([self::GROUP_ADMIN_READ, self::GROUP_FRONT_READ])]
    public ?int $id = null;

    #[Groups([self::GROUP_ADMIN_READ, self::GROUP_FRONT_READ])]
    public ?string $ref = null;

    #[Groups([self::GROUP_ADMIN_READ, self::GROUP_FRONT_READ])]
    public ?string $invoiceRef = null;

    #[Groups([self::GROUP_ADMIN_READ, self::GROUP_FRONT_READ])]
    public ?\DateTimeInterface $invoiceDate = null;

    #[Groups([self::GROUP_ADMIN_READ, self::GROUP_FRONT_READ])]
    public ?string $status = null;

    #[Groups([self::GROUP_ADMIN_READ])]
    public ?string $type = null;

    #[Groups([self::GROUP_ADMIN_READ])]
    public ?int $customerId = null;

    #[Groups([self::GROUP_ADMIN_READ])]
    public ?int $orderId = null;

    #[Groups([self::GROUP_ADMIN_READ, self::GROUP_FRONT_READ])]
    public ?string $orderRef = null;

    #[Groups([self::GROUP_ADMIN_READ, self::GROUP_FRONT_READ])]
    public ?string $currency = null;

    /** Decimal string, six decimals. */
    #[Groups([self::GROUP_ADMIN_READ])]
    public ?string $totalPrice = null;

    /** Decimal string, six decimals. */
    #[Groups([self::GROUP_ADMIN_READ, self::GROUP_FRONT_READ])]
    public ?string $totalPriceWithTax = null;

    /** What is left to spend: the total with tax minus what orders were paid with. Decimal string, six decimals. */
    #[Groups([self::GROUP_ADMIN_READ, self::GROUP_FRONT_READ])]
    public ?string $balance = null;

    #[Groups([self::GROUP_ADMIN_READ, self::GROUP_FRONT_READ])]
    public bool $allowPartialUse = true;

    #[Groups([self::GROUP_ADMIN_READ])]
    public ?\DateTimeInterface $createdAt = null;

    /**
     * @var list<array{orderProductId: ?int, title: ?string, type: ?string, quantity: int, price: string, priceWithTax: string}>
     */
    #[Groups([self::GROUP_ADMIN_READ_SINGLE])]
    public array $lines = [];
}
