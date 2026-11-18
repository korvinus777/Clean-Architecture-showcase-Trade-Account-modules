<?php

declare(strict_types=1);

namespace Platform\Account\Domain\Adapters;

use Platform\Account\Domain\Dto\WithdrawalInfoDto;

interface CrmAdapter
{
    public function getWithdrawalInfo(int $accountId) : ?WithdrawalInfoDto;
}
