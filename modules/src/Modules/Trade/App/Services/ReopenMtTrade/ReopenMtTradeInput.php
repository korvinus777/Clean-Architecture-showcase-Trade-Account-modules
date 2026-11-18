<?php

declare(strict_types=1);

namespace Platform\Trade\App\Services\ReopenMtTrade;

interface ReopenMtTradeInput
{
    public function reopen(ReopenMtTradeRequest $request) : bool;
}
