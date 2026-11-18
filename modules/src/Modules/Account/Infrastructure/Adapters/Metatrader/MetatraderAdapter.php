<?php

declare(strict_types=1);

namespace Platform\Account\Infrastructure\Adapters\Metatrader;

use Platform\Metatrader\Api\Api;
use Platform\Metatrader\Api\Dto\UserDto;
use Platform\Account\Domain\Dto\MetatraderUserDto;
use Platform\Account\Domain\Dto\MetatraderAccountDto;
use Platform\Account\Domain\Dto\MetatraderFullUserDto;
use Platform\Account\Domain\Adapters\MetatraderAdapter as IMetatraderAdapter;

class MetatraderAdapter implements IMetatraderAdapter
{
    public function __construct(private Api $api)
    {
    }

    public function addUser(
        string $name,
        string $group,
        string $mainPassword,
        string $investorPassword,
        int    $leverage,
        bool   $enabled,
        bool   $tradable,
    ) : ?MetatraderUserDto {
        $user = $this->api->addUser(
            $name,
            $group,
            $mainPassword,
            $investorPassword,
            $leverage,
            $enabled,
            $tradable,
        );

        return $user ? $this->createMtUserDto($user) : null;
    }

    public function updateUser(
        int     $login,
        ?string $name,
        ?string $group,
        ?int    $leverage,
        ?bool   $enabled,
        ?bool   $tradable,
    ) : ?MetatraderUserDto {
        $user = $this->api->updateUser(
            $login,
            $name,
            $group,
            $leverage,
            $enabled,
            $tradable,
        );

        return $user ? $this->createMtUserDto($user) : null;
    }

    public function changeMainPassword(int $login, string $password) : bool
    {
        return $this->api->changeMainPassword($login, $password);
    }

    public function changeInvestorPassword(int $login, string $password) : bool
    {
        return $this->api->changeInvestorPassword($login, $password);
    }

    public function deleteUser(int $login) : bool
    {
        return $this->api->deleteUser($login);
    }

    public function getAccount(int $login) : ?MetatraderAccountDto
    {
        $account = $this->api->getAccount($login);

        if ( ! $account) {
            return null;
        }

        return new MetatraderAccountDto(
            $account->login,
            $account->balance,
            $account->credit,
            $account->margin,
            $account->leverage,
        );
    }

    public function getFullUsers(array $logins) : ?array
    {
        $users = $this->api->getFullUsers($logins);

        if (is_null($users)) {
            return null;
        }

        $result = [];

        foreach ($users as $user) {
            $result[$user->login] = new MetatraderFullUserDto(
                login: $user->login,
                name: $user->name,
                group: $user->group,
                leverage: $user->leverage,
                balance: $user->balance,
                credit: $user->credit,
                enabled: $user->enabled,
                tradable: $user->tradable,
                hasAccount: $user->hasAccount,
                margin: $user->margin,
            );
        }

        return $result;
    }

    public function deferredUpdateUser(
        int     $login,
        ?string $name = '',
        ?string $group = '',
        ?int    $leverage = 0,
        ?bool   $enabled = null,
        ?bool   $tradable = null,
    ) : void {
        $this->api->deferredUpdateUser(
            login: $login,
            name: $name,
            group: $group,
            leverage: $leverage,
            enabled: $enabled,
            tradable: $tradable,
        );
    }

    private function createMtUserDto(UserDto $user) : MetatraderUserDto
    {
        return new MetatraderUserDto(
            $user->login,
            $user->name,
            $user->group,
            $user->leverage,
            $user->enabled,
            $user->tradable,
        );
    }
}
