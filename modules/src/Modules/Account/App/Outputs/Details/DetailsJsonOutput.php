<?php

declare(strict_types=1);

namespace Platform\Account\App\Outputs\Details;

use Platform\Account\Domain\Dto\AccountDto;
use Platform\Package\Components\Http\Response\Code;
use Platform\Account\Domain\Services\Details\DetailsOutput;
use Platform\Account\App\Outputs\Details\Results\DetailsJsonResult;

class DetailsJsonOutput implements DetailsOutput
{
    public function output(AccountDto $account) : DetailsJsonResult
    {
        return new DetailsJsonResult($account);
    }

    public function accountNotFound() : DetailsJsonResult
    {
        return (new DetailsJsonResult())
            ->setCode(Code::ACCOUNT_NOT_FOUND)
            ->setHttStatus(404);
    }
}
