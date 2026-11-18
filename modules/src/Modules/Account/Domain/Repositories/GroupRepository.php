<?php

declare(strict_types=1);

namespace Platform\Account\Domain\Repositories;

use Platform\Account\Domain\Entities\Group;
use Platform\Account\Domain\Entities\SpecificGroupType;

interface GroupRepository
{
    public function findByCriteria(
        bool              $isDemo,
        int               $brandId,
        SpecificGroupType $specificType,
        int               $currencyId,
        int               $engineType,
        string            $type,
    ) : ?Group;

    public function find(int $id, bool $withType = false) : ?Group;

    public function findByName(string $name, bool $withType = false) : ?Group;

    public function add(
        string            $name,
        bool              $isDemo,
        int               $brandId,
        int               $currencyId,
        int               $typeId,
        SpecificGroupType $specificGroupType,
    ) : Group;

    public function getGroupOfAccount(int $accountId) : ?Group;

    public function getDefaultGroup() : Group;

    public function getGroupIds() : array;
}
