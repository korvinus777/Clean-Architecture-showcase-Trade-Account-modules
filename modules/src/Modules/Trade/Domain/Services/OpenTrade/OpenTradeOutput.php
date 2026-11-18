<?php

declare(strict_types=1);

namespace Platform\Trade\Domain\Services\OpenTrade;

use Platform\Trade\Domain\Dto\MTResultCode;
use Platform\Trade\Domain\Dto\NTResultCode;
use Platform\Package\Components\Http\Response\Code;

interface OpenTradeOutput
{
    public function opened(int $tradeId, float $price) : OpenTradeResult;

    public function symbolUnavailable() : OpenTradeResult;

    public function quoteNotFound() : OpenTradeResult;

    public function incorrectVolume(Code $code, ?float $volume = null) : OpenTradeResult;

    public function stopLossHigherThanCurrentPrice() : OpenTradeResult;

    public function stopLossLowerThanCurrentPrice() : OpenTradeResult;

    public function takeProfitLowerThanCurrentPrice() : OpenTradeResult;

    public function takeProfitHigherThanCurrentPrice() : OpenTradeResult;

    public function cannotOpenOnMt(MTResultCode $resultCode) : OpenTradeResult;

    public function cannotOpenOnNt(NTResultCode $resultCode) : OpenTradeResult;

    public function symbolMarketClosed() : OpenTradeResult;

    public function symbolDisabled() : OpenTradeResult;

    public function accountNotFound() : OpenTradeResult;

    public function accountNotTradable() : OpenTradeResult;

    public function engineTypeNotFound(Code $code) : OpenTradeResult;
}
