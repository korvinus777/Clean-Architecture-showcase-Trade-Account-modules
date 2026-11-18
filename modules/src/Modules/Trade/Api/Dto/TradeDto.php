<?php

declare(strict_types=1);

namespace Platform\Trade\Api\Dto;

use Platform\Trade\Domain\Entities\Trade;

class TradeDto
{
    public function __construct(
        public int   $id,
        public int   $symbolId,
        public float $contractSize,
        public int   $accountId,
        public int   $leadId,
        public int   $direction,
        public float $lots,
        public float $lotsLeft,
        public float $amount,
        public float $amountLeft,
        public float $openPrice,
        public int   $openTime,
        public float $stopLoss,
        public float $takeProfit,
        public float $swap,
        public float $swapSystem,
        public bool  $isClosed,
        public float $turnover = 0,
        public float $turnoverSystem = 0,
    ) {
    }

    public static function fromEntity(Trade $trade) : self
    {
        return new self(
            $trade->id,
            $trade->symbolId,
            $trade->contractSize,
            $trade->accountId,
            $trade->leadId,
            $trade->direction->value,
            $trade->lots,
            $trade->lotsLeft,
            $trade->amount,
            $trade->amountLeft,
            $trade->openPrice,
            $trade->openTime->getTimestamp(),
            $trade->stopLoss,
            $trade->takeProfit,
            $trade->swap,
            $trade->swapSystem,
            $trade->isClosed,
            $trade->turnover,
            $trade->turnoverSystem,
        );
    }
}
