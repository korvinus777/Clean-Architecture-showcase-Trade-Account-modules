<?php

declare(strict_types=1);

namespace App\Http\Controllers\Account;

use OpenApi\Attributes as OA;
use Platform\Package\Components\Http\Request\Request;
use Platform\Package\Components\Http\Response\JsonResponse;
use Platform\Account\Domain\Services\SelectAccount\SelectAccountInput;
use Platform\Account\Domain\Services\SelectAccount\SelectAccountRequest;
use Platform\Account\App\Outputs\SelectAccount\Results\SelectAccountJsonResult;

class SelectController
{
    public function __construct(
        private SelectAccountInput $selectAccountService,
    ) {
    }

    #[OA\Post(
        path: '/account/{accountId}/select',
        summary: 'Select account',
        security: [['Lead' => []]],
        tags: ['Account'],
        parameters: [new OA\Parameter(
            name: 'accountId',
            in: 'path',
            required: true,
        )],
    )]
    #[OA\Response(response: 200, description: 'Account successfully selected')]
    #[OA\Response(ref: '#/components/responses/AuthenticationError', response: 401)]
    #[OA\Response(response: 500, description: 'Unexpected server error')]
    #[OA\Response(ref: '#/components/responses/ConflictError', response: 409, description: 'Wrong account id')]
    public function __invoke(int $accountId) : JsonResponse
    {
        /** @var SelectAccountJsonResult $result */
        $result = $this->selectAccountService->select($this->domainRequest($accountId));

        return $result->response();
    }

    private function domainRequest(int $accountId) : SelectAccountRequest
    {
        return new SelectAccountRequest(Request::lead()->id, $accountId);
    }
}
