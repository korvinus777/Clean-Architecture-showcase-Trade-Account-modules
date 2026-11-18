<?php

declare(strict_types=1);

namespace Platform\Trade\App\Outputs\CrmReopenTrade;

use Platform\Trade\Domain\Dto\MTResultCode;
use Platform\Trade\Domain\Dto\NTResultCode;
use Platform\Package\Components\Http\Response\Code;
use Platform\Trade\App\Outputs\CrmReopenTrade\Results\CrmReopenTradeJsonResult;
use Platform\Trade\Domain\Services\CrmReopenTrade\CrmReopenTradeOutput as ICrmReopenTradeOutput;

class CrmReopenTradeOutput implements ICrmReopenTradeOutput
{
    public function reopened(int $tradeId) : CrmReopenTradeJsonResult
    {
        return (new CrmReopenTradeJsonResult(tradeId: $tradeId));
    }

    public function symbolUnavailable() : CrmReopenTradeJsonResult
    {
        return (new CrmReopenTradeJsonResult())
            ->setCode(Code::TRADE_UNAVAILABLE_SYMBOL)
            ->setHttStatus(422);
    }

    public function cannotReopenOnMt(MTResultCode $resultCode) : CrmReopenTradeJsonResult
    {
        $code = $resultCode->getAppCode(Code::TRADE_MT_EDIT_ERROR);

        return (new CrmReopenTradeJsonResult())
            ->setCode($code)
            ->setHttStatus($code === Code::TRADE_MT_EDIT_ERROR ? 503 : 422);
    }

    public function cannotReopenOnNt(NTResultCode $resultCode) : CrmReopenTradeJsonResult
    {
        $code = $resultCode->getAppCode(Code::NT_TRADE_EDIT_ERROR);

        return (new CrmReopenTradeJsonResult())
            ->setCode($code)
            ->setHttStatus($code === Code::NT_TRADE_EDIT_ERROR ? 503 : 422);
    }

    public function symbolMarketClosed() : CrmReopenTradeJsonResult
    {
        return (new CrmReopenTradeJsonResult())
            ->setCode(Code::TRADE_MARKET_CLOSED)
            ->setHttStatus(422);
    }

    public function symbolDisabled() : CrmReopenTradeJsonResult
    {
        return (new CrmReopenTradeJsonResult())
            ->setCode(Code::TRADE_DISABLED_SYMBOL)
            ->setHttStatus(422);
    }

    public function accountNotFound() : CrmReopenTradeJsonResult
    {
        return (new CrmReopenTradeJsonResult())
            ->setCode(Code::TRADE_ACCOUNT_NOT_FOUND)
            ->setHttStatus(422);
    }

    public function accountNotTradable() : CrmReopenTradeJsonResult
    {
        return (new CrmReopenTradeJsonResult())
            ->setCode(Code::TRADE_ACCOUNT_NOT_TRADABLE)
            ->setHttStatus(422);
    }

    public function tradeNotFound() : CrmReopenTradeJsonResult
    {
        return (new CrmReopenTradeJsonResult())
            ->setCode(Code::TRADE_NOT_FOUND)
            ->setHttStatus(404);
    }

    public function error() : CrmReopenTradeJsonResult
    {
        return (new CrmReopenTradeJsonResult())
            ->setCode(Code::TRADE_UNEXPECTED_ERROR)
            ->setHttStatus(500);
    }

    public function engineTypeNotFound(Code $code) : CrmReopenTradeJsonResult
    {
        return (new CrmReopenTradeJsonResult())
            ->setCode(Code::TRADE_ENGINE_TYPE_NOT_FOUND)
            ->setHttStatus(503);
    }

    public function cannotCloseOnNativeEngine() : CrmReopenTradeJsonResult
    {
        return (new CrmReopenTradeJsonResult())
            ->setCode(CODE::NT_TRADE_EDIT_ERROR)
            ->setHttStatus(503);
    }

    public function cannotProcessReopen() : CrmReopenTradeJsonResult
    {
        return (new CrmReopenTradeJsonResult())
            ->setCode(Code::TRADE_UNEXPECTED_ERROR)
            ->setHttStatus(503);
    }
}
