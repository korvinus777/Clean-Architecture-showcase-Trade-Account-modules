<?php

declare(strict_types=1);

namespace App\Http\Controllers\Account;

use OpenApi\Attributes as OA;
use Platform\Package\Components\Http\Request\Request;
use Platform\Package\Components\Http\Response\JsonResponse;
use Platform\Account\Domain\Services\LeadAccountList\LeadAccountListInput;
use Platform\Account\Domain\Services\LeadAccountList\LeadAccountListRequest;
use Platform\Account\App\Outputs\LeadAccountList\Results\LeadAccountListJsonResult;

class LeadAccountsController
{
    public function __construct(
        private LeadAccountListInput $leadAccountListService,
    ) {
    }

    /**
     * @return JsonResponse
     */
    #[OA\Get(
        path: '/account/lead',
        summary: 'Returns lead\'s accounts',
        security: [['Lead' => []], ['CRM' => []]],
        tags: ['Account'],
        parameters: [
            new OA\Parameter(name: 'leadHashId', in: 'query', required: false),
        ],
    )]
    #[OA\Response(
        response: 200,
        description: 'Success',
        content: new OA\JsonContent(
            properties: [
                new OA\Property('success', type: 'bool', example: true),
                new OA\Property('data', type: 'array', items: new OA\Items('#/components/schemas/AccountDto')),
            ],
        ),
    )]
    #[OA\Response(ref: '#/components/responses/AuthenticationError', response: 401)]
    #[OA\Response(ref: '#/components/responses/ClientAccessError', response: 403)]
    public function __invoke() : JsonResponse
    {
        $leadHashId = Request::lead()->leadHashId ?? Request::getInt('leadHashId');

        /** @var LeadAccountListJsonResult $result */
        $result = $this->leadAccountListService->get(
            new LeadAccountListRequest(
                leadId: Request::lead()->id,
                leadHashId: $leadHashId,
            ),
        );

        return $result->response();
    }
}
