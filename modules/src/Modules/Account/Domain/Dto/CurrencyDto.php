<?php

declare(strict_types=1);

namespace Platform\Account\Domain\Dto;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'CurrencyDto',
    properties: [
        new OA\Property('id', type: 'int', example: 1),
        new OA\Property('name', type: 'string', example: 'USD'),
        new OA\Property('sign', type: 'string', example: '$'),
        new OA\Property('precision', type: 'int', example: 2),
    ],
)]
class CurrencyDto
{
    public function __construct(
        public int    $id,
        public string $name,
        public string $sign,
        public int    $precision,
    ) {
    }

    public function toArray() : array
    {
        return [
            'id'        => $this->id,
            'name'      => $this->name,
            'sign'      => $this->sign,
            'precision' => $this->precision,
        ];
    }
}
