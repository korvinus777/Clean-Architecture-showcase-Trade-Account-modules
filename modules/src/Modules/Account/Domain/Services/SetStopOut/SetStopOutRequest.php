<?php

declare(strict_types=1);

namespace Platform\Account\Domain\Services\SetStopOut;

class SetStopOutRequest
{
    public function __construct(
        public int  $accountId,
        public bool $isEnter,
    ) {
    }
}
