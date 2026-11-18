<?php

declare(strict_types=1);

namespace Platform\Trade\Domain\Services\CloseTrade;

use Platform\Trade\Domain\Dto\MTResultCode;
use Platform\Trade\Domain\Dto\NTResultCode;
use Platform\Package\Components\Http\Response\Code;

interface CloseTradeOutput
{
    public function closed(int $tradeId, float $price) : CloseTradeResult;

    public function symbolUnavailable() : CloseTradeResult;

    public function cannotCloseOnMt(MTResultCode $resultCode) : CloseTradeResult;

    public function cannotCloseOnNt(NTResultCode $resultCode) : CloseTradeResult;

    public function symbolMarketClosed() : CloseTradeResult;

    public function symbolDisabled() : CloseTradeResult;

    public function accountNotFound() : CloseTradeResult;

    public function accountNotTradable() : CloseTradeResult;

    public function tradeNotFound() : CloseTradeResult;

    public function error() : CloseTradeResult;

    public function asyncClosing() : CloseTradeResult;

    public function engineTypeNotFound(Code $code) : CloseTradeResult;

    public function cannotProcessClosing() : CloseTradeResult;
}
