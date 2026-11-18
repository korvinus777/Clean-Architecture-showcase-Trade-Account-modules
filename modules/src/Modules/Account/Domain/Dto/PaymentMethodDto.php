<?php

declare(strict_types=1);

namespace Platform\Account\Domain\Dto;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'PaymentMethodDto',
    properties: [
        new OA\Property('id', type: 'int', example: 1),
        new OA\Property('title', type: 'string', example: 'Credit card'),
        new OA\Property('systemName', type: 'string', example: 'cc'),
        new OA\Property('isActive', type: 'bool', example: true),
        new OA\Property('fields', type: 'array', items: new OA\Items(), example: [
            'card_number',
            'holder_name',
        ]),
    ],
)]
class PaymentMethodDto
{
    public function __construct(
        public int    $id,
        public string $title,
        public string $systemName,
        public bool   $isActive,
        public array  $fields,
    ) {
    }

    public function toArray() : array
    {
        return [
            'id'         => $this->id,
            'title'      => $this->title,
            'systemName' => $this->systemName,
            'isActive'   => $this->isActive,
            'fields'     => $this->fields,
        ];
    }
}
