<?php

declare(strict_types=1);

namespace Platform\Trade\App\Outputs\EditTrade;

use Platform\Trade\Domain\Dto\AccountDto;
use Platform\Trade\Domain\Dto\MTResultCode;
use Platform\Trade\Domain\Dto\NTResultCode;
use Platform\Package\Components\Http\Response\Code;
use Platform\Trade\App\Services\EditTrade\EditTradeOutput;
use Platform\Trade\App\Outputs\EditTrade\Results\EditTradeJsonResult;

class EditTradeJsonOutput implements EditTradeOutput
{
    public function edited() : EditTradeJsonResult
    {
        return new EditTradeJsonResult(true);
    }

    public function tradeNotFound() : EditTradeJsonResult
    {
        return (new EditTradeJsonResult(false))
            ->setHttStatus(404)
            ->setCode(Code::TRADE_NOT_FOUND);
    }

    public function quoteNotFound() : EditTradeJsonResult
    {
        return (new EditTradeJsonResult(false))
            ->setHttStatus(404)
            ->setCode(Code::TRADE_UNEXPECTED_ERROR);
    }

    public function cannotEditOnMt(MTResultCode $resultCode) : EditTradeJsonResult
    {
        $code = $resultCode->getAppCode(Code::TRADE_MT_EDIT_ERROR);

        $isUnexpectedError = $code === Code::TRADE_MT_EDIT_ERROR;

        return (new EditTradeJsonResult(false))
            ->setCode($code)
            ->setHttStatus($isUnexpectedError ? 503 : 422);
    }

    public function cannotEditOnNt(NTResultCode $resultCode) : EditTradeJsonResult
    {
        $code = $resultCode->getAppCode(Code::NT_TRADE_EDIT_ERROR);

        $isUnexpectedError = $code === Code::NT_TRADE_EDIT_ERROR;

        return (new EditTradeJsonResult(false))
            ->setCode($code)
            ->setHttStatus($isUnexpectedError ? 503 : 422);
    }

    public function stopLossHigherThanCurrentPrice() : EditTradeJsonResult
    {
        return (new EditTradeJsonResult(false))
            ->setHttStatus(422)
            ->setCode(Code::TRADE_SL_HIGHER_CURRENT_PRICE);
    }

    public function stopLossLowerThanCurrentPrice() : EditTradeJsonResult
    {
        return (new EditTradeJsonResult(false))
            ->setHttStatus(422)
            ->setCode(Code::TRADE_SL_LOWER_CURRENT_PRICE);
    }

    public function takeProfitHigherThanCurrentPrice() : EditTradeJsonResult
    {
        return (new EditTradeJsonResult(false))
            ->setHttStatus(422)
            ->setCode(Code::TRADE_TP_HIGHER_CURRENT_PRICE);
    }

    public function takeProfitLowerThanCurrentPrice() : EditTradeJsonResult
    {
        return (new EditTradeJsonResult(false))
            ->setHttStatus(422)
            ->setCode(Code::TRADE_TP_LOWER_CURRENT_PRICE);
    }

    public function accountNotAvailable(?AccountDto $account) : EditTradeJsonResult
    {
        return (new EditTradeJsonResult(false))
            ->setHttStatus(422)
            ->setCode(is_null($account) ? Code::TRADE_ACCOUNT_NOT_FOUND : Code::TRADE_ACCOUNT_NOT_TRADABLE);
    }

    public function symbolUnavailable() : EditTradeJsonResult
    {
        return (new EditTradeJsonResult(false))
            ->setHttStatus(422)
            ->setCode(Code::TRADE_UNAVAILABLE_SYMBOL);
    }

    public function savingFailed() : EditTradeJsonResult
    {
        return (new EditTradeJsonResult(false))
            ->setHttStatus(500)
            ->setCode(Code::TRADE_UNEXPECTED_ERROR);
    }

    public function engineTypeNotFound() : EditTradeJsonResult
    {
        return (new EditTradeJsonResult(false))
            ->setCode(Code::TRADE_ENGINE_TYPE_NOT_FOUND)
            ->setHttStatus(422);
    }
}
