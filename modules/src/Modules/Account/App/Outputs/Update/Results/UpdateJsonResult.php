<?php

declare(strict_types=1);

namespace Platform\Account\App\Outputs\Update\Results;

use Platform\Package\Components\Http\Response\Code;
use Platform\Account\Domain\Services\Update\UpdateResult;
use Platform\Package\Components\Http\Response\JsonResponse;

class UpdateJsonResult extends UpdateResult
{
    public function __construct(public int $httpStatus, Code $code)
    {
        parent::__construct($code);
    }

    public function response() : JsonResponse
    {
        return JsonResponse::withCode($this->code, [], $this->httpStatus);
    }
}
