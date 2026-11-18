<?php

declare(strict_types=1);

namespace Platform\Account\App\Outputs\ParseGroupName;

use Platform\Account\Domain\Dto\GroupParamsDto;
use Platform\Account\Domain\Services\ParseGroupName\ParseGroupNameResult;
use Platform\Account\Domain\Services\ParseGroupName\ParseGroupNameOutput as IParseGroupNameOutput;

class ParseGroupNameOutput implements IParseGroupNameOutput
{
    public function serviceGroup(string $group) : ParseGroupNameResult
    {
        return new ParseGroupNameResult(isServiceGroup: true);
    }

    public function parsed(GroupParamsDto $groupParams) : ParseGroupNameResult
    {
        return new ParseGroupNameResult(groupParams: $groupParams);
    }
}
