<?php

declare(strict_types=1);

namespace Platform\Trade\App\Services\CloseAccountTrades;

use Platform\Trade\Domain\Services\CloseTrade\CloseTradeResult;

interface CloseAccountTradesOutput
{
    /**
     * @param array<int, CloseTradeResult> $results
     * @return CloseAccountTradesResult
     */
    public function done(array $results) : CloseAccountTradesResult;
}
