<?php

declare(strict_types=1);

namespace Platform\Trade\App\Outputs\OpenTrade;

use Platform\Trade\Domain\Dto\MTResultCode;
use Platform\Trade\Domain\Dto\NTResultCode;
use Platform\Package\Components\Http\Response\Code;
use Platform\Trade\App\Outputs\OpenTrade\Results\OpenTradeJsonResult;
use Platform\Trade\Domain\Services\OpenTrade\OpenTradeOutput as IOpenTradeOutput;

class OpenTradeJsonOutput implements IOpenTradeOutput
{
    public function opened(int $tradeId, float $price) : OpenTradeJsonResult
    {
        return new OpenTradeJsonResult(tradeId: $tradeId, price: $price);
    }

    public function quoteNotFound() : OpenTradeJsonResult
    {
        return (new OpenTradeJsonResult())
            ->setCode(Code::TRADE_UNEXPECTED_ERROR)
            ->setHttStatus(404);
    }

    public function symbolUnavailable() : OpenTradeJsonResult
    {
        return (new OpenTradeJsonResult())
            ->setCode(Code::TRADE_UNAVAILABLE_SYMBOL)
            ->setHttStatus(422);
    }

    public function incorrectVolume(Code $code, ?float $volume = null) : OpenTradeJsonResult
    {
        return (new OpenTradeJsonResult())
            ->setCode($code)
            ->setBindings($volume ? ['volume' => $volume] : [])
            ->setHttStatus(422);
    }

    public function stopLossHigherThanCurrentPrice() : OpenTradeJsonResult
    {
        return (new OpenTradeJsonResult())
            ->setCode(Code::TRADE_SL_HIGHER_CURRENT_PRICE)
            ->setHttStatus(422);
    }

    public function stopLossLowerThanCurrentPrice() : OpenTradeJsonResult
    {
        return (new OpenTradeJsonResult())
            ->setCode(Code::TRADE_SL_LOWER_CURRENT_PRICE)
            ->setHttStatus(422);
    }

    public function takeProfitHigherThanCurrentPrice() : OpenTradeJsonResult
    {
        return (new OpenTradeJsonResult())
            ->setCode(Code::TRADE_TP_HIGHER_CURRENT_PRICE)
            ->setHttStatus(422);
    }

    public function takeProfitLowerThanCurrentPrice() : OpenTradeJsonResult
    {
        return (new OpenTradeJsonResult())
            ->setCode(Code::TRADE_TP_LOWER_CURRENT_PRICE)
            ->setHttStatus(422);
    }

    public function cannotOpenOnMt(MTResultCode $resultCode) : OpenTradeJsonResult
    {
        $code = $resultCode->getAppCode(Code::TRADE_MT_OPEN_ERROR);

        $isUnexpectedError = $code === Code::TRADE_MT_OPEN_ERROR;

        return (new OpenTradeJsonResult($resultCode))
            ->setCode($code)
            ->setHttStatus($isUnexpectedError ? 503 : 422);
    }

    public function cannotOpenOnNt(NTResultCode $resultCode) : OpenTradeJsonResult
    {
        $code = $resultCode->getAppCode(Code::NT_TRADE_OPEN_ERROR);

        $isUnexpectedError = $code === Code::NT_TRADE_OPEN_ERROR;

        return (new OpenTradeJsonResult($resultCode))
            ->setCode($code)
            ->setHttStatus($isUnexpectedError ? 503 : 422);
    }

    public function symbolMarketClosed() : OpenTradeJsonResult
    {
        return (new OpenTradeJsonResult())
            ->setCode(Code::TRADE_MARKET_CLOSED)
            ->setHttStatus(422);
    }

    public function symbolDisabled() : OpenTradeJsonResult
    {
        return (new OpenTradeJsonResult())
            ->setCode(Code::TRADE_DISABLED_SYMBOL)
            ->setHttStatus(422);
    }

    public function accountNotFound() : OpenTradeJsonResult
    {
        return (new OpenTradeJsonResult())
            ->setCode(Code::TRADE_ACCOUNT_NOT_FOUND)
            ->setHttStatus(422);
    }

    public function accountNotTradable() : OpenTradeJsonResult
    {
        return (new OpenTradeJsonResult())
            ->setCode(Code::TRADE_ACCOUNT_NOT_TRADABLE)
            ->setHttStatus(422);
    }

    public function engineTypeNotFound(Code $code) : OpenTradeJsonResult
    {
        return (new OpenTradeJsonResult())
            ->setCode(Code::TRADE_ENGINE_TYPE_NOT_FOUND)
            ->setHttStatus(503);
    }
}
