<?php

declare(strict_types=1);

namespace Platform\Trade\Domain\Services\ProcessTradeOpening;

use Platform\Trade\Domain\Entities\Trade;

class ProcessTradeOpeningResult
{
    public function __construct(
        public ?Trade $trade,
        public bool   $alreadyProcessed = false,
    ) {
    }
}
