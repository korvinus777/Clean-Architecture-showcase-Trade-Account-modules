<?php

declare(strict_types=1);

namespace App\Http\Controllers\Trade;

use OpenApi\Attributes as OA;
use Platform\Package\Components\Http\Request\Request;
use Platform\Package\Components\Http\Response\JsonResponse;
use Platform\Trade\Domain\Services\ClosePendingOrder\ClosePendingOrderInput;
use Platform\Trade\Domain\Services\ClosePendingOrder\ClosePendingOrderRequest;
use Platform\Trade\App\Outputs\ClosePendingOrder\Results\ClosePendingOrderJsonResult;

class ClosePendingOrderController
{
    public function __construct(
        private ClosePendingOrderInput $closePendingOrderService,
    ) {
    }
    #[OA\Delete(
        path: '/trade/order/{pendingOrderId}',
        summary: 'Closes pending order',
        security: [['Lead' => []]],
        tags: ['Trade'],
        parameters: [
            new OA\Parameter('Pending Order ID', 'pendingOrderId', in: 'path', required: true),
            new OA\Parameter('Account ID', 'accountId', in: 'query', required: true),
        ],
    )]
    #[OA\Response(
        response: 200,
        description: 'Pending order successfully closed',
        content: new OA\JsonContent(properties: [
            new OA\Property('success', type: 'bool', example: true),
            new OA\Property('data', type: 'object', example: [
                'pendingOrderId' => 1,
            ]),
        ]),
    )]
    #[OA\Response(ref: '#/components/responses/AuthenticationError', response: 401)]
    #[OA\Response(
        response: 404,
        description: 'Pending order not found',
        content: new OA\JsonContent(properties: [
            new OA\Property(property: 'success', type: 'bool', example: false),
            new OA\Property(property: 'error', type: 'string', example: 'PENDING_ORDER_NOT_FOUND'),
            new OA\Property(property: 'code', type: 'int', example: 16027),
            new OA\Property(property: 'message', type: 'string', example: 'PENDING_ORDER_NOT_FOUND'),
            new OA\Property('data', type: 'array', items: new OA\Items(), example: []),
        ]),
    )]
    #[OA\Response(ref: '#/components/responses/ValidationError', response: 422)]
    #[OA\Response(response: 503, description: 'Error on metatrader side')]
    public function __invoke(int $platformOrderId) : JsonResponse
    {
        /** @var ClosePendingOrderJsonResult $result */
        $result = $this->closePendingOrderService->close(new ClosePendingOrderRequest(
            $platformOrderId,
            Request::getInt('accountId'),
        ));

        return $result->response();
    }
}
