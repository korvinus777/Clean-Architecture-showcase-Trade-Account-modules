<?php

declare(strict_types=1);

namespace Platform\Account\App\Services\UpdateMtAccount;

class UpdateMtAccountResult
{
    public function __construct(
        public bool $success = false,
    ) {
    }
}
