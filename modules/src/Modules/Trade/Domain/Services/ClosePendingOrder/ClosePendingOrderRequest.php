<?php

declare(strict_types=1);

namespace Platform\Trade\Domain\Services\ClosePendingOrder;

class ClosePendingOrderRequest
{
    public function __construct(
        public int  $pendingOrderId,
        public int  $accountId,
        public bool $deferred = false,
    ) {
    }
}
