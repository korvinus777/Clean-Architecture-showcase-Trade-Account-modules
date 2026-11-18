<?php

declare(strict_types=1);

namespace App\Http\Controllers\Account;

use OpenApi\Attributes as OA;
use App\Http\Requests\Account\DepositLinkRequest;
use Platform\Package\Components\Http\Response\JsonResponse;
use Platform\Lead\App\Services\GetDepositLink\GetDepositLinkInput;
use Platform\Lead\App\Outputs\GetDepositLink\Results\GetDepositLinkJsonResult;

class DepositController
{
    #[OA\Get(
        path: '/account/{accountId}/deposit-link',
        summary: 'Returns account deposit link',
        security: [['Lead' => []]],
        tags: ['Account'],
        parameters: [
            new OA\Parameter(name: 'accountId', in: 'path', required: true),
            new OA\Parameter(name: 'amount', in: 'query', required: true),
            new OA\Parameter(name: 'domain', in: 'query', required: true),
        ],
    )]
    #[OA\Response(
        response: 200,
        description: 'Success',
        content: new OA\JsonContent(
            properties: [
                new OA\Property('success', type: 'bool', example: true),
                new OA\Property('data', type: 'object', example: [
                    'link' => 'https://cashier.example.com/payment/<id>/identify',
                ]),
            ],
        ),
    )]
    #[OA\Response(ref: '#/components/responses/AuthenticationError', response: 401)]
    #[OA\Response(ref: '#/components/responses/ClientAccessError', response: 403)]
    #[OA\Response(ref: '#/components/responses/ConflictError', response: 409, description: 'Wrong account id')]
    #[OA\Response(response: 422, description: 'Error message', content: new OA\JsonContent(properties: [
        new OA\Property('success', type: 'bool', example: false),
        new OA\Property('message', type: 'string', example: 'Error message'),
    ]))]
    public function link(
        int                 $accountId,
        DepositLinkRequest  $request,
        GetDepositLinkInput $getDepositLinkService,
    ) : JsonResponse {
        /** @var GetDepositLinkJsonResult $result */
        $result = $getDepositLinkService->get(
            $request->domainRequest($accountId),
        );

        return $result->response();
    }
}
