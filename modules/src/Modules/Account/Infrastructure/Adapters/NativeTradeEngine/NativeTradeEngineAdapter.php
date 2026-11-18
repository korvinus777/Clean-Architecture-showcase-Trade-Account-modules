<?php

declare(strict_types=1);

namespace Platform\Account\Infrastructure\Adapters\NativeTradeEngine;

use Platform\NativeTradeEngine\Api\Api;
use Platform\Account\Domain\Dto\NTUserDto;
use Platform\NativeTradeEngine\Api\Dto\UserDto;
use Platform\Account\Domain\Adapters\NativeTradeEngineAdapter as INativeTradeEngineAdapter;

class NativeTradeEngineAdapter implements INativeTradeEngineAdapter
{
    public function __construct(private Api $api)
    {
    }

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
    ) : ?NTUserDto {
        $user = $this->api->addUser(
            $leadHashId,
            $currencyId,
            $groupId,
            $group,
            $name,
            $leverage,
            $enabled,
            $tradable,
            $isStopOutCloseEnabled,
        );

        return $user ? $this->createNtUserDto($user) : null;
    }

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
    ) : ?NTUserDto {
        $user = $this->api->updateUser(
            login: $login,
            currencyId: $currencyId,
            groupId: $groupId,
            group: $group,
            name: $name,
            leverage: $leverage,
            enabled: $enabled,
            tradable: $tradable,
            isStopOutCloseEnabled: $isStopOutCloseEnabled,
        );

        return $user ? $this->createNtUserDto($user) : null;
    }


    private function createNtUserDto(UserDto $user) : NTUserDto
    {
        return new NTUserDto(
            $user->login,
        );
    }
}
