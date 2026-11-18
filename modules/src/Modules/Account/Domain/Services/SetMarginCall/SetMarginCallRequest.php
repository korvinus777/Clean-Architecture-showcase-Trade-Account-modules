<?php

declare(strict_types=1);

namespace Platform\Account\Domain\Services\SetMarginCall;

class SetMarginCallRequest
{
    public function __construct(
        public int  $accountId,
        public bool $isEnter,
    ) {
    }
}
