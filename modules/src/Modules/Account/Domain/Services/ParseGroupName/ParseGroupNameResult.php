<?php

declare(strict_types=1);

namespace Platform\Account\Domain\Services\ParseGroupName;

use Platform\Account\Domain\Dto\GroupParamsDto;

class ParseGroupNameResult
{
    public function __construct(
        public bool            $isServiceGroup = false,
        public ?GroupParamsDto $groupParams = null,
    ) {
    }

    /**
     * @psalm-assert-if-true GroupParamsDto $this->groupParams
     * @return bool
     */
    public function isParsed() : bool
    {
        return $this->groupParams !== null;
    }
}
