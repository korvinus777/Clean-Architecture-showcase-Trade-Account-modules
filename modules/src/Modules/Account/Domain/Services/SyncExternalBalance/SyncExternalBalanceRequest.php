<?php

declare(strict_types=1);

namespace Platform\Account\Domain\Services\SyncExternalBalance;

class SyncExternalBalanceRequest
{
    public function __construct(
        public int $login,
        public float $balance,
        public float $credit,
    ) {

    }
}
