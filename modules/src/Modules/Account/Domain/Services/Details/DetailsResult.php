<?php

declare(strict_types=1);

namespace Platform\Account\Domain\Services\Details;

use Platform\Account\Domain\Dto\AccountDto;

class DetailsResult
{
    public function __construct(
        public ?AccountDto $account = null,
    ) {
    }
}
