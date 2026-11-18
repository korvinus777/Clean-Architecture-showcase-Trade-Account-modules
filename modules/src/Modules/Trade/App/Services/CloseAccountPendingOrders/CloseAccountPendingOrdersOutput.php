<?php

declare(strict_types=1);

namespace Platform\Trade\App\Services\CloseAccountPendingOrders;

use Platform\Trade\Domain\Services\ClosePendingOrder\ClosePendingOrderResult;

interface CloseAccountPendingOrdersOutput
{
    /**
     * @param array<int, ClosePendingOrderResult> $results
     * @return CloseAccountPendingOrdersResult
     */
    public function done(array $results) : CloseAccountPendingOrdersResult;
}
