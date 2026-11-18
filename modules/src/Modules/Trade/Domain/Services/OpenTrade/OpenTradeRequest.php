<?php

declare(strict_types=1);

namespace Platform\Trade\Domain\Services\OpenTrade;

use Platform\Trade\Domain\ValueObjects\Direction;

class OpenTradeRequest
{
    public function __construct(
        public int       $accountId,
        public int       $symbolId,
        public float     $lots,
        public float     $stopLoss,
        public float     $takeProfit,
        public Direction $direction,
        public int       $requestedFromClient,
    ) {
    }
}
