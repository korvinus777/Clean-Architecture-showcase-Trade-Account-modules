<?php

declare(strict_types=1);

namespace Platform\Account\Domain\Services\SelectAccount;

use Platform\Account\Domain\Dto\AccountDto;

class SelectAccountResult
{
    public function __construct(
        public ?AccountDto $account = null,
    ) {
    }
}
