<?php

declare(strict_types=1);

namespace Platform\Trade\Domain\Services\OpenPendingOrder;

use Platform\Trade\Domain\ValueObjects\Direction;

class OpenPendingOrderRequest
{
    public function __construct(
        public int       $accountId,
        public int       $symbolId,
        public float     $price,
        public float     $lots,
        public float     $stopLoss,
        public float     $takeProfit,
        public Direction $direction,
        public int       $requestedFromClient,
    ) {
    }
}
