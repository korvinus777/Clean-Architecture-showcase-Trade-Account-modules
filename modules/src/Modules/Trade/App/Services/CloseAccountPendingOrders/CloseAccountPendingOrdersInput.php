<?php

declare(strict_types=1);

namespace Platform\Trade\App\Services\CloseAccountPendingOrders;

interface CloseAccountPendingOrdersInput
{
    public function close(CloseAccountPendingOrdersRequest $request) : CloseAccountPendingOrdersResult;
}
