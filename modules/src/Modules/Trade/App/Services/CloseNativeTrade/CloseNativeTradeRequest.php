<?php

declare(strict_types=1);

namespace Platform\Trade\App\Services\CloseNativeTrade;

use Platform\Package\Components\Time\DateTime;
use Platform\Trade\Domain\ValueObjects\CloseReason;

class CloseNativeTradeRequest
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
        public int         $nativeTransactionId,
        public int         $nativePositionId,
        public float       $margin,
    ) {
    }
}
