<?php

declare(strict_types=1);

namespace Platform\Account\App\Dto;

use Platform\Account\Domain\Entities\Account;
use Platform\Account\Domain\Dto\MetatraderFullUserDto;

class AggregateMtAccountDto
{
    public function __construct(
        public Account                $account,
        public ?MetatraderFullUserDto $mtUser,
    ) {
    }
}
