<?php

declare(strict_types=1);

namespace Platform\Trade\Api\Dto;

use Platform\Package\Components\Time\DateTime;
use Platform\Trade\Domain\Entities\PendingOrder;
use Platform\Trade\Domain\ValueObjects\PendingOrderType;
use Platform\Trade\Domain\ValueObjects\PendingOrderStatus;

class OrderDto
{
    public function __construct(
        public int                $id,
        public int                $accountId,
        public int                $symbolId,
        public float              $lots,
        public float              $price,
        public float              $stopLimitPrice,
        public float              $stopLoss,
        public float              $takeProfit,
        public PendingOrderType   $type,
        public PendingOrderStatus $status,
        public DateTime           $openTime,
        public ?DateTime          $closeTime = null,
    ) {
    }

    public static function fromEntity(PendingOrder $order) : self
    {
        return new self(
            $order->id,
            $order->accountId,
            $order->symbolId,
            $order->lots,
            $order->price,
            $order->stopLimitPrice,
            $order->stopLoss,
            $order->takeProfit,
            $order->type,
            $order->status,
            $order->openTime,
            $order->closeTime,
        );
    }
}
