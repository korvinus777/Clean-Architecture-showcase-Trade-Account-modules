<?php

declare(strict_types=1);

namespace Platform\Trade\Domain\Services\CrmReopenTrade;

use Platform\Trade\Domain\Dto\MTResultCode;
use Platform\Trade\Domain\Dto\NTResultCode;
use Platform\Package\Components\Http\Response\Code;

interface CrmReopenTradeOutput
{
    public function reopened(int $tradeId) : CrmReopenTradeResult;

    public function symbolUnavailable() : CrmReopenTradeResult;

    public function cannotReopenOnMt(MTResultCode $resultCode) : CrmReopenTradeResult;

    public function cannotReopenOnNt(NTResultCode $resultCode) : CrmReopenTradeResult;

    public function symbolMarketClosed() : CrmReopenTradeResult;

    public function symbolDisabled() : CrmReopenTradeResult;

    public function accountNotFound() : CrmReopenTradeResult;

    public function accountNotTradable() : CrmReopenTradeResult;

    public function tradeNotFound() : CrmReopenTradeResult;

    public function error() : CrmReopenTradeResult;

    public function engineTypeNotFound(Code $code) : CrmReopenTradeResult;

    public function cannotProcessReopen() : CrmReopenTradeResult;
}
