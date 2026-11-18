<?php

declare(strict_types=1);

namespace App\Http\Controllers\Trade;

use OpenApi\Attributes as OA;
use App\Http\Requests\Trade\OpenPendingOrderRequest;
use Platform\Package\Components\Http\Response\JsonResponse;
use Platform\Trade\Domain\Services\OpenPendingOrder\OpenPendingOrderInput;
use Platform\Trade\App\Outputs\OpenPendingOrder\Results\OpenPendingOrderJsonResult;

class OpenPendingOrderController
{
    public function __construct(private OpenPendingOrderInput $openPendingOrderService)
    {
    }

    #[OA\Post(
        path: '/trade/order',
        summary: 'Opens Pending Order',
        security: [['Lead' => []]],
        requestBody: new OA\RequestBody('#/components/requestBodies/OpenPendingOrderRequest'),
        tags: ['Trade'],
    )]
    #[OA\Response(
        response: 200,
        description: 'Pending Order successfully opened',
        content: new OA\JsonContent(properties: [
            new OA\Property('orderId', type: 'int', example: 1),
            new OA\Property('price', type: 'float', example: 1.231),
        ]),
    )]
    #[OA\Response(ref: '#/components/responses/AuthenticationError', response: 401)]
    #[OA\Response(ref: '#/components/responses/ConflictError', response: 409)]
    #[OA\Response(ref: '#/components/responses/ValidationError', response: 422)]
    #[OA\Response(response: 503, description: 'Error on metatrader side')]
    public function __invoke(OpenPendingOrderRequest $request) : JsonResponse
    {
        /** @var OpenPendingOrderJsonResult $result */
        $result = $this->openPendingOrderService->open($request->domainRequest());

        return $result->response();
    }
}
