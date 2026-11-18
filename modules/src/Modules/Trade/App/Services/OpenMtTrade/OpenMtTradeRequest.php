<?php

declare(strict_types=1);

namespace Platform\Trade\App\Services\OpenMtTrade;

use Platform\Package\Components\Time\DateTime;
use Platform\Trade\Domain\ValueObjects\Direction;

class OpenMtTradeRequest
{
    public function __construct(
        public int       $tradeId,
        public string    $symbol,
        public int       $login,
        public Direction $direction,
        public float     $lots,
        public float     $price,
        public DateTime  $time,
        public float     $stopLoss,
        public float     $takeProfit,
        public float     $swap,
        public float     $commission,
        public int       $mt5TransactionId,
        public DateTime  $mt5TransactionTime,
    ) {
    }
}
