<?php

declare(strict_types=1);

namespace App\Http\Controllers\Trade;

use OpenApi\Attributes as OA;
use App\Http\Requests\Trade\EditPendingOrderRequest;
use Platform\Package\Components\Http\Response\JsonResponse;
use Platform\Trade\Domain\Services\EditPendingOrder\EditPendingOrderInput;
use Platform\Trade\App\Outputs\EditPendingOrder\Result\EditPendingOrderJsonResult;

class EditPendingOrderController
{
    public function __construct(
        private EditPendingOrderInput $editPendingOrderService,
    ) {
    }

    #[OA\Put(
        path: '/trade/order/{orderId}',
        summary: 'Edits pending order',
        security: [['Lead' => []]],
        requestBody: new OA\RequestBody('#/components/requestBodies/EditPendingOrderRequest'),
        tags: ['Trade'],
        parameters: [
            new OA\Parameter('Order ID', 'orderId', in: 'path'),
            new OA\Parameter('Account ID', 'accountId', in: 'query'),
        ],
    )]
    #[OA\Response(response: 200, description: 'Successfully edited')]
    #[OA\Response(ref: '#/components/responses/AuthenticationError', response: 401)]
    #[OA\Response(ref: '#/components/responses/ConflictError', response: 409)]
    #[OA\Response(ref: '#/components/responses/ValidationError', response: 422)]
    #[OA\Response(response: 500, description: 'Unexpected error')]
    #[OA\Response(response: 503, description: 'Error on metatrader side')]
    public function __invoke(EditPendingOrderRequest $request, int $pendingOrderId) : JsonResponse
    {
        /** @var EditPendingOrderJsonResult $result */
        $result = $this->editPendingOrderService->edit($request->domainRequest($pendingOrderId));

        return $result->response();
    }
}
