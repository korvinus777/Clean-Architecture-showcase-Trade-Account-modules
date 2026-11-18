<?php

declare(strict_types=1);

namespace Platform\Trade\Domain\Services\ProcessPendingOrderClosing;

use Platform\Trade\Domain\Entities\PendingOrder;

class ProcessPendingOrderClosingResult
{
    public function __construct(
        public ?PendingOrder $order,
    ) {
    }
}
