<?php

declare(strict_types=1);

namespace Platform\Account\App\Outputs\AddGroup;

use Platform\Account\Domain\Entities\Group;
use Platform\Account\Domain\Services\AddGroup\AddGroupResult;
use Platform\Account\Domain\Services\AddGroup\AddGroupOutput as IAddGroupOutput;

class AddGroupOutput implements IAddGroupOutput
{
    public function added(Group $group) : AddGroupResult
    {
        return new AddGroupResult($group);
    }

    public function unableToAdd(\Throwable $throwable) : AddGroupResult
    {
        return new AddGroupResult(throwable: $throwable);
    }
}
