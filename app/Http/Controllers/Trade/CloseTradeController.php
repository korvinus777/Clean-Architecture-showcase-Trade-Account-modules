<?php

declare(strict_types=1);

namespace App\Http\Controllers\Trade;

use OpenApi\Attributes as OA;
use Platform\Package\Components\Http\Request\Request;
use Platform\Package\Components\Http\Response\JsonResponse;
use Platform\Trade\Domain\Services\CloseTrade\CloseTradeInput;
use Platform\Trade\Domain\Services\CloseTrade\CloseTradeRequest;
use Platform\Trade\App\Outputs\CloseTrade\Results\CloseTradeJsonResult;

class CloseTradeController
{
    public function __construct(
        private CloseTradeInput $closeTradeService,
    ) {
    }

    #[OA\Delete(
        path: '/trade/{tradeId}',
        summary: 'Closes trade',
        security: [['Lead' => []]],
        tags: ['Trade'],
        parameters: [
            new OA\Parameter('Trade ID', 'tradeId', in: 'path', required: true),
            new OA\Parameter('Account ID', 'accountId', in: 'query', required: true),
            new OA\Parameter('Timestamp in ms', 'timestampMs', in: 'query', required: true),
        ],
    )]
    #[OA\Response(
        response: 200,
        description: 'Trade successfully closed',
        content: new OA\JsonContent(properties: [
            new OA\Property('success', type: 'bool', example: true),
            new OA\Property('data', type: 'object', example: [
                'tradeId' => 1,
                'price' => 1.2342,
            ]),
        ]),
    )]
    #[OA\Response(ref: '#/components/responses/AuthenticationError', response: 401)]
    #[OA\Response(
        response: 404,
        description: 'Trade not found',
        content: new OA\JsonContent(properties: [
            new OA\Property(property: 'success', type: 'bool', example: false),
            new OA\Property(property: 'error', type: 'string', example: 'TRADE_NOT_FOUND'),
            new OA\Property(property: 'code', type: 'int', example: 16012),
            new OA\Property(property: 'message', type: 'string', example: 'TRADE_NOT_FOUND'),
            new OA\Property('data', type: 'array', items: new OA\Items(), example: ['TRADE_NOT_FOUND']),
        ]),
    )]
    #[OA\Response(ref: '#/components/responses/ConflictError', response: 409)]
    #[OA\Response(ref: '#/components/responses/ValidationError', response: 422)]
    #[OA\Response(
        response: 500,
        description: 'Trade not closed due to unexpected error',
        content: new OA\JsonContent(properties: [
            new OA\Property(property: 'success', type: 'bool', example: false),
            new OA\Property(property: 'error', type: 'string', example: 'TRADE_UNEXPECTED_ERROR'),
            new OA\Property(property: 'code', type: 'int', example: 16015),
            new OA\Property(property: 'message', type: 'string', example: 'TRADE_UNEXPECTED_ERROR'),
            new OA\Property('data', type: 'array', items: new OA\Items(), example: ['TRADE_UNEXPECTED_ERROR']),
        ]),
    )]
    #[OA\Response(response: 503, description: 'Error on metatrader side')]
    public function __invoke(int $tradeId) : JsonResponse
    {
        /** @var CloseTradeJsonResult $result */
        $result = $this->closeTradeService->close(new CloseTradeRequest(
            $tradeId,
            Request::getInt('accountId'),
            Request::getInt('timestampMs'),
            false,
        ));

        return $result->response();
    }
}
