<?php

declare(strict_types=1);

namespace Platform\Trade\App\Services\OpenMtTrade;

use Platform\Trade\Domain\Entities\Trade;

class OpenMtTradeResult
{
    public function __construct(
        public ?Trade $trade,
        public bool   $alreadyProcessed = false,
    ) {
    }
}
