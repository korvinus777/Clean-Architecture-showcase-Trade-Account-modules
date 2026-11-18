<?php

declare(strict_types=1);

namespace App\Http\Controllers\Account;

use OpenApi\Attributes as OA;
use Platform\Account\Domain\Services\Delete\DeleteInput;
use Platform\Account\Domain\Services\Delete\DeleteRequest;
use Platform\Package\Components\Http\Response\JsonResponse;
use Platform\Account\App\Outputs\Delete\Results\DeleteJsonResult;

class DeleteController
{
    public function __construct(private DeleteInput $deleteService)
    {
    }

    #[OA\Delete(
        path: '/account/{accountId}',
        summary: 'Deletes account',
        security: [['CRM' => []]],
        tags: ['Account'],
        parameters: [new OA\Parameter(
            name: 'accountId',
            in: 'path',
            required: true,
        )],
    )]
    #[OA\Response(response: 200, description: 'Account successfully deleted')]
    #[OA\Response(ref: '#/components/responses/ClientAccessError', response: 403)]
    #[OA\Response(response: 500, description: 'Unexpected server error')]
    #[OA\Response(response: 503, description: 'Error on metatrader side')]
    #[OA\Response(response: 404, description: 'Account not found')]
    public function __invoke(int $accountId) : JsonResponse
    {
        /** @var DeleteJsonResult $result */
        $result = $this->deleteService->delete(new DeleteRequest(accountId: $accountId));

        return $result->response();
    }
}
