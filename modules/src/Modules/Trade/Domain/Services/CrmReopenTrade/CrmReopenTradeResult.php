<?php

declare(strict_types=1);

namespace Platform\Trade\Domain\Services\CrmReopenTrade;

class CrmReopenTradeResult
{
    public function __construct(protected int $tradeId = 0)
    {

    }
}
