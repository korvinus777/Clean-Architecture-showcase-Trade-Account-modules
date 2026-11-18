<?php

declare(strict_types=1);

namespace Platform\Trade\App\Services\CloseMtTrade;

use Platform\Trade\Domain\Entities\ClosedTrade;

class CloseMtTradeResult
{
    public function __construct(
        public ?ClosedTrade $closedTrade = null,
        public \Throwable   $error = new \Exception(),
        public bool         $alreadyProcessed = false,
    ) {
    }
}
