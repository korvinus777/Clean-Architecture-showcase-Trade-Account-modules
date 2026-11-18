<?php

declare(strict_types=1);

namespace Platform\Account\Domain\Services\ParseGroupName;

use Platform\Account\Domain\Dto\GroupParamsDto;

interface ParseGroupNameOutput
{
    public function serviceGroup(string $group) : ParseGroupNameResult;

    public function parsed(GroupParamsDto $groupParams) : ParseGroupNameResult;
}
