<?php

declare(strict_types=1);

namespace Platform\Trade\Domain\Entities;

use Platform\Package\Components\Time\DateTime;
use Platform\Trade\Domain\ValueObjects\PendingOrderType;
use Platform\Trade\Domain\ValueObjects\PendingOrderStatus;

class PendingOrder
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

    public function toArray(array $with = []) : array
    {
        return array_merge($with, [
            'id'           => $this->id,
            'accountId'    => $this->accountId,
            'symbolId'     => $this->symbolId,
            'lots'         => $this->lots,
            'price'        => $this->price,
            'stopLoss'     => $this->stopLoss,
            'takeProfit'   => $this->takeProfit,
            'openTime'     => $this->openTime->getTimestampMs(),
            'closeTime'    => $this->closeTime?->getTimestampMs(),
            'direction'    => $this->type->isBuy() ? 1 : -1,
        ]);
    }
}
