<?php

declare(strict_types=1);

namespace Platform\Account\Domain\Services\AddGroup;

use Platform\Account\Domain\Entities\SpecificGroupType;

class AddGroupRequest
{
    public function __construct(
        public string             $name,
        public ?bool              $isDemo = null,
        public null|int|string    $brand = null,
        public null|int|string    $type = null,
        public null|int|string    $currency = null,
        public ?int               $currencyPrecision = null,
        public ?SpecificGroupType $specificGroupType = null,
    ) {
    }
}
