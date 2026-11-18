<?php

declare(strict_types=1);

namespace Platform\Account\App\Outputs\Create\Results;

use OpenApi\Attributes as OA;
use Platform\Account\Domain\Entities\Account;
use Platform\Package\Components\Http\Response\Code;
use Platform\Account\Domain\Services\Create\CreateResult;
use Platform\Package\Components\Http\Response\JsonResponse;

class CreateJsonResult extends CreateResult
{
    public function __construct(public int $httpStatus, Code $code, ?Account $account = null)
    {
        parent::__construct($code, $account);
    }

    #[OA\Schema(
        schema: 'CreatedAccount',
        properties: [
            new OA\Property('id', type: 'int', example: 1),
            new OA\Property('login', type: 'int', example: 1000),
            new OA\Property('leverage', type: 'int', example: 100),
            new OA\Property('enabled', type: 'bool', example: true),
            new OA\Property('tradable', type: 'bool', example: true),
        ],
    )]
    public function response() : JsonResponse
    {
        $data = ! $this->account ? [] : [
            'id'       => $this->account->id,
            'login'    => $this->account->login,
            'leverage' => $this->account->leverage,
            'enabled'  => $this->account->enabled,
            'tradable' => $this->account->tradable,
        ];

        return JsonResponse::withCode($this->code, $data, $this->httpStatus);
    }
}
