<?php

declare(strict_types=1);

namespace Platform\Trade\App\Services\CloseMtPendingOrder;

use Platform\Package\Components\Time\DateTime;

class CloseMtPendingOrderRequest
{
    public function __construct(
        public int       $orderId,
        public DateTime  $closeTime,
    ) {
    }
}
