<?php

declare(strict_types=1);

namespace App\Http\Controllers\Account;

use OpenApi\Attributes as OA;
use App\Http\Requests\Account\UpdateRequest;
use Platform\Account\Domain\Services\Update\UpdateInput;
use Platform\Package\Components\Http\Response\JsonResponse;
use Platform\Account\App\Outputs\Update\Results\UpdateJsonResult;

class UpdateController
{
    public function __construct(private UpdateInput $updateService)
    {
    }

    #[OA\Put(
        path: '/account/{accountId}',
        summary: 'Updates account',
        security: [['CRM' => []]],
        requestBody: new OA\RequestBody(ref: '#/components/requestBodies/UpdateAccount'),
        tags: ['Account'],
        parameters: [new OA\Parameter(
            name: 'accountId',
            in: 'path',
            required: true,
        )],
    )]
    #[OA\Response(response: 200, description: 'Account successfully updated')]
    #[OA\Response(response: 400, description: 'Invalid data')]
    #[OA\Response(ref: '#/components/responses/ValidationError', response: 422)]
    #[OA\Response(ref: '#/components/responses/ClientAccessError', response: 403)]
    #[OA\Response(response: 500, description: 'Unexpected server error')]
    #[OA\Response(response: 503, description: 'Error on metatrader side')]
    #[OA\Response(
        response: 404,
        description: 'Group not found',
        content: new OA\JsonContent(
            properties: [
                new OA\Property('success', type: 'bool', example: false),
                new OA\Property('error', type: 'string', example: 'ACCOUNT_GROUP_NOT_FOUND'),
                new OA\Property('code', type: 'int', example: 12002),
                new OA\Property('message', type: 'string', example: 'Group not found'),
            ],
        ),
    )]
    public function __invoke(UpdateRequest $request, int $accountId) : JsonResponse
    {
        /** @var UpdateJsonResult $result */
        $result = $this->updateService->update($request->domainRequest($accountId));

        return $result->response();
    }
}
