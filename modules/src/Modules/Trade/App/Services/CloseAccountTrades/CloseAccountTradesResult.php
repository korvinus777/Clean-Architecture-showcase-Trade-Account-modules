<?php

declare(strict_types=1);

namespace Platform\Trade\App\Services\CloseAccountTrades;

use Platform\Trade\Domain\Services\CloseTrade\CloseTradeResult;

class CloseAccountTradesResult
{
    /**
     * @param array<int, CloseTradeResult> $results
     */
    public function __construct(
        public array $results = [],
    ) {
    }
}
