<?php

declare(strict_types=1);

namespace Platform\Account\Api\Dto;

use Platform\Account\Domain\Entities\Account;

class AccountDto
{
    public function __construct(
        public int    $accountId,
        public int    $leadId,
        public int    $login,
        public int    $leverage,
        public float  $balance,
        public float  $balanceSystem,
        public float  $credit,
        public float  $creditSystem,
        public float  $margin,
        public float  $marginSystem,
        public bool   $enabled,
        public bool   $tradable,
        public bool   $selected,
        public int    $groupId,
        public string $groupName,
        public int    $typeId,
        public int    $brandId,
        public bool   $isDemo,
        public int    $currencyId,
        public int    $specificType,
    ) {
    }

    public static function fromEntity(Account $account) : self
    {
        return new self(
            accountId: $account->id,
            leadId: $account->leadId,
            login: $account->login,
            leverage: $account->leverage,
            balance: $account->balance,
            balanceSystem: $account->balanceSystem,
            credit: $account->credit,
            creditSystem: $account->creditSystem,
            margin: $account->margin,
            marginSystem: $account->marginSystem,
            enabled: $account->enabled,
            tradable: $account->tradable,
            selected: $account->selected,
            groupId: $account->groupId,
            groupName: (string) $account->group?->name,
            typeId: (int) $account->group?->typeId,
            brandId: (int) $account->group?->brandId,
            isDemo: (bool) $account->group?->isDemo,
            currencyId: (int) $account->group?->currencyId,
            specificType: (int) $account->group?->specificType->value,
        );
    }
}
