<?php

declare(strict_types=1);

namespace Platform\Account\Infrastructure\Repositories;

use Platform\Account\Domain\Entities\Group;
use Platform\Account\Infrastructure\Models\AccountType;
use Platform\Account\Domain\Repositories\GroupTypeRepository as IGroupTypeRepository;

class GroupTypeRepository implements IGroupTypeRepository
{
    public function findNameById(int $id) : ?string
    {
        /** @var string|null $typeName */
        $typeName = AccountType::query()->where('id', $id)->value('name');

        return is_string($typeName) ? $typeName : null;
    }

    public function findIdByName(string $name) : ?int
    {
        /** @var AccountType|null $type */
        $type = AccountType::query()->where('name', $name)->first();

        return $type?->id;
    }

    public function getCustomId() : int
    {
        /** @var AccountType|null $customType */
        $customType = AccountType::query()->where('name', Group::CUSTOM_TYPE)->first();

        return intval($customType?->id);
    }
}
