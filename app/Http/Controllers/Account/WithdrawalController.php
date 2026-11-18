<?php

declare(strict_types=1);

namespace App\Http\Controllers\Account;

use OpenApi\Attributes as OA;
use Platform\Package\Components\Http\Response\JsonResponse;
use Platform\Account\Domain\Services\GetWithdrawalInfo\GetWithdrawalInfoInput;
use Platform\Account\Domain\Services\GetWithdrawalInfo\GetWithdrawalInfoRequest;
use Platform\Account\App\Outputs\GetWithdrawalInfo\Results\GetWithdrawalInfoJsonResult;

class WithdrawalController
{
    #[OA\Get(
        path: '/account/withdrawal-info/{accountId}',
        summary: 'Returns account\' withdrawal info',
        security: [['Lead' => []]],
        tags: ['Account'],
        parameters: [
            new OA\Parameter(name: 'accountId', in: 'path', required: true),
        ],
    )]
    #[OA\Response(
        response: 200,
        description: 'Success',
        content: new OA\JsonContent(
            properties: [
                new OA\Property('success', type: 'bool', example: true),
                new OA\Property('data', ref: '#/components/schemas/WithdrawalInfoDto', type: 'object'),
            ],
        ),
    )]
    #[OA\Response(ref: '#/components/responses/MissingData', response: 404)]
    #[OA\Response(ref: '#/components/responses/AuthenticationError', response: 401)]
    #[OA\Response(ref: '#/components/responses/ClientAccessError', response: 403)]
    #[OA\Response(ref: '#/components/responses/ConflictError', response: 409, description: 'Wrong account id')]
    public function info(
        int                    $accountId,
        GetWithdrawalInfoInput $getWithdrawalInfoService,
    ) : JsonResponse {
        /** @var GetWithdrawalInfoJsonResult $result */
        $result = $getWithdrawalInfoService->get(
            new GetWithdrawalInfoRequest($accountId),
        );

        return $result->response();
    }
}
