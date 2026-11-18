<?php

declare(strict_types=1);

namespace App\Http\Requests\Trade;

use OpenApi\Attributes as OA;
use Platform\Package\Components\Http\Request\FormValidator;
use Platform\Trade\Domain\Services\EditPendingOrder\EditPendingOrderRequest as DomainEditPendingOrderRequest;

class EditPendingOrderRequest extends FormValidator
{
    #[OA\RequestBody(
        request: 'EditPendingOrderRequest',
        required: true,
        content: new OA\JsonContent(
            properties: [
                new OA\Property('price', type: 'float', example: 15.21),
                new OA\Property('stopLoss', type: 'float', example: 10.21),
                new OA\Property('takeProfit', type: 'float', example: 10.23),
            ],
        ),
    )]
    public function rules() : array
    {
        return [
            'accountId'  => ['numeric', 'min:1'],
            'price'      => ['required_without_all:takeProfit,stopLoss', 'numeric', 'min:0'],
            'stopLoss'   => ['required_without_all:takeProfit,price', 'numeric', 'min:0'],
            'takeProfit' => ['required_without_all:stopLoss,price', 'numeric', 'min:0'],
        ];
    }

    public function domainRequest(int $pendingOrderId) : DomainEditPendingOrderRequest
    {
        return new DomainEditPendingOrderRequest(
            $pendingOrderId,
            $this->getInt('accountId'),
            $this->getNullableFloat('stopLoss'),
            $this->getNullableFloat('takeProfit'),
            $this->getNullableFloat('price'),
        );
    }
}
