<?php

declare(strict_types=1);

namespace Platform\Account\Domain\Services\ParseGroupName;

interface ParseGroupNameInput
{
    public function parse(ParseGroupNameRequest $request) : ParseGroupNameResult;
}
