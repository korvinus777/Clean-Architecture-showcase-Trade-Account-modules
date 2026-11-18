<?php

declare(strict_types=1);

namespace Platform\Account\Domain\Services\GetWithdrawalInfo;

interface GetWithdrawalInfoInput
{
    public function get(GetWithdrawalInfoRequest $request) : GetWithdrawalInfoResult;
}
