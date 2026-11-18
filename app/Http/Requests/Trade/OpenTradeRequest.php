<?php

declare(strict_types=1);

namespace App\Http\Requests\Trade;

use OpenApi\Attributes as OA;
use Illuminate\Validation\Rule;
use Platform\Trade\Domain\ValueObjects\Direction;
use Platform\Package\Components\Http\Request\FormValidator;
use Platform\Trade\Domain\Services\OpenTrade\OpenTradeRequest as DomainOpenTradeRequest;

#[OA\RequestBody(
    request: 'OpenTradeRequest',
    required: true,
    content: new OA\JsonContent(
        required: [
            'accountId',
            'symbolId',
            'lots',
            'direction',
            'timestampMs',
        ],
        properties: [
            new OA\Property('accountId', type: 'int', example: 1),
            new OA\Property('symbolId', type: 'int', example: 1),
            new OA\Property('lots', type: 'float', example: 0.1),
            new OA\Property('stopLoss', type: 'float', example: 10.21),
            new OA\Property('takeProfit', type: 'float', example: 10.23),
            new OA\Property('direction', description: '-1 = Sell, 1 = Buy', type: 'int', example: 1),
            new OA\Property('timestampMs', type: 'int', example: 1649851287619),
        ],
    ),
)]
class OpenTradeRequest extends FormValidator
{
    public function rules() : array
    {
        return [
            'accountId'   => ['required', 'integer'],
            'symbolId'    => ['required', 'integer'],
            'lots'        => ['required', 'numeric'],
            'direction'   => ['required', Rule::in(array_map(fn (Direction $dir) => $dir->value, Direction::cases()))],
            'timestampMs' => ['required', 'numeric', 'min:0'],
            'stopLoss'    => ['numeric', 'min:0'],
            'takeProfit'  => ['numeric', 'min:0'],
        ];
    }

    public function domainRequest() : DomainOpenTradeRequest
    {
        return new DomainOpenTradeRequest(
            accountId: $this->getInt('accountId'),
            symbolId: $this->getInt('symbolId'),
            lots: $this->getFloat('lots'),
            stopLoss: $this->getFloat('stopLoss'),
            takeProfit: $this->getFloat('takeProfit'),
            direction: Direction::from($this->getInt('direction')),
            requestedFromClient: $this->getInt('timestampMs'),
        );
    }
}
