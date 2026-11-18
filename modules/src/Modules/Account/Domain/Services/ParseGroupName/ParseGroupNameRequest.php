<?php

declare(strict_types=1);

namespace Platform\Account\Domain\Services\ParseGroupName;

class ParseGroupNameRequest
{
    public function __construct(
        public string $name,
    ) {
    }
}
