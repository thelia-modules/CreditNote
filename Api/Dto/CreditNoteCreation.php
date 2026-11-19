<?php

declare(strict_types=1);

namespace CreditNote\Api\Dto;

use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * What an administrator sends to create a credit note from an order: the order, the type of
 * credit note, and the lines of the order to refund with their quantity. A full refund
 * (`order_full_refund`) takes every line, the postage and the discount of the order: its lines
 * are not read.
 */
final class CreditNoteCreation
{
    public const GROUP_WRITE = 'admin:credit_note:write';

    #[Groups([self::GROUP_WRITE])]
    #[Assert\NotNull]
    #[Assert\Positive]
    public ?int $orderId = null;

    #[Groups([self::GROUP_WRITE])]
    #[Assert\NotBlank]
    public string $type = 'back_product';

    #[Groups([self::GROUP_WRITE])]
    #[Assert\Choice(choices: ['proposed', 'accepted'])]
    public string $status = 'proposed';

    /**
     * @var list<array{orderProductId: int, quantity: int}>
     */
    #[Groups([self::GROUP_WRITE])]
    #[Assert\All([
        new Assert\Collection(fields: [
            'orderProductId' => [new Assert\NotNull(), new Assert\Type('integer'), new Assert\Positive()],
            'quantity' => [new Assert\NotNull(), new Assert\Type('integer'), new Assert\Positive()],
        ]),
    ])]
    public array $lines = [];

    #[Groups([self::GROUP_WRITE])]
    public bool $allowPartialUse = true;

    #[Groups([self::GROUP_WRITE])]
    #[Assert\Length(max: 2000)]
    public ?string $comment = null;
}
