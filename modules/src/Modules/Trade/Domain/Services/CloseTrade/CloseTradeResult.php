<?php

declare(strict_types=1);

namespace Platform\Trade\Domain\Services\CloseTrade;

use Platform\Trade\Domain\Dto\MTResultCode;
use Platform\Trade\Domain\Dto\NTResultCode;

class CloseTradeResult
{
    public function __construct(
        public MTResultCode|NTResultCode|null $resultCode = null,
        public int                            $tradeId = 0,
        public float                          $price = 0,
    ) {
    }
}
