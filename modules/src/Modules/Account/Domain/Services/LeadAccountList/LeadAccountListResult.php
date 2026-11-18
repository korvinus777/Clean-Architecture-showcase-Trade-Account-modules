<?php

declare(strict_types=1);

namespace Platform\Account\Domain\Services\LeadAccountList;

use Platform\Account\Domain\Dto\AccountDto;

class LeadAccountListResult
{
    /**
     * @param AccountDto[] $accountList
     */
    public function __construct(
        public array $accountList,
    ) {
    }
}
