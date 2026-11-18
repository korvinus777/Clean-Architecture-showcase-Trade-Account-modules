<?php

declare(strict_types=1);

namespace Platform\Trade\App\Outputs\CloseNativeTrade;

use Platform\Trade\Domain\Entities\ClosedTrade;
use Platform\Trade\App\Services\CloseNativeTrade\CloseNativeTradeResult;
use Platform\Trade\App\Services\CloseNativeTrade\CloseNativeTradeOutput as ICloseNativeTradeOutput;

class CloseNativeTradeOutput implements ICloseNativeTradeOutput
{
    public function closed(ClosedTrade $closedTrade) : CloseNativeTradeResult
    {
        return new CloseNativeTradeResult($closedTrade);
    }

    public function tradeNotFound() : CloseNativeTradeResult
    {
        return new CloseNativeTradeResult(error: new \Exception('Trade not found'));
    }

    public function accountNotFound() : CloseNativeTradeResult
    {
        return new CloseNativeTradeResult(error: new \Exception('Account not found'));
    }

    public function cannotProcessClosing() : CloseNativeTradeResult
    {
        return new CloseNativeTradeResult(error: new \Exception('Cannot process closing'));
    }

    public function alreadyProcessed() : CloseNativeTradeResult
    {
        return new CloseNativeTradeResult(
            closedTrade: null,
            alreadyProcessed: true,
        );
    }
}
