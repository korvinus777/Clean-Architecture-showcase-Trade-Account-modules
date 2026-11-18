<?php

declare(strict_types=1);

namespace Platform\Account\App\Outputs\SyncMtAccount;

use Platform\Account\App\Services\SyncMtAccount\SyncMtAccountResult;
use Platform\Account\App\Services\SyncMtAccount\SyncMtAccountOutput as IFetchMarginBalanceOutput;

class SyncMtAccountOutput implements IFetchMarginBalanceOutput
{
    public function synced(bool $synced, bool $balancesSynced) : SyncMtAccountResult
    {
        return new SyncMtAccountResult(
            synced: $synced,
            balancesSynced: $balancesSynced,
        );
    }

    public function deleted(bool $deleted) : SyncMtAccountResult
    {
        return new SyncMtAccountResult(
            synced: $deleted,
            deletingSynced: $deleted,
        );
    }
}
