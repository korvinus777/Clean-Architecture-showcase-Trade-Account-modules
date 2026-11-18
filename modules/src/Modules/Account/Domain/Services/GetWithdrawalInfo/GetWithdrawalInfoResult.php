<?php

declare(strict_types=1);

namespace Platform\Account\Domain\Services\GetWithdrawalInfo;

use Platform\Account\Domain\Dto\WithdrawalInfoDto;

class GetWithdrawalInfoResult
{
    public function __construct(
        public ?WithdrawalInfoDto $withdrawalInfoDto = null,
    ) {
    }
}
