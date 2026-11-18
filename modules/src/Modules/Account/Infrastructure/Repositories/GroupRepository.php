<?php

declare(strict_types=1);

namespace Platform\Account\Infrastructure\Repositories;

use Illuminate\Database\Eloquent\Builder;
use Platform\Account\Domain\Entities\Group;
use Platform\Account\Domain\Entities\SpecificGroupType;
use Platform\Account\Infrastructure\Mappers\GroupMapper;
use Platform\Account\Infrastructure\Models\AccountGroupModel;
use Platform\Account\Domain\Repositories\GroupRepository as IGroupRepository;

class GroupRepository implements IGroupRepository
{
    public function findByCriteria(
        bool              $isDemo,
        int               $brandId,
        SpecificGroupType $specificType,
        int               $currencyId,
        int               $engineType,
        string            $type,
    ) : ?Group {
        /** @var AccountGroupModel|null $accountGroup */
        $accountGroup = AccountGroupModel::query()
            ->join('accountsTypes as at', 'at.id', '=', 'accountsGroups.accountTypeId')
            ->where([
                'accountsGroups.isDemo'         => intval($isDemo),
                'accountsGroups.brandId'        => $brandId,
                'accountsGroups.specificTypeId' => $specificType->value,
                'accountsGroups.currencyId'     => $currencyId,
                'accountsGroups.engineType'     => $engineType,
                'at.name'                       => $type,
            ])
            ->first(['accountsGroups.*']);

        return $accountGroup ? GroupMapper::entityFromModel($accountGroup) : null;
    }

    public function find(int $id, bool $withType = false) : ?Group
    {
        /** @var AccountGroupModel|null $accountGroup */
        $accountGroup = AccountGroupModel::query()
            ->when($withType, function (Builder $builder) {
                /** @uses AccountGroupModel::type() */
                $builder->with('type');
            })
            ->whereKey($id)
            ->first();

        return $accountGroup ? GroupMapper::entityFromModel($accountGroup) : null;
    }

    public function findByName(string $name, bool $withType = false) : ?Group
    {
        /** @var AccountGroupModel|null $accountGroup */
        $accountGroup = AccountGroupModel::query()
            ->when($withType, function (Builder $builder) {
                /** @uses AccountGroupModel::type() */
                $builder->with('type');
            })
            ->where('name', $name)
            ->first();

        return $accountGroup ? GroupMapper::entityFromModel($accountGroup) : null;
    }

    public function add(
        string            $name,
        bool              $isDemo,
        int               $brandId,
        int               $currencyId,
        int               $typeId,
        SpecificGroupType $specificGroupType,
        bool              $isNoSwap = false,
        int               $engineType = 0,
    ) : Group {
        /** @var AccountGroupModel $accountGroup */
        $accountGroup = AccountGroupModel::query()->create([
            'name'           => $name,
            'isDemo'         => $isDemo,
            'brandId'        => $brandId,
            'currencyId'     => $currencyId,
            'accountTypeId'  => $typeId,
            'specificTypeId' => $specificGroupType,
            'isNoSwap'       => $isNoSwap,
            'engineType'     => $engineType,
        ]);

        return GroupMapper::entityFromModel($accountGroup);
    }

    public function getGroupOfAccount(int $accountId) : ?Group
    {
        /** @var AccountGroupModel|null $model */
        $model = AccountGroupModel::query()
            /** @uses AccountGroupModel::accounts() */
            ->whereRelation('accounts', 'id', '=', $accountId)
            ->first();

        if ( ! $model) {
            return null;
        }

        return GroupMapper::entityFromModel($model);
    }

    public function getDefaultGroup() : Group
    {
        /** @var AccountGroupModel $model */
        $model = AccountGroupModel::query()->where('isDefault', true)->first();

        return GroupMapper::entityFromModel($model);
    }

    public function getGroupIds() : array
    {
        return AccountGroupModel::all()->pluck('id')->all();
    }
}
