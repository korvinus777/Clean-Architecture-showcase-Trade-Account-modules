<?php

declare(strict_types=1);

namespace Platform\Trade\App\Services\ActivateMtOrder;

use Platform\Package\Components\Time\DateTime;
use Platform\Trade\Domain\ValueObjects\Direction;

class ActivateMtOrderRequest
{
    public function __construct(
        public int       $orderId,
        public string    $symbol,
        public Direction $direction,
        public float     $lots,
        public float     $price,
        public DateTime  $time,
        public float     $stopLoss,
        public float     $takeProfit,
        public float     $swap,
        public float     $commission,
        public int       $mt5TransactionId,
    ) {
    }
}
