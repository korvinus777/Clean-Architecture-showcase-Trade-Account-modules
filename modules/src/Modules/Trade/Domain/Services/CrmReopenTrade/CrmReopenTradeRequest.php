<?php

declare(strict_types=1);

namespace Platform\Trade\Domain\Services\CrmReopenTrade;

class CrmReopenTradeRequest
{
    public function __construct(
        public int  $tradeId,
        public int  $accountId,
    ) {

    }
}
