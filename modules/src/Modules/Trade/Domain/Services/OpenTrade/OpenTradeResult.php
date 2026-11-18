<?php

declare(strict_types=1);

namespace Platform\Trade\Domain\Services\OpenTrade;

use Platform\Trade\Domain\Dto\MTResultCode;
use Platform\Trade\Domain\Dto\NTResultCode;

class OpenTradeResult
{
    public function __construct(
        public MTResultCode|NTResultCode|null $resultCode = null,
        public int                            $tradeId = 0,
        public float                          $price = 0,
    ) {
    }
}
