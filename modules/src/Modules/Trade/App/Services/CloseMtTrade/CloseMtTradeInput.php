<?php

declare(strict_types=1);

namespace Platform\Trade\App\Services\CloseMtTrade;

interface CloseMtTradeInput
{
    public function close(CloseMtTradeRequest $request) : CloseMtTradeResult;
}
