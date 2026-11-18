<?php

declare(strict_types=1);

namespace Platform\Account\Domain\Services\GetWithdrawalInfo;

use Platform\Account\Domain\Dto\WithdrawalInfoDto;

interface GetWithdrawalInfoOutput
{
    public function success(WithdrawalInfoDto $withdrawalInfoDto) : GetWithdrawalInfoResult;

    public function missingData() : GetWithdrawalInfoResult;

    public function unexpectedError() : GetWithdrawalInfoResult;

    public function withdrawalBlocked() : GetWithdrawalInfoResult;
}
