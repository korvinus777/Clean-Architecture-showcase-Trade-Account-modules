<?php

declare(strict_types=1);

namespace Platform\Trade\App\Services\EditMtPendingOrder;

use Platform\Package\Components\Time\DateTime;

class EditMtPendingOrderRequest
{
    public function __construct(
        public int      $orderId,
        public int      $type,
        public float    $lots,
        public DateTime $time,
        public float    $price,
        public float    $stopLimitPrice,
        public ?float   $stopLoss = null,
        public ?float   $takeProfit = null,
    ) {
    }
}
