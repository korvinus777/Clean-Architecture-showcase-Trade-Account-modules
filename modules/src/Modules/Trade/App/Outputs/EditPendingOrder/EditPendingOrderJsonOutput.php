<?php

declare(strict_types=1);

namespace Platform\Trade\App\Outputs\EditPendingOrder;

use Platform\Trade\Domain\Dto\MTResultCode;
use Platform\Trade\Domain\Dto\NTResultCode;
use Platform\Package\Components\Http\Response\Code;
use Platform\Trade\Domain\Services\EditPendingOrder\EditPendingOrderResult;
use Platform\Trade\App\Outputs\EditPendingOrder\Result\EditPendingOrderJsonResult;
use Platform\Trade\Domain\Services\EditPendingOrder\EditPendingOrderOutput as IEditPendingOrderOutput;

class EditPendingOrderJsonOutput implements IEditPendingOrderOutput
{
    public function accountNotFound() : EditPendingOrderResult
    {
        return (new EditPendingOrderJsonResult())
            ->setCode(Code::TRADE_ACCOUNT_NOT_FOUND)
            ->setHttStatus(404);
    }

    public function accountNotTradable() : EditPendingOrderResult
    {
        return (new EditPendingOrderJsonResult())
            ->setCode(Code::TRADE_ACCOUNT_NOT_TRADABLE)
            ->setHttStatus(422);
    }

    public function pendingOrderNotFound() : EditPendingOrderResult
    {
        return (new EditPendingOrderJsonResult())
            ->setCode(Code::TRADE_PENDING_ORDER_NOT_FOUND)
            ->setHttStatus(422);
    }

    public function pendingOrderNotInPendingStatus() : EditPendingOrderResult
    {
        return (new EditPendingOrderJsonResult())
            ->setCode(Code::TRADE_IS_NOT_PENDING)
            ->setHttStatus(422);
    }

    public function symbolUnavailable() : EditPendingOrderResult
    {
        return (new EditPendingOrderJsonResult())
            ->setCode(Code::TRADE_UNAVAILABLE_SYMBOL)
            ->setHttStatus(422);
    }

    public function quoteNotFound() : EditPendingOrderResult
    {
        return (new EditPendingOrderJsonResult())
            ->setCode(Code::TRADE_UNEXPECTED_ERROR)
            ->setHttStatus(404);
    }

    public function engineTypeNotFound() : EditPendingOrderResult
    {
        return (new EditPendingOrderJsonResult())
            ->setCode(Code::TRADE_ENGINE_TYPE_NOT_FOUND)
            ->setHttStatus(422);
    }

    public function priceEqualCurrentPrice() : EditPendingOrderResult
    {
        return (new EditPendingOrderJsonResult())
            ->setCode(Code::TRADE_ORDER_EQUAL_CURRENT_PRICE)
            ->setHttStatus(422);
    }

    public function priceHigherThenCurrentPrice() : EditPendingOrderResult
    {
        return (new EditPendingOrderJsonResult())
            ->setCode(Code::TRADE_PENDING_ORDER_PRICE_HIGHER_CURRENT)
            ->setHttStatus(422);
    }

    public function priceLowerThenCurrentPrice() : EditPendingOrderResult
    {
        return (new EditPendingOrderJsonResult())
            ->setCode(Code::TRADE_PENDING_ORDER_PRICE_LOWER_CURRENT)
            ->setHttStatus(422);
    }

    public function stopLimitPriceHigherThenPrice() : EditPendingOrderResult
    {
        return (new EditPendingOrderJsonResult())
            ->setCode(Code::TRADE_STOP_LIMIT_PRICE_HIGHER_PRICE)
            ->setHttStatus(422);
    }

    public function stopLimitPriceLowerThenPrice() : EditPendingOrderResult
    {
        return (new EditPendingOrderJsonResult())
            ->setCode(Code::TRADE_STOP_LIMIT_PRICE_LOWER_PRICE)
            ->setHttStatus(422);
    }

    public function stopLossHigherThanPrice() : EditPendingOrderResult
    {
        return (new EditPendingOrderJsonResult())
            ->setCode(Code::TRADE_PENDING_ORDER_SL_HIGHER_PRICE)
            ->setHttStatus(422);
    }

    public function stopLossLowerThanPrice() : EditPendingOrderResult
    {
        return (new EditPendingOrderJsonResult())
            ->setCode(Code::TRADE_PENDING_ORDER_SL_LOWER_PRICE)
            ->setHttStatus(422);
    }

    public function takeProfitHigherThanPrice() : EditPendingOrderResult
    {
        return (new EditPendingOrderJsonResult())
            ->setCode(Code::TRADE_PENDING_ORDER_TP_HIGHER_PRICE)
            ->setHttStatus(422);
    }

    public function takeProfitLowerThanPrice() : EditPendingOrderResult
    {
        return (new EditPendingOrderJsonResult())
            ->setCode(Code::TRADE_PENDING_ORDER_TP_LOWER_PRICE)
            ->setHttStatus(422);
    }

    public function cannotEditOnMt(MTResultCode $resultCode) : EditPendingOrderResult
    {
        $code = $resultCode->getAppCode(Code::TRADE_ORDER_MT_EDIT_ERROR);

        $errorCodes = [
            Code::TRADE_INCORRECT_SL_TP,
            Code::TRADE_PENDING_ORDER_INCORRECT_PRICE,
            Code::TRADE_ORDER_MT_EDIT_ERROR,
        ];

        return (new EditPendingOrderJsonResult())
            ->setCode($code)
            ->setHttStatus(in_array($code, $errorCodes, true) ? 503 : 422);
    }

    public function cannotEditOnNt(NTResultCode $resultCode) : EditPendingOrderResult
    {
        $code = $resultCode->getAppCode(Code::TRADE_NT_ORDER_EDIT_ERROR);

        $errorCodes = [
            Code::TRADE_INCORRECT_SL_TP,
            Code::TRADE_PENDING_ORDER_INCORRECT_PRICE,
            Code::TRADE_NT_ORDER_EDIT_ERROR,
        ];

        return (new EditPendingOrderJsonResult())
            ->setCode($code)
            ->setHttStatus(in_array($code, $errorCodes, true) ? 503 : 422);
    }

    public function edited() : EditPendingOrderResult
    {
        return new EditPendingOrderJsonResult();
    }

    public function savingFailed() : EditPendingOrderResult
    {
        return (new EditPendingOrderJsonResult())
            ->setCode(Code::TRADE_UNEXPECTED_ERROR)
            ->setHttStatus(500);
    }
}
