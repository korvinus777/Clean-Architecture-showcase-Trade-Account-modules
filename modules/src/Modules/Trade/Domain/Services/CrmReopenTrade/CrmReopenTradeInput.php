<?php

declare(strict_types=1);

namespace Platform\Trade\Domain\Services\CrmReopenTrade;

interface CrmReopenTradeInput
{
    public function reopen(CrmReopenTradeRequest $request) : CrmReopenTradeResult;
}
