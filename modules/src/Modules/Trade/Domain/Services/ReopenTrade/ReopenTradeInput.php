<?php

declare(strict_types=1);

namespace Platform\Trade\Domain\Services\ReopenTrade;

interface ReopenTradeInput
{
    public function reopen(ReopenTradeRequest $request) : ReopenTradeResult;
}
