<?php

declare(strict_types=1);

namespace Platform\Account\Domain\Adapters;

use Platform\Account\Domain\Dto\NTUserDto;

interface NativeTradeEngineAdapter
{
    public function addUser(
        int    $leadHashId,
        int    $currencyId,
        int    $groupId,
        string $group,
        string $name,
        int    $leverage,
        bool   $enabled,
        bool   $tradable,
        bool   $isStopOutCloseEnabled,
    ) : ?NTUserDto;

    public function updateUser(
        int    $login,
        int    $currencyId,
        int    $groupId,
        string $group,
        string $name,
        int    $leverage,
        bool   $enabled,
        bool   $tradable,
        bool   $isStopOutCloseEnabled,
    ) : ?NTUserDto ;
}
