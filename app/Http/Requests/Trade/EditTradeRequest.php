<?php

declare(strict_types=1);

namespace App\Http\Requests\Trade;

use OpenApi\Attributes as OA;
use Platform\Package\Components\Http\Request\Context;
use Platform\Package\Components\Http\Request\FormValidator;
use Platform\Trade\App\Services\EditTrade\EditTradeRequest as DomainEditTradeRequest;

#[OA\RequestBody(
    request: 'EditTradeRequest',
    required: true,
    content: new OA\JsonContent(
        properties: [
            new OA\Property('stopLoss', type: 'float', example: 10.21),
            new OA\Property('takeProfit', type: 'float', example: 10.23),
        ],
    ),
)]
class EditTradeRequest extends FormValidator
{
    public function rules() : array
    {
        return [
            'accountId'  => ['numeric', 'min:1'],
            'stopLoss'   => ['required_without:takeProfit', 'numeric', 'min:0'],
            'takeProfit' => ['required_without:stopLoss', 'numeric', 'min:0'],
        ];
    }

    public function domainRequest(int $tradeId) : DomainEditTradeRequest
    {
        return new DomainEditTradeRequest(
            tradeId: $tradeId,
            accountId: $this->getInt('accountId'),
            context: Context::fromRequest(),
            stopLoss: $this->getNullableFloat('stopLoss'),
            takeProfit: $this->getNullableFloat('takeProfit'),
        );
    }
}
