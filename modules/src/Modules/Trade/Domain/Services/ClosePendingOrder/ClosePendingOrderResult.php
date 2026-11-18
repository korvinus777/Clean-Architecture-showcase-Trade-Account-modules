<?php

declare(strict_types=1);

namespace Platform\Trade\Domain\Services\ClosePendingOrder;

use Platform\Trade\Domain\Dto\MTResultCode;

class ClosePendingOrderResult
{
    public function __construct(
        public ?MTResultCode $resultCode = null,
        public int           $pendingOrderId = 0,
    ) {
    }
}
