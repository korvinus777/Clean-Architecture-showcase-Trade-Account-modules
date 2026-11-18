<?php

declare(strict_types=1);

namespace Platform\Trade\App\Outputs\ProcessTradeClosing;

use Platform\Trade\Domain\Entities\ClosedTrade;
use Platform\Trade\Domain\Services\ProcessTradeClosing\ProcessTradeClosingResult;
use Platform\Trade\Domain\Services\ProcessTradeClosing\ProcessTradeClosingOutput as IProcessTradeClosingOutput;

class ProcessTradeClosingOutput implements IProcessTradeClosingOutput
{
    public function processed(ClosedTrade $closedTrade) : ProcessTradeClosingResult
    {
        return new ProcessTradeClosingResult($closedTrade);
    }

    public function cannotProcess() : ProcessTradeClosingResult
    {
        return new ProcessTradeClosingResult(null);
    }

    public function alreadyProcessed() : ProcessTradeClosingResult
    {
        return new ProcessTradeClosingResult(
            closedTrade: null,
            alreadyProcessed: true,
        );
    }
}
