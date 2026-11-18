<?php

declare(strict_types=1);

namespace Platform\Trade\Domain\Services\ProcessPendingOrderOpening;

use Platform\Trade\Domain\Entities\PendingOrder;

class ProcessPendingOrderOpeningResult
{
    public function __construct(
        public ?PendingOrder $order,
    ) {
    }
}
