<?php

declare(strict_types=1);

namespace Platform\Trade\App\Services\CloseMtPendingOrder;

interface CloseMtPendingOrderInput
{
    public function close(CloseMtPendingOrderRequest $request) : CloseMtPendingOrderResult;
}
