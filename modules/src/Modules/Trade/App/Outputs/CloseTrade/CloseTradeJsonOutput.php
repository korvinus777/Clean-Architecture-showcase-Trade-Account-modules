<?php

declare(strict_types=1);

namespace Platform\Trade\App\Outputs\CloseTrade;

use Platform\Trade\Domain\Dto\MTResultCode;
use Platform\Trade\Domain\Dto\NTResultCode;
use Platform\Package\Components\Http\Response\Code;
use Platform\Trade\Domain\Services\CloseTrade\CloseTradeOutput;
use Platform\Trade\App\Outputs\CloseTrade\Results\CloseTradeJsonResult;

class CloseTradeJsonOutput implements CloseTradeOutput
{
    public function closed(int $tradeId, float $price) : CloseTradeJsonResult
    {
        return new CloseTradeJsonResult(tradeId: $tradeId, price: $price);
    }

    public function symbolUnavailable() : CloseTradeJsonResult
    {
        return (new CloseTradeJsonResult())
            ->setCode(Code::TRADE_UNAVAILABLE_SYMBOL)
            ->setHttStatus(422);
    }

    public function cannotCloseOnMt(MTResultCode $resultCode) : CloseTradeJsonResult
    {
        $code = $resultCode->getAppCode(Code::TRADE_MT_CLOSE_ERROR);

        $isUnexpectedError = $code === Code::TRADE_MT_CLOSE_ERROR;

        return (new CloseTradeJsonResult($resultCode))
            ->setCode($code)
            ->setHttStatus($isUnexpectedError ? 503 : 422);
    }

    public function cannotCloseOnNt(NTResultCode $resultCode) : CloseTradeJsonResult
    {
        $code = $resultCode->getAppCode(Code::NT_TRADE_CLOSE_ERROR);

        $isUnexpectedError = $code === Code::NT_TRADE_CLOSE_ERROR;

        return (new CloseTradeJsonResult($resultCode))
            ->setCode($code)
            ->setHttStatus($isUnexpectedError ? 503 : 422);
    }

    public function symbolMarketClosed() : CloseTradeJsonResult
    {
        return (new CloseTradeJsonResult())
            ->setCode(Code::TRADE_MARKET_CLOSED)
            ->setHttStatus(422);
    }

    public function symbolDisabled() : CloseTradeJsonResult
    {
        return (new CloseTradeJsonResult())
            ->setCode(Code::TRADE_DISABLED_SYMBOL)
            ->setHttStatus(422);
    }

    public function accountNotFound() : CloseTradeJsonResult
    {
        return (new CloseTradeJsonResult())
            ->setCode(Code::TRADE_ACCOUNT_NOT_FOUND)
            ->setHttStatus(422);
    }

    public function accountNotTradable() : CloseTradeJsonResult
    {
        return (new CloseTradeJsonResult())
            ->setCode(Code::TRADE_ACCOUNT_NOT_TRADABLE)
            ->setHttStatus(422);
    }

    public function tradeNotFound() : CloseTradeJsonResult
    {
        return (new CloseTradeJsonResult())
            ->setCode(Code::TRADE_NOT_FOUND)
            ->setHttStatus(404);
    }

    public function error() : CloseTradeJsonResult
    {
        return (new CloseTradeJsonResult())
            ->setCode(Code::TRADE_UNEXPECTED_ERROR)
            ->setHttStatus(500);
    }

    public function asyncClosing() : CloseTradeJsonResult
    {
        return (new CloseTradeJsonResult())
            ->setCode(Code::TRADE_SENT_ON_MT_ASYNC)
            ->setHttStatus(200);
    }

    public function engineTypeNotFound(Code $code) : CloseTradeJsonResult
    {
        return (new CloseTradeJsonResult())
            ->setCode(Code::TRADE_ENGINE_TYPE_NOT_FOUND)
            ->setHttStatus(503);
    }

    public function cannotProcessClosing() : CloseTradeJsonResult
    {
        return (new CloseTradeJsonResult())
            ->setCode(Code::TRADE_UNEXPECTED_ERROR)
            ->setHttStatus(503);
    }
}
