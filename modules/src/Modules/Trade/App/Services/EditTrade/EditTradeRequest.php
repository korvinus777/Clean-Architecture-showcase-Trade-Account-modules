<?php

declare(strict_types=1);

namespace Platform\Trade\App\Services\EditTrade;

use Platform\Package\Components\Http\Request\Context;

class EditTradeRequest
{
    public function __construct(
        public int     $tradeId,
        public int     $accountId,
        public Context $context,
        public ?float  $stopLoss = null,
        public ?float  $takeProfit = null,
        public ?int    $direction = null,
        public ?string $openDate = null,
        public ?float  $openPrice = null,
        public ?float  $volume = null,
        public ?float  $swap = null,
    ) {
    }
}
