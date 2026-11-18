<?php

declare(strict_types=1);

namespace Platform\Account\Domain\Services\GetWithdrawalInfo;

class GetWithdrawalInfoRequest
{
    public function __construct(
        public int $accountId,
    ) {
    }
}
