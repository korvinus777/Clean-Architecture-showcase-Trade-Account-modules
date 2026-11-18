<?php

declare(strict_types=1);

namespace Platform\Account\App\Services\SyncMtAccount;

interface SyncMtAccountInput
{
    public function sync(SyncMtAccountRequest $request) : SyncMtAccountResult;
}
