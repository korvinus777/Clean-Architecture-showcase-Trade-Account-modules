<?php

declare(strict_types=1);

namespace Platform\Trade\Domain\Services\ProcessPendingOrderClosing;

use Platform\Package\Components\Time\DateTime;
use Platform\Trade\Domain\Entities\PendingOrder;

class ProcessPendingOrderClosingRequest
{
    public function __construct(
        public PendingOrder $pendingOrder,
        public DateTime     $closeTime,
    ) {
    }
}
