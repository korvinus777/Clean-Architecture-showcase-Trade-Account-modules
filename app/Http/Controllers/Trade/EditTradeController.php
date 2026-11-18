<?php

declare(strict_types=1);

namespace App\Http\Controllers\Trade;

use OpenApi\Attributes as OA;
use App\Http\Requests\Trade\EditTradeRequest;
use Platform\Trade\App\Services\EditTrade\EditTradeInput;
use Platform\Package\Components\Http\Response\JsonResponse;
use Platform\Trade\App\Outputs\EditTrade\Results\EditTradeJsonResult;

class EditTradeController
{
    public function __construct(
        private EditTradeInput $editTradeService,
    ) {
    }

    #[OA\Put(
        path: '/trade/{tradeId}',
        summary: 'Edits trade',
        security: [['Lead' => []]],
        requestBody: new OA\RequestBody('#/components/requestBodies/EditTradeRequest'),
        tags: ['Trade'],
        parameters: [
            new OA\Parameter('Trade ID', 'tradeId', in: 'path'),
            new OA\Parameter('Account ID', 'accountId', in: 'query'),
        ],
    )]
    #[OA\Response(response: 200, description: 'Successfully edited')]
    #[OA\Response(ref: '#/components/responses/AuthenticationError', response: 401)]
    #[OA\Response(ref: '#/components/responses/ConflictError', response: 409)]
    #[OA\Response(ref: '#/components/responses/ValidationError', response: 422)]
    #[OA\Response(response: 500, description: 'Unexpected error')]
    #[OA\Response(response: 503, description: 'Error on metatrader side')]
    public function __invoke(EditTradeRequest $request, int $tradeId) : JsonResponse
    {
        /** @var EditTradeJsonResult $result */
        $result = $this->editTradeService->edit($request->domainRequest($tradeId));

        return $result->response();
    }
}
