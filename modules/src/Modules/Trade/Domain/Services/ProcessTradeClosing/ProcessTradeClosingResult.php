<?php

declare(strict_types=1);

namespace Platform\Trade\Domain\Services\ProcessTradeClosing;

use Platform\Trade\Domain\Entities\ClosedTrade;

class ProcessTradeClosingResult
{
    public function __construct(
        public ?ClosedTrade $closedTrade,
        public bool         $alreadyProcessed = false,
    ) {
    }
}
