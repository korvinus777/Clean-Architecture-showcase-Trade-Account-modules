<?php

declare(strict_types=1);

namespace Platform\Account\Domain\Services\AddGroup;

use Platform\Account\Domain\Entities\Group;

interface AddGroupOutput
{
    public function added(Group $group) : AddGroupResult;

    public function unableToAdd(\Throwable $throwable) : AddGroupResult;
}
