<?php

declare(strict_types=1);

namespace Platform\Trade\App\Services\OpenMtPendingOrder;

use Platform\Trade\Domain\Entities\PendingOrder;

class OpenMtPendingOrderResult
{
    public function __construct(
        public ?PendingOrder $order,
    ) {
    }
}
