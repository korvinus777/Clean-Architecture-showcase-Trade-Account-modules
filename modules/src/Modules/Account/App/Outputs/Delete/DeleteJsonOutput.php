<?php

declare(strict_types=1);

namespace Platform\Account\App\Outputs\Delete;

use Platform\Account\Domain\Entities\Account;
use Platform\Package\Components\Http\Response\Code;
use Platform\Account\Domain\Services\Delete\DeleteOutput;
use Platform\Account\App\Outputs\Delete\Results\DeleteJsonResult;

class DeleteJsonOutput implements DeleteOutput
{
    public function accountNotFound(int $accountId) : DeleteJsonResult
    {
        return new DeleteJsonResult(Code::ACCOUNT_NOT_FOUND, 404, accountId: $accountId);
    }

    public function canNotDeleteOnMt(int $login) : DeleteJsonResult
    {
        return new DeleteJsonResult(Code::MT_DELETE_USER_ERROR, 503, login: $login);
    }

    public function canNotDeleteFromDb(Account $account, \Throwable $throwable) : DeleteJsonResult
    {
        return new DeleteJsonResult(Code::ACCOUNT_DELETE_ERROR, 500, throwable: $throwable, account: $account);
    }

    public function deleted(Account $account) : DeleteJsonResult
    {
        return new DeleteJsonResult(Code::SUCCESS, 200, account: $account);
    }
}
