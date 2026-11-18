<?php

declare(strict_types=1);

namespace Platform\Trade\App\Outputs\ClosePendingOrder;

use Platform\Trade\Domain\Dto\MTResultCode;
use Platform\Trade\Domain\Dto\NTResultCode;
use Platform\Package\Components\Http\Response\Code;
use Platform\Trade\App\Outputs\ClosePendingOrder\Results\ClosePendingOrderJsonResult;
use Platform\Trade\Domain\Services\ClosePendingOrder\ClosePendingOrderOutput as IClosePendingOrderOutput;

class ClosePendingOrderJsonOutput implements IClosePendingOrderOutput
{
    public function accountNotFound() : ClosePendingOrderJsonResult
    {
        return (new ClosePendingOrderJsonResult())
            ->setCode(Code::TRADE_ACCOUNT_NOT_FOUND)
            ->setHttStatus(422);
    }

    public function accountNotTradable() : ClosePendingOrderJsonResult
    {
        return (new ClosePendingOrderJsonResult())
            ->setCode(Code::TRADE_ACCOUNT_NOT_TRADABLE)
            ->setHttStatus(422);
    }

    public function pendingOrderNotFound() : ClosePendingOrderJsonResult
    {
        return (new ClosePendingOrderJsonResult())
            ->setCode(Code::TRADE_PENDING_ORDER_NOT_FOUND)
            ->setHttStatus(404);
    }

    public function pendingOrderNotInPendingStatus() : ClosePendingOrderJsonResult
    {
        return (new ClosePendingOrderJsonResult())
            ->setCode(Code::TRADE_IS_NOT_PENDING)
            ->setHttStatus(422);
    }

    public function symbolUnavailable() : ClosePendingOrderJsonResult
    {
        return (new ClosePendingOrderJsonResult())
            ->setCode(Code::TRADE_UNAVAILABLE_SYMBOL)
            ->setHttStatus(422);
    }

    public function symbolDisabled() : ClosePendingOrderJsonResult
    {
        return (new ClosePendingOrderJsonResult())
            ->setCode(Code::TRADE_DISABLED_SYMBOL)
            ->setHttStatus(422);
    }

    public function symbolMarketClosed() : ClosePendingOrderJsonResult
    {
        return (new ClosePendingOrderJsonResult())
            ->setCode(Code::TRADE_MARKET_CLOSED)
            ->setHttStatus(422);
    }

    public function engineTypeNotFound() : ClosePendingOrderJsonResult
    {
        return (new ClosePendingOrderJsonResult())
            ->setCode(Code::TRADE_ENGINE_TYPE_NOT_FOUND)
            ->setHttStatus(422);
    }

    public function cannotCloseOnMt(MTResultCode $resultCode) : ClosePendingOrderJsonResult
    {
        $code = $resultCode->getAppCode(Code::TRADE_ORDER_MT_CLOSE_ERROR);

        $isUnexpectedError = $code === Code::TRADE_ORDER_MT_CLOSE_ERROR;

        return (new ClosePendingOrderJsonResult($resultCode))
            ->setCode($code)
            ->setHttStatus($isUnexpectedError ? 503 : 422);
    }

    public function cannotCloseOnNt(NTResultCode $resultCode) : ClosePendingOrderJsonResult
    {
        $code = $resultCode->getAppCode(Code::TRADE_NT_ORDER_CLOSE_ERROR);

        $isUnexpectedError = $code === Code::TRADE_NT_ORDER_CLOSE_ERROR;

        return (new ClosePendingOrderJsonResult())
            ->setCode($code)
            ->setHttStatus($isUnexpectedError ? 503 : 422);
    }

    public function closed(int $pendingOrderId) : ClosePendingOrderJsonResult
    {
        return new ClosePendingOrderJsonResult(pendingOrderId: $pendingOrderId);
    }

    public function asyncClosing() : ClosePendingOrderJsonResult
    {
        return (new ClosePendingOrderJsonResult())
            ->setCode(Code::TRADE_SENT_ON_MT_ASYNC)
            ->setHttStatus(200);
    }
}
