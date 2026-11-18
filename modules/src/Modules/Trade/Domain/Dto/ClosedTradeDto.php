<?php

declare(strict_types=1);

namespace Platform\Trade\Domain\Dto;

use Platform\Package\Components\Time\DateTime;
use Platform\Trade\Domain\ValueObjects\CloseReason;

class ClosedTradeDto
{
    public function __construct(
        public int         $tradeId,
        public int         $mt5TransactionId,
        public float       $price,
        public DateTime    $time,
        public float       $lots,
        public float       $amount,
        public float       $swap,
        public float       $swapSystem,
        public float       $commission,
        public float       $pnl,
        public float       $pnlSystem,
        public CloseReason $reason,
        public float       $turnover = 0,
        public float       $turnoverSystem = 0,
    ) {
    }
}
