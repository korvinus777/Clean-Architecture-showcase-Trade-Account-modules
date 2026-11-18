<?php

declare(strict_types=1);

namespace Platform\Trade\App\Outputs\ProcessTradeOpening;

use Platform\Trade\Domain\Entities\Trade;
use Platform\Trade\Domain\Services\ProcessTradeOpening\ProcessTradeOpeningResult;
use Platform\Trade\Domain\Services\ProcessTradeOpening\ProcessTradeOpeningOutput as IProcessTradeOpeningOutput;

class ProcessTradeOpeningOutput implements IProcessTradeOpeningOutput
{
    public function processed(Trade $trade) : ProcessTradeOpeningResult
    {
        return new ProcessTradeOpeningResult($trade);
    }

    public function canNotProcess() : ProcessTradeOpeningResult
    {
        return new ProcessTradeOpeningResult(null);
    }

    public function alreadyProcessed() : ProcessTradeOpeningResult
    {
        return new ProcessTradeOpeningResult(
            trade: null,
            alreadyProcessed: true,
        );
    }
}
