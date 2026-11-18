<?php

declare(strict_types=1);

namespace Platform\Trade\App\Services\CloseAccountPendingOrders;

class CloseAccountPendingOrdersRequest
{
    public function __construct(
        public int $accountId,
    ) {
    }
}
