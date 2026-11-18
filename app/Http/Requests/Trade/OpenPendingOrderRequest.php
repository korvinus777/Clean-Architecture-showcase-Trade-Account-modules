<?php

declare(strict_types=1);

namespace App\Http\Requests\Trade;

use OpenApi\Attributes as OA;
use Illuminate\Validation\Rules\Enum;
use Platform\Trade\Domain\ValueObjects\Direction;
use Platform\Package\Components\Http\Request\FormValidator;
use Platform\Trade\Domain\Services\OpenPendingOrder\OpenPendingOrderRequest as DomainOpenPendingOrderRequest;

#[OA\RequestBody(
    request: 'OpenPendingOrderRequest',
    required: true,
    content: new OA\JsonContent(
        required: [
            'accountId',
            'symbolId',
            'price',
            'lots',
            'stopLoss',
            'takeProfit',
            'direction',
            'timestampMs',
        ],
        properties: [
            new OA\Property('accountId', type: 'int', example: 1),
            new OA\Property('symbolId', type: 'int', example: 1),
            new OA\Property('price', type: 'float', example: 77.19),
            new OA\Property('lots', type: 'float', example: 0.1),
            new OA\Property('stopLoss', type: 'float', example: 10.21),
            new OA\Property('takeProfit', type: 'float', example: 10.23),
            new OA\Property('direction', description: '-1 = Sell, 1 = Buy', type: 'int', example: 1),
            new OA\Property('timestampMs', type: 'int', example: 1649851287619),
        ],
    ),
)]
class OpenPendingOrderRequest extends FormValidator
{
    public function rules() : array
    {
        return [
            'accountId'   => ['required', 'integer'],
            'symbolId'    => ['required', 'integer'],
            'price'       => ['required', 'numeric'],
            'lots'        => ['required', 'numeric'],
            'stopLoss'    => ['required', 'numeric', 'min:0'],
            'takeProfit'  => ['required', 'numeric', 'min:0'],
            'direction'   => ['required', new Enum(Direction::class)],
            'timestampMs' => ['required', 'numeric', 'min:0'],
        ];
    }

    public function domainRequest() : DomainOpenPendingOrderRequest
    {
        return new DomainOpenPendingOrderRequest(
            $this->getInt('accountId'),
            $this->getInt('symbolId'),
            $this->getFloat('price'),
            $this->getFloat('lots'),
            $this->getFloat('stopLoss'),
            $this->getFloat('takeProfit'),
            Direction::from($this->getInt('direction')),
            $this->getInt('timestampMs'),
        );
    }
}
