<?php

declare(strict_types=1);

namespace Platform\Account\App\Outputs\SelectAccount;

use Platform\Account\Domain\Dto\AccountDto;
use Platform\Package\Components\Http\Response\Code;
use Platform\Account\Domain\Services\SelectAccount\SelectAccountOutput;
use Platform\Account\App\Outputs\SelectAccount\Results\SelectAccountJsonResult;

class SelectAccountJsonOutput implements SelectAccountOutput
{
    public function selected(AccountDto $account) : SelectAccountJsonResult
    {
        return (new SelectAccountJsonResult($account));
    }

    public function notFound() : SelectAccountJsonResult
    {
        return (new SelectAccountJsonResult())
            ->setCode(Code::ACCOUNT_NOT_FOUND)
            ->setHttStatus(404);
    }

    public function failed() : SelectAccountJsonResult
    {
        return (new SelectAccountJsonResult())
            ->setCode(Code::ACCOUNT_UPDATE_ERROR)
            ->setHttStatus(500);
    }
}
