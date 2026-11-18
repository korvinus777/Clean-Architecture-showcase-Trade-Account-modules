<?php

declare(strict_types=1);

namespace Platform\Account\Api\Dto;

use Platform\Account\Domain\Entities\Group as GroupEntity;

class GroupDto
{
    public function __construct(
        public int    $id,
        public string $name,
        public int    $typeId,
        public int    $brandId,
        public bool   $isDemo,
        public int    $specificType,
        public int    $currencyId,
        public bool   $isNoSwap,
        public int    $engineType,
        public string $typeName,
    ) {
    }

    public static function fromEntity(GroupEntity $group) : self
    {
        return new self(
            $group->id,
            $group->name,
            $group->typeId,
            $group->brandId,
            $group->isDemo,
            $group->specificType->value,
            $group->currencyId,
            $group->isNoSwap,
            $group->engineType,
            (string) $group->typeName,
        );
    }
}
