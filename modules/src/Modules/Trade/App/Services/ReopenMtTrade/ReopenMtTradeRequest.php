<?php

declare(strict_types=1);

namespace Platform\Trade\App\Services\ReopenMtTrade;

class ReopenMtTradeRequest
{
    public function __construct(
        public int $mt5TransactionId,
    ) {
    }
}
