<?php

declare(strict_types=1);

namespace Platform\Trade\Domain\Services\OpenPendingOrder;

use Platform\Trade\Domain\Dto\MTResultCode;
use Platform\Trade\Domain\Dto\NTResultCode;
use Platform\Package\Components\Http\Response\Code;

interface OpenPendingOrderOutput
{
    public function opened(int $orderId, float $price) : OpenPendingOrderResult;

    public function symbolUnavailable() : OpenPendingOrderResult;

    public function incorrectVolume(Code $code, ?float $volume = null) : OpenPendingOrderResult;

    public function cannotOpenOnMt(MTResultCode $resultCode) : OpenPendingOrderResult;

    public function cannotOpenOnNt(NTResultCode $resultCode) : OpenPendingOrderResult;

    public function symbolMarketClosed() : OpenPendingOrderResult;

    public function symbolDisabled() : OpenPendingOrderResult;

    public function accountNotFound() : OpenPendingOrderResult;

    public function accountNotTradable() : OpenPendingOrderResult;

    public function unexpectedError() : OpenPendingOrderResult;

    public function priceEqualCurrentPrice() : OpenPendingOrderResult;

    public function stopLossHigherThanPrice() : OpenPendingOrderResult;

    public function stopLossLowerThanPrice() : OpenPendingOrderResult;

    public function takeProfitLowerThanPrice() : OpenPendingOrderResult;

    public function takeProfitHigherThanPrice() : OpenPendingOrderResult;

    public function engineTypeNotFound() : OpenPendingOrderResult;

    public function canNotProcessOrder() : OpenPendingOrderResult;
}
