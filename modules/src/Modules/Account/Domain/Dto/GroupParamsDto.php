<?php

declare(strict_types=1);

namespace Platform\Account\Domain\Dto;

use Platform\Account\Domain\Entities\SpecificGroupType;

class GroupParamsDto
{
    public function __construct(
        public bool              $isDemo = false,
        public int               $brandId = 0,
        public int               $currencyId = 0,
        public int               $typeId = 0,
        public SpecificGroupType $specificGroupType = SpecificGroupType::REGULAR,
    ) {
    }
}
