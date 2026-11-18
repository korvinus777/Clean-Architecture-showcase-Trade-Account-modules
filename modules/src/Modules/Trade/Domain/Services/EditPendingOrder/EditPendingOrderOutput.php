<?php

declare(strict_types=1);

namespace Platform\Trade\Domain\Services\EditPendingOrder;

use Platform\Trade\Domain\Dto\MTResultCode;
use Platform\Trade\Domain\Dto\NTResultCode;

interface EditPendingOrderOutput
{
    public function accountNotFound() : EditPendingOrderResult;

    public function accountNotTradable() : EditPendingOrderResult;

    public function pendingOrderNotFound() : EditPendingOrderResult;

    public function pendingOrderNotInPendingStatus() : EditPendingOrderResult;

    public function symbolUnavailable() : EditPendingOrderResult;

    public function quoteNotFound() : EditPendingOrderResult;

    public function engineTypeNotFound() : EditPendingOrderResult;

    public function stopLossHigherThanPrice() : EditPendingOrderResult;

    public function stopLossLowerThanPrice() : EditPendingOrderResult;

    public function takeProfitLowerThanPrice() : EditPendingOrderResult;

    public function takeProfitHigherThanPrice() : EditPendingOrderResult;

    public function priceEqualCurrentPrice() : EditPendingOrderResult;

    public function priceHigherThenCurrentPrice() : EditPendingOrderResult;

    public function priceLowerThenCurrentPrice() : EditPendingOrderResult;

    public function stopLimitPriceHigherThenPrice() : EditPendingOrderResult;

    public function stopLimitPriceLowerThenPrice() : EditPendingOrderResult;

    public function cannotEditOnMt(MTResultCode $resultCode) : EditPendingOrderResult;

    public function cannotEditOnNt(NTResultCode $resultCode) : EditPendingOrderResult;

    public function edited() : EditPendingOrderResult;

    public function savingFailed() : EditPendingOrderResult;
}
