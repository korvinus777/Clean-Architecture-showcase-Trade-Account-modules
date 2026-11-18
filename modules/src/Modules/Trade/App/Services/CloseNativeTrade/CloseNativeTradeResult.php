<?php

declare(strict_types=1);

namespace Platform\Trade\App\Services\CloseNativeTrade;

use Platform\Trade\Domain\Entities\ClosedTrade;

class CloseNativeTradeResult
{
    public function __construct(
        public ?ClosedTrade $closedTrade = null,
        public \Throwable   $error = new \Exception(),
        public bool         $alreadyProcessed = false,
    ) {
    }
}
