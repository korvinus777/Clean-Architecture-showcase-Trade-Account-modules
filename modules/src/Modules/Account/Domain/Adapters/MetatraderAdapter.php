<?php

declare(strict_types=1);

namespace Platform\Account\Domain\Adapters;

use Platform\Account\Domain\Dto\MetatraderUserDto;
use Platform\Account\Domain\Dto\MetatraderAccountDto;
use Platform\Account\Domain\Dto\MetatraderFullUserDto;

interface MetatraderAdapter
{
    public function addUser(
        string $name,
        string $group,
        string $mainPassword,
        string $investorPassword,
        int    $leverage,
        bool   $enabled,
        bool   $tradable,
    ) : ?MetatraderUserDto;

    public function updateUser(
        int     $login,
        ?string $name,
        ?string $group,
        ?int    $leverage,
        ?bool   $enabled,
        ?bool   $tradable,
    ) : ?MetatraderUserDto;

    public function changeMainPassword(int $login, string $password) : bool;

    public function changeInvestorPassword(int $login, string $password) : bool;

    public function deleteUser(int $login) : bool;

    public function getAccount(int $login) : ?MetatraderAccountDto;

    /**
     * @param int[] $logins
     * @return array<int, MetatraderFullUserDto>|null
     */
    public function getFullUsers(array $logins) : ?array;

    public function deferredUpdateUser(
        int     $login,
        ?string $name = '',
        ?string $group = '',
        ?int    $leverage = 0,
        ?bool   $enabled = null,
        ?bool   $tradable = null,
    ) : void;
}
