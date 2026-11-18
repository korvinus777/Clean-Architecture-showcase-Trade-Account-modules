<?php

declare(strict_types=1);

namespace Platform\Trade\Api\Dto;

use Platform\Trade\Domain\Entities\ClosedTrade;

class ClosedTradeDto
{
    public function __construct(
        public int   $id,
        public int   $tradeId,
        public int   $mt5TransactionId,
        public float $lots,
        public float $amount,
        public float $price,
        public int   $reason,
        public int   $time,
        public float $pnl,
        public float $pnlSystem,
        public float $swap,
        public float $swapSystem,
        public float $turnover = 0,
        public float $turnoverSystem = 0,
    ) {
    }

    public static function fromEntity(ClosedTrade $closeTrade) : self
    {
        return new self(
            $closeTrade->id,
            $closeTrade->tradeId,
            $closeTrade->mt5TransactionId,
            $closeTrade->lots,
            $closeTrade->amount,
            $closeTrade->price,
            $closeTrade->reason->value,
            $closeTrade->time->getTimestamp(),
            $closeTrade->pnl,
            $closeTrade->pnlSystem,
            $closeTrade->swap,
            $closeTrade->swapSystem,
            $closeTrade->turnover,
            $closeTrade->turnoverSystem,
        );
    }
}
