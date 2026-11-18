<?php

declare(strict_types=1);

namespace Platform\Trade\Domain\Services\CloseTrade;

class CloseTradeRequest
{
    public function __construct(
        public int  $tradeId,
        public int  $accountId,
        public int  $requestedFromClient,
        public bool $deferred,
    ) {
    }
}
