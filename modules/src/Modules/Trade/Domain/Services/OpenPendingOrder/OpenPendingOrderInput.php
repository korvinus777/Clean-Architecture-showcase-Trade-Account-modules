<?php

declare(strict_types=1);

namespace Platform\Trade\Domain\Services\OpenPendingOrder;

interface OpenPendingOrderInput
{
    public function open(OpenPendingOrderRequest $request) : OpenPendingOrderResult;
}
