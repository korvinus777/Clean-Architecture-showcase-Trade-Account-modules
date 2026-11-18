<?php

declare(strict_types=1);

namespace Platform\Trade\App\Services\CloseNativeTrade;

interface CloseNativeTradeInput
{
    public function close(CloseNativeTradeRequest $request) : CloseNativeTradeResult;
}
