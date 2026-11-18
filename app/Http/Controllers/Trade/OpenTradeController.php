<?php

declare(strict_types=1);

namespace App\Http\Controllers\Trade;

use OpenApi\Attributes as OA;
use App\Http\Requests\Trade\OpenTradeRequest;
use Platform\Package\Components\Http\Response\JsonResponse;
use Platform\Trade\Domain\Services\OpenTrade\OpenTradeInput;
use Platform\Trade\App\Outputs\OpenTrade\Results\OpenTradeJsonResult;

class OpenTradeController
{
    public function __construct(
        private OpenTradeInput $openTradeService,
    ) {
    }

    #[OA\Post(
        path: '/trade',
        summary: 'Opens trade',
        security: [['Lead' => []]],
        requestBody: new OA\RequestBody('#/components/requestBodies/OpenTradeRequest'),
        tags: ['Trade'],
    )]
    #[OA\Response(
        response: 200,
        description: 'Trade successfully opened',
        content: new OA\JsonContent(properties: [
            new OA\Property('success', type: 'bool', example: true),
            new OA\Property('data', type: 'object', example: [
                'tradeId' => 1,
                'price' => 1.2342,
            ]),
        ]),
    )]
    #[OA\Response(ref: '#/components/responses/AuthenticationError', response: 401)]
    #[OA\Response(ref: '#/components/responses/ConflictError', response: 409)]
    #[OA\Response(ref: '#/components/responses/ValidationError', response: 422)]
    #[OA\Response(response: 503, description: 'Error on metatrader side')]
    public function __invoke(OpenTradeRequest $request) : JsonResponse
    {
        /** @var OpenTradeJsonResult $result */
        $result = $this->openTradeService->open($request->domainRequest());

        return $result->response();
    }
}
