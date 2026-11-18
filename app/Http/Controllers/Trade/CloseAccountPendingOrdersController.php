<?php

declare(strict_types=1);

namespace App\Http\Controllers\Trade;

use OpenApi\Attributes as OA;
use Platform\Package\Components\Http\Request\Request;
use Platform\Package\Components\Http\Response\JsonResponse;
use Platform\Trade\App\Services\CloseAccountPendingOrders\CloseAccountPendingOrdersInput;
use Platform\Trade\App\Services\CloseAccountPendingOrders\CloseAccountPendingOrdersRequest;
use Platform\Trade\App\Outputs\CloseAccountPendingOrders\Results\CloseAccountPendingOrdersJsonResult;

class CloseAccountPendingOrdersController
{
    public function __construct(
        private CloseAccountPendingOrdersInput $closeAccountPendingOrdersService,
    ) {
    }

    #[OA\Delete(
        path: '/trade/pending-order',
        summary: 'Closes all pending orders of account',
        security: [['Lead' => []]],
        tags: ['Trade'],
        parameters: [
            new OA\Parameter('Account ID', 'accountId', in: 'query', required: true),
        ],
    )]
    #[OA\Response(
        response: 200,
        description: 'Request completed',
        content: new OA\JsonContent(properties: [
            new OA\Property('success', type: 'bool', example: true),
            new OA\Property('data', properties: [
                new OA\Property('102', ref: '#/components/schemas/ErrorMassiveClosePendingOrder', type: 'object'),
            ], type: 'object'),
        ]),
    )]
    #[OA\Response(ref: '#/components/responses/AuthenticationError', response: 401)]
    #[OA\Response(ref: '#/components/responses/ConflictError', response: 409)]
    public function __invoke() : JsonResponse
    {
        /** @var CloseAccountPendingOrdersJsonResult $result */
        $result = $this->closeAccountPendingOrdersService->close(new CloseAccountPendingOrdersRequest(
            Request::getInt('accountId'),
        ));

        return $result->response();
    }
}
