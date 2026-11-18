<?php

declare(strict_types=1);

namespace Platform\Trade\Domain\Services\ProcessPendingOrderEditing;

use Platform\Package\Components\Time\DateTime;
use Platform\Trade\Domain\Entities\PendingOrder;
use Platform\Trade\Domain\ValueObjects\PendingOrderType;

class ProcessPendingOrderEditingRequest
{
    public function __construct(
        public PendingOrder     $pendingOrder,
        public PendingOrderType $type,
        public float            $lots,
        public DateTime         $time,
        public bool             $isFromMt,
        public float            $price,
        public float            $stopLimitPrice,
        public ?float           $stopLoss = null,
        public ?float           $takeProfit = null,
    ) {
    }
}
