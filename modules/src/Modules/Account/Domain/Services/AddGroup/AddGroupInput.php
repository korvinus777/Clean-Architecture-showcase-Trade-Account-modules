<?php

declare(strict_types=1);

namespace Platform\Account\Domain\Services\AddGroup;

interface AddGroupInput
{
    public function add(AddGroupRequest $request) : AddGroupResult;
}
