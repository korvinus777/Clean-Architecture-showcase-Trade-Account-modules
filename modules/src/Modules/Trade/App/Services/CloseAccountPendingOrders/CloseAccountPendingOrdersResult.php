<?php

declare(strict_types=1);

namespace Platform\Trade\App\Services\CloseAccountPendingOrders;

use Platform\Trade\Domain\Services\ClosePendingOrder\ClosePendingOrderResult;

class CloseAccountPendingOrdersResult
{
    /**
     * @param array<int, ClosePendingOrderResult> $results
     */
    public function __construct(
        public array $results = [],
    ) {
    }
}
