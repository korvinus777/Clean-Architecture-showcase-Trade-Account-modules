<?php

declare(strict_types=1);

namespace Platform\Trade\App\Services\CloseAccountTrades;

class CloseAccountTradesRequest
{
    public function __construct(
        public int $accountId,
        public int $requestedFromClient,
    ) {
    }
}
