<?php

declare(strict_types=1);

namespace Platform\Trade\Domain\Services\ClosePendingOrder;

interface ClosePendingOrderInput
{
    public function close(ClosePendingOrderRequest $request) : ClosePendingOrderResult;
}
