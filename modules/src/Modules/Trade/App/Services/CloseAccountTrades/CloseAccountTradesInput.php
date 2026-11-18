<?php

declare(strict_types=1);

namespace Platform\Trade\App\Services\CloseAccountTrades;

interface CloseAccountTradesInput
{
    public function close(CloseAccountTradesRequest $request) : CloseAccountTradesResult;
}
