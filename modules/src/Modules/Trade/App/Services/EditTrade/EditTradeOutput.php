<?php

declare(strict_types=1);

namespace Platform\Trade\App\Services\EditTrade;

use Platform\Trade\Domain\Dto\AccountDto;
use Platform\Trade\Domain\Dto\MTResultCode;
use Platform\Trade\Domain\Dto\NTResultCode;

interface EditTradeOutput
{
    public function edited() : EditTradeResult;

    public function tradeNotFound() : EditTradeResult;

    public function quoteNotFound() : EditTradeResult;

    public function cannotEditOnMt(MTResultCode $resultCode) : EditTradeResult;

    public function cannotEditOnNt(NTResultCode $resultCode) : EditTradeResult;

    public function stopLossHigherThanCurrentPrice() : EditTradeResult;

    public function stopLossLowerThanCurrentPrice() : EditTradeResult;

    public function takeProfitLowerThanCurrentPrice() : EditTradeResult;

    public function takeProfitHigherThanCurrentPrice() : EditTradeResult;

    public function accountNotAvailable(?AccountDto $account) : EditTradeResult;

    public function symbolUnavailable() : EditTradeResult;

    public function savingFailed() : EditTradeResult;

    public function engineTypeNotFound() : EditTradeResult;
}
