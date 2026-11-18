<?php

declare(strict_types=1);

namespace Platform\Trade\Domain\Services\OpenPendingOrder;

use Platform\Trade\Domain\Dto\MTResultCode;

class OpenPendingOrderResult
{
    public function __construct(
        public ?MTResultCode $resultCode = null,
        public int           $orderId = 0,
        public float         $price = 0,
    ) {
    }
}
