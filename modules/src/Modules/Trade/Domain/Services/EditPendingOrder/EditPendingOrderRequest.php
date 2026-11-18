<?php

declare(strict_types=1);

namespace Platform\Trade\Domain\Services\EditPendingOrder;

class EditPendingOrderRequest
{
    public function __construct(
        public int    $orderId,
        public int    $accountId,
        public ?float $stopLoss,
        public ?float $takeProfit,
        public ?float $price,
    ) {
    }
}
