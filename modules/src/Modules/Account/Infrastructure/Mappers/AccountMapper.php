<?php

declare(strict_types=1);

namespace Platform\Account\Infrastructure\Mappers;

use Platform\Account\Domain\Entities\Account;
use Platform\Account\Infrastructure\Models\AccountModel;

class AccountMapper
{
    /**
     * @param AccountModel $accountModel
     * @return Account
     */
    public static function entityFromModel(AccountModel $accountModel) : Account
    {
        $account = new Account(
            id: $accountModel->id,
            leadId: $accountModel->leadId,
            login: $accountModel->login,
            groupId: $accountModel->groupId,
            leverage: $accountModel->leverage,
            balance: $accountModel->balance,
            balanceSystem: $accountModel->balanceSystem,
            credit: $accountModel->credit,
            creditSystem: $accountModel->creditSystem,
            margin: $accountModel->margin,
            marginSystem: $accountModel->marginSystem,
            enabled: $accountModel->isEnabled,
            tradable: $accountModel->isTradable,
            selected: $accountModel->isSelected,
            marginCall: $accountModel->isMarginCall,
            stopOut: $accountModel->isStopOut,
            isStopOutCloseEnabled: $accountModel->isStopOutCloseEnabled,
        );

        if ($accountModel->relationLoaded('group') && $accountModel->group) {
            $account->group = GroupMapper::entityFromModel($accountModel->group);
        }

        return $account;
    }
}
