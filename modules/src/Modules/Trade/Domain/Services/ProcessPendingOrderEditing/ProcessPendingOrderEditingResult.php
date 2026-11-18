<?php

declare(strict_types=1);

namespace Platform\Trade\Domain\Services\ProcessPendingOrderEditing;

use Platform\Trade\Domain\Entities\PendingOrder;

class ProcessPendingOrderEditingResult
{
    public function __construct(
        public ?PendingOrder $order,
    ) {
    }
}
