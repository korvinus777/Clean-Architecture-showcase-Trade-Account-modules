<?php

declare(strict_types=1);

namespace Platform\Account\App\Services\SyncMtAccount;

interface SyncMtAccountOutput
{
    public function synced(bool $synced, bool $balancesSynced) : SyncMtAccountResult;

    public function deleted(bool $deleted) : SyncMtAccountResult;
}
