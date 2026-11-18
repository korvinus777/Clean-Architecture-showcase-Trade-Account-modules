<?php

declare(strict_types=1);

namespace Platform\Account\App\Outputs\Create;

use Platform\Account\Domain\Entities\Account;
use Platform\Package\Components\Http\Response\Code;
use Platform\Account\Domain\Services\Create\CreateOutput;
use Platform\Account\Domain\Services\Create\CreateResult;
use Platform\Account\App\Outputs\Create\Results\CreateJsonResult;

class CreateJsonOutput implements CreateOutput
{
    public function leadNotFound(Code $code) : CreateResult
    {
        return new CreateJsonResult(404, $code);
    }

    public function currencyNotFound(Code $code) : CreateJsonResult
    {
        return new CreateJsonResult(404, $code);
    }

    public function groupNotFound(Code $code) : CreateJsonResult
    {
        return new CreateJsonResult(404, $code);
    }

    public function engineTypeNotFound(Code $code) : CreateJsonResult
    {
        return new CreateJsonResult(503, $code);
    }

    public function metatraderError(Code $code) : CreateJsonResult
    {
        return new CreateJsonResult(503, $code);
    }

    public function created(Code $code, Account $account) : CreateJsonResult
    {
        return new CreateJsonResult(201, $code, $account);
    }

    public function canNotCreate(Code $code) : CreateJsonResult
    {
        return new CreateJsonResult(500, $code);
    }
}
