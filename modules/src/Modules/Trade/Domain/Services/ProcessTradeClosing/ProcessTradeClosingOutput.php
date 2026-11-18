<?php

declare(strict_types=1);

namespace Platform\Trade\Domain\Services\ProcessTradeClosing;

use Platform\Trade\Domain\Entities\ClosedTrade;

interface ProcessTradeClosingOutput
{
    public function processed(ClosedTrade $closedTrade) : ProcessTradeClosingResult;

    public function cannotProcess() : ProcessTradeClosingResult;

    public function alreadyProcessed() : ProcessTradeClosingResult;
}
