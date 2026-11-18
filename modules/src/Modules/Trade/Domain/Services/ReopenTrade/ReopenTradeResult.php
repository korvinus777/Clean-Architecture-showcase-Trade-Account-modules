<?php

declare(strict_types=1);

namespace Platform\Trade\Domain\Services\ReopenTrade;

class ReopenTradeResult
{
    public function __construct(
        public bool $reopened,
    ) {
    }
}
