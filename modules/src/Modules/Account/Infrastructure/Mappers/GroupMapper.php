<?php

declare(strict_types=1);

namespace Platform\Account\Infrastructure\Mappers;

use Platform\Account\Domain\Entities\Group;
use Platform\Account\Infrastructure\Models\AccountGroupModel;

class GroupMapper
{
    /**
     * @param AccountGroupModel $accountGroup
     * @return Group
     */
    public static function entityFromModel(AccountGroupModel $accountGroup) : Group
    {
        $group = new Group(
            $accountGroup->id,
            $accountGroup->name,
            $accountGroup->accountTypeId,
            $accountGroup->brandId,
            $accountGroup->isDemo,
            $accountGroup->specificTypeId,
            $accountGroup->currencyId,
            $accountGroup->isNoSwap,
            $accountGroup->engineType,
        );

        if ($accountGroup->relationLoaded('type')) {
            $group->typeName = $accountGroup->type?->name;
        }

        return $group;
    }
}
