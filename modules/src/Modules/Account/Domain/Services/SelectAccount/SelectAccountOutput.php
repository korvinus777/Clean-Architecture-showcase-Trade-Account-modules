<?php

declare(strict_types=1);

namespace Platform\Account\Domain\Services\SelectAccount;

use Platform\Account\Domain\Dto\AccountDto;

interface SelectAccountOutput
{
    public function selected(AccountDto $account) : SelectAccountResult;

    public function notFound() : SelectAccountResult;

    public function failed() : SelectAccountResult;
}
