<?php

declare(strict_types=1);

namespace Platform\Account\App\Outputs\Delete\Results;

use Platform\Account\Domain\Entities\Account;
use Platform\Package\Components\Http\Response\Code;
use Platform\Account\Domain\Services\Delete\DeleteResult;
use Platform\Package\Components\Http\Response\JsonResponse;

class DeleteJsonResult extends DeleteResult
{
    public function __construct(
        public Code $code,
        public int  $httpStatus,
        ?\Throwable $throwable = null,
        ?Account    $account = null,
        ?int        $accountId = null,
        ?int        $login = null,
    ) {
        parent::__construct($throwable, $account, $accountId, $login);
    }

    public function response() : JsonResponse
    {
        return JsonResponse::withCode($this->code, [], $this->httpStatus);
    }
}
