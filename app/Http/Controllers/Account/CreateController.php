<?php

declare(strict_types=1);

namespace App\Http\Controllers\Account;

use OpenApi\Attributes as OA;
use App\Http\Requests\Account\CreateRequest;
use Platform\Account\Domain\Services\Create\CreateInput;
use Platform\Package\Components\Http\Response\JsonResponse;
use Platform\Account\App\Outputs\Create\Results\CreateJsonResult;

class CreateController
{
    public function __construct(private CreateInput $createService)
    {
    }

    #[OA\Post(
        path: '/account',
        summary: 'Creates account',
        security: [['CRM' => []]],
        requestBody: new OA\RequestBody(ref: '#/components/requestBodies/CreateAccount'),
        tags: ['Account'],
    )]
    #[OA\Response(
        response: 201,
        description: 'Account successfully created',
        content: new OA\JsonContent(
            properties: [
                new OA\Property('success', type: 'bool', example: true),
                new OA\Property('data', '#/components/schemas/CreatedAccount'),
            ],
        ),
    )]
    #[OA\Response(ref: '#/components/responses/ValidationError', response: 422)]
    #[OA\Response(ref: '#/components/responses/ClientAccessError', response: 403)]
    #[OA\Response(response: 500, description: 'Unexpected server error')]
    #[OA\Response(response: 503, description: 'Error on metatrader side')]
    #[OA\Response(
        response: 404,
        description: 'Some entity not found. Details in response',
        content: new OA\JsonContent(
            properties: [
                new OA\Property('success', type: 'bool', example: false),
                new OA\Property('error', type: 'string', example: 'ACCOUNT_GROUP_NOT_FOUND'),
                new OA\Property('code', type: 'int', example: 12002),
                new OA\Property('message', type: 'string', example: 'Group not found'),
            ],
        ),
    )]
    public function __invoke(CreateRequest $request) : JsonResponse
    {
        /** @var CreateJsonResult $result */
        $result = $this->createService->create($request->domainRequest());

        return $result->response();
    }
}
