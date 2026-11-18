<?php

declare(strict_types=1);

namespace Platform\Account\Domain\Services\AddGroup;

use Platform\Account\Domain\Entities\Group;

class AddGroupResult
{
    public function __construct(
        public ?Group      $group = null,
        public ?\Throwable $throwable = null,
    ) {
    }
}
