<?php

declare(strict_types=1);

namespace Platform\Account\Domain\Services\SelectAccount;

class SelectAccountRequest
{
    public function __construct(
        public int $leadId,
        public int $accountId,
    ) {
    }
}
