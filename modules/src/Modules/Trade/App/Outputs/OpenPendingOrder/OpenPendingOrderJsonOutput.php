<?php

declare(strict_types=1);

namespace Platform\Trade\App\Outputs\OpenPendingOrder;

use Platform\Trade\Domain\Dto\MTResultCode;
use Platform\Trade\Domain\Dto\NTResultCode;
use Platform\Package\Components\Http\Response\Code;
use Platform\Trade\App\Outputs\OpenPendingOrder\Results\OpenPendingOrderJsonResult;
use Platform\Trade\Domain\Services\OpenPendingOrder\OpenPendingOrderOutput as IOpenPendingOrderOutput;

class OpenPendingOrderJsonOutput implements IOpenPendingOrderOutput
{
    public function opened(int $orderId, float $price) : OpenPendingOrderJsonResult
    {
        return new OpenPendingOrderJsonResult(orderId: $orderId, price: $price);
    }

    public function symbolUnavailable() : OpenPendingOrderJsonResult
    {
        return (new OpenPendingOrderJsonResult())
            ->setCode(Code::TRADE_UNAVAILABLE_SYMBOL)
            ->setHttStatus(422);
    }

    public function incorrectVolume(Code $code, ?float $volume = null) : OpenPendingOrderJsonResult
    {
        return (new OpenPendingOrderJsonResult())
            ->setCode($code)
            ->setBindings($volume ? ['volume' => $volume] : [])
            ->setHttStatus(422);
    }

    public function cannotOpenOnMt(MTResultCode $resultCode) : OpenPendingOrderJsonResult
    {
        $code = $resultCode->getAppCode(Code::TRADE_ORDER_MT_OPEN_ERROR);

        $isUnexpectedError = $code === Code::TRADE_ORDER_MT_OPEN_ERROR;

        return (new OpenPendingOrderJsonResult($resultCode))
            ->setCode($code)
            ->setHttStatus($isUnexpectedError ? 503 : 422);
    }

    public function cannotOpenOnNt(NTResultCode $resultCode) : OpenPendingOrderJsonResult
    {
        $code = $resultCode->getAppCode(Code::TRADE_NT_ORDER_OPEN_ERROR);

        $isUnexpectedError = $code === Code::TRADE_NT_ORDER_OPEN_ERROR;

        return (new OpenPendingOrderJsonResult())
            ->setCode($code)
            ->setHttStatus($isUnexpectedError ? 503 : 422);
    }

    public function symbolMarketClosed() : OpenPendingOrderJsonResult
    {
        return (new OpenPendingOrderJsonResult())
            ->setCode(Code::TRADE_MARKET_CLOSED)
            ->setHttStatus(422);
    }

    public function symbolDisabled() : OpenPendingOrderJsonResult
    {
        return (new OpenPendingOrderJsonResult())
            ->setCode(Code::TRADE_DISABLED_SYMBOL)
            ->setHttStatus(422);
    }

    public function accountNotFound() : OpenPendingOrderJsonResult
    {
        return (new OpenPendingOrderJsonResult())
            ->setCode(Code::TRADE_ACCOUNT_NOT_FOUND)
            ->setHttStatus(422);
    }

    public function accountNotTradable() : OpenPendingOrderJsonResult
    {
        return (new OpenPendingOrderJsonResult())
            ->setCode(Code::TRADE_ACCOUNT_NOT_TRADABLE)
            ->setHttStatus(422);
    }

    public function unexpectedError() : OpenPendingOrderJsonResult
    {
        return (new OpenPendingOrderJsonResult())
            ->setCode(Code::UNEXPECTED_ERROR)
            ->setHttStatus(500);
    }

    public function priceEqualCurrentPrice() : OpenPendingOrderJsonResult
    {
        return (new OpenPendingOrderJsonResult())
            ->setCode(Code::TRADE_ORDER_EQUAL_CURRENT_PRICE)
            ->setHttStatus(422);
    }

    public function stopLossHigherThanPrice() : OpenPendingOrderJsonResult
    {
        return (new OpenPendingOrderJsonResult())
            ->setCode(Code::TRADE_PENDING_ORDER_SL_HIGHER_PRICE)
            ->setHttStatus(422);
    }

    public function stopLossLowerThanPrice() : OpenPendingOrderJsonResult
    {
        return (new OpenPendingOrderJsonResult())
            ->setCode(Code::TRADE_PENDING_ORDER_SL_LOWER_PRICE)
            ->setHttStatus(422);
    }

    public function takeProfitHigherThanPrice() : OpenPendingOrderJsonResult
    {
        return (new OpenPendingOrderJsonResult())
            ->setCode(Code::TRADE_PENDING_ORDER_TP_HIGHER_PRICE)
            ->setHttStatus(422);
    }

    public function takeProfitLowerThanPrice() : OpenPendingOrderJsonResult
    {
        return (new OpenPendingOrderJsonResult())
            ->setCode(Code::TRADE_PENDING_ORDER_TP_LOWER_PRICE)
            ->setHttStatus(422);
    }

    public function engineTypeNotFound() : OpenPendingOrderJsonResult
    {
        return (new OpenPendingOrderJsonResult())
            ->setCode(Code::TRADE_ENGINE_TYPE_NOT_FOUND)
            ->setHttStatus(422);
    }

    public function canNotProcessOrder() : OpenPendingOrderJsonResult
    {
        return (new OpenPendingOrderJsonResult())
            ->setCode(Code::TRADE_PENDING_ORDER_PROCESS_ERROR)
            ->setHttStatus(422);
    }
}
