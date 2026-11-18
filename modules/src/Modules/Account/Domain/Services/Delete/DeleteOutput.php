<?php

declare(strict_types=1);

namespace Platform\Account\Domain\Services\Delete;

use Platform\Account\Domain\Entities\Account;

interface DeleteOutput
{
    public function accountNotFound(int $accountId) : DeleteResult;

    public function canNotDeleteOnMt(int $login) : DeleteResult;

    public function canNotDeleteFromDb(Account $account, \Throwable $throwable) : DeleteResult;

    public function deleted(Account $account) : DeleteResult;
}
