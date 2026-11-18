<?php

declare(strict_types=1);

namespace Platform\Account\Domain\Services\Details;

use Platform\Account\Domain\Dto\AccountDto;

interface DetailsOutput
{
    public function output(AccountDto $account) : DetailsResult;

    public function accountNotFound() : DetailsResult;
}
