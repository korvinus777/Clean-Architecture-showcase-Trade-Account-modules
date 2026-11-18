<?php

declare(strict_types=1);

namespace Platform\Trade\Domain\Services\ClosePendingOrder;

use Platform\Trade\Domain\Dto\MTResultCode;
use Platform\Trade\Domain\Dto\NTResultCode;

interface ClosePendingOrderOutput
{
    public function accountNotFound() : ClosePendingOrderResult;

    public function accountNotTradable() : ClosePendingOrderResult;

    public function pendingOrderNotFound() : ClosePendingOrderResult;

    public function pendingOrderNotInPendingStatus() : ClosePendingOrderResult;

    public function symbolUnavailable() : ClosePendingOrderResult;

    public function symbolDisabled() : ClosePendingOrderResult;

    public function symbolMarketClosed() : ClosePendingOrderResult;

    public function engineTypeNotFound() : ClosePendingOrderResult;

    public function cannotCloseOnMt(MTResultCode $resultCode) : ClosePendingOrderResult;

    public function cannotCloseOnNt(NTResultCode $resultCode) : ClosePendingOrderResult;

    public function closed(int $pendingOrderId) : ClosePendingOrderResult;

    public function asyncClosing() : ClosePendingOrderResult;
}
