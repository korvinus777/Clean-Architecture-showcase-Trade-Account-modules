<?php

declare(strict_types=1);

namespace Platform\Account\App\Services\SyncMtAccount;

class SyncMtAccountResult
{
    public function __construct(
        public bool $synced = false,
        public bool $balancesSynced = true,
        public bool $deletingSynced = true,
    ) {
    }
}
