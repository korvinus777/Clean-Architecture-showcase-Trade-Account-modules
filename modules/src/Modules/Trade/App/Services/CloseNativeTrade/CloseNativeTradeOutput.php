<?php

declare(strict_types=1);

namespace Platform\Trade\App\Services\CloseNativeTrade;

use Platform\Trade\Domain\Entities\ClosedTrade;

interface CloseNativeTradeOutput
{
    public function closed(ClosedTrade $closedTrade) : CloseNativeTradeResult;

    public function tradeNotFound() : CloseNativeTradeResult;

    public function accountNotFound() : CloseNativeTradeResult;

    public function cannotProcessClosing() : CloseNativeTradeResult;

    public function alreadyProcessed() : CloseNativeTradeResult;
}
