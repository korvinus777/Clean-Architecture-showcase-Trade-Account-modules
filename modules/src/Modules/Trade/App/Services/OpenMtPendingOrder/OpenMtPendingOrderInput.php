<?php

declare(strict_types=1);

namespace Platform\Trade\App\Services\OpenMtPendingOrder;

interface OpenMtPendingOrderInput
{
    public function open(OpenMtPendingOrderRequest $request) : OpenMtPendingOrderResult;
}
