<?php

declare(strict_types=1);

namespace Platform\Trade\App\Services\OpenMtPendingOrder;

use Platform\Package\Components\Time\DateTime;
use Platform\Trade\Domain\ValueObjects\PendingOrderType;

class OpenMtPendingOrderRequest
{
    public function __construct(
        public int              $orderId,
        public string           $symbol,
        public int              $login,
        public float            $lots,
        public float            $price,
        public float            $stopLimitPrice,
        public float            $stopLoss,
        public float            $takeProfit,
        public PendingOrderType $type,
        public DateTime         $time,
    ) {
    }
}
