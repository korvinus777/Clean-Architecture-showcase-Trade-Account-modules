<?php

declare(strict_types=1);

namespace Platform\Trade\App\Services\CloseMtTrade;

use Platform\Package\Components\Time\DateTime;
use Platform\Trade\Domain\ValueObjects\CloseReason;

class CloseMtTradeRequest
{
    public function __construct(
        public int         $tradeId,
        public int         $login,
        public float       $lots,
        public float       $price,
        public DateTime    $time,
        public float       $swap,
        public float       $commission,
        public float       $pnl,
        public CloseReason $reason,
        public int         $mt5TransactionId,
        public int         $mtPositionId,
    ) {
    }
}
