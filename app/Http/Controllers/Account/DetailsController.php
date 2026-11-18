<?php

declare(strict_types=1);

namespace App\Http\Controllers\Account;

use OpenApi\Attributes as OA;
use Platform\Account\Domain\Services\Details\DetailsInput;
use Platform\Package\Components\Http\Response\JsonResponse;
use Platform\Account\Domain\Services\Details\DetailsRequest;
use Platform\Account\App\Outputs\Details\Results\DetailsJsonResult;

class DetailsController
{
    public function __construct(
        private DetailsInput $detailsService,
    ) {
    }

    /**
     * @param int $id
     * @return JsonResponse
     */
    #[OA\Get(
        path: '/account/{id}',
        summary: 'Returns account details',
        security: [['CRM' => []]],
        tags: ['Account'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true),
        ],
    )]
    #[OA\Response(
        response: 200,
        description: 'Success',
        content: new OA\JsonContent(
            properties: [
                new OA\Property('success', type: 'bool', example: true),
                new OA\Property('data', ref: '#/components/schemas/AccountDto', type: 'object'),
            ],
        ),
    )]
    #[OA\Response(ref: '#/components/responses/AuthenticationError', response: 401)]
    #[OA\Response(ref: '#/components/responses/ClientAccessError', response: 403)]
    public function __invoke(int $id) : JsonResponse
    {
        /** @var DetailsJsonResult $result */
        $result = $this->detailsService->get(
            new DetailsRequest($id),
        );

        return $result->response();
    }
}
