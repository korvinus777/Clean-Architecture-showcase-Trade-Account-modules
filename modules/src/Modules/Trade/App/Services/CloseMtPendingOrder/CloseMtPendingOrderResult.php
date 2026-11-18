<?php

declare(strict_types=1);

namespace Platform\Trade\App\Services\CloseMtPendingOrder;

use Platform\Trade\Domain\Entities\PendingOrder;

class CloseMtPendingOrderResult
{
    public function __construct(
        public ?PendingOrder $order = null,
    ) {
    }
}
