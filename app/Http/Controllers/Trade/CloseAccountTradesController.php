<?php

declare(strict_types=1);

namespace App\Http\Controllers\Trade;

use OpenApi\Attributes as OA;
use Platform\Package\Components\Http\Request\Request;
use Platform\Package\Components\Http\Response\JsonResponse;
use Platform\Trade\App\Services\CloseAccountTrades\CloseAccountTradesInput;
use Platform\Trade\App\Services\CloseAccountTrades\CloseAccountTradesRequest;
use Platform\Trade\App\Outputs\CloseAccountTrades\Results\CloseAccountTradesJsonResult;

class CloseAccountTradesController
{
    public function __construct(
        private CloseAccountTradesInput $closeAccountTradesService,
    ) {
    }

    #[OA\Delete(
        path: '/trade',
        summary: 'Closes all trades of account',
        security: [['Lead' => []]],
        tags: ['Trade'],
        parameters: [
            new OA\Parameter('Account ID', 'accountId', in: 'query', required: true),
            new OA\Parameter('Timestamp in ms', 'timestampMs', in: 'query', required: true),
        ],
    )]
    #[OA\Response(
        response: 200,
        description: 'Request completed',
        content: new OA\JsonContent(properties: [
            new OA\Property('success', type: 'bool', example: true),
            new OA\Property('data', properties: [
                new OA\Property('102', ref: '#/components/schemas/ErrorMassiveCloseTrade', type: 'object'),
            ], type: 'object'),
        ]),
    )]
    #[OA\Response(ref: '#/components/responses/AuthenticationError', response: 401)]
    #[OA\Response(ref: '#/components/responses/ConflictError', response: 409)]
    public function __invoke() : JsonResponse
    {
        /** @var CloseAccountTradesJsonResult $result */
        $result = $this->closeAccountTradesService->close(new CloseAccountTradesRequest(
            Request::getInt('accountId'),
            Request::getInt('timestampMs'),
        ));

        return $result->response();
    }
}
