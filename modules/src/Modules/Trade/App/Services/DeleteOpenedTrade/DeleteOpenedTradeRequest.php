<?php

declare(strict_types=1);

namespace Platform\Trade\App\Services\DeleteOpenedTrade;

class DeleteOpenedTradeRequest
{
    public function __construct(
        public int $tradeId,
    ) {
    }
}
