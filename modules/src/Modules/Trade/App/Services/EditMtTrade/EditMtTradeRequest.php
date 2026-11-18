<?php

declare(strict_types=1);

namespace Platform\Trade\App\Services\EditMtTrade;

use Platform\Package\Components\Time\DateTime;

class EditMtTradeRequest
{
    public function __construct(
        public int       $tradeId,
        public ?string   $symbol,
        public ?float    $contractSize,
        public ?bool     $isBuy,
        public ?float    $lots,
        public ?float    $lotsLeft,
        public ?float    $openPrice,
        public ?DateTime $openTime,
        public ?float    $swap,
        public ?float    $stopLoss,
        public ?float    $takeProfit,
    ) {
    }
}
