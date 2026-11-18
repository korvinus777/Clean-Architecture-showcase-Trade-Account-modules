<?php

declare(strict_types=1);

namespace Platform\Account\Domain\Services\SyncExternalBalance;

interface SyncExternalBalanceInput
{
    /**
     * @param SyncExternalBalanceRequest $request
     * @return bool
     */
    public function sync(SyncExternalBalanceRequest $request) : bool;
}
