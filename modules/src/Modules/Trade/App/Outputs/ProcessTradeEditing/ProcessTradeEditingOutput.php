<?php

declare(strict_types=1);

namespace Platform\Trade\App\Outputs\ProcessTradeEditing;

use Platform\Trade\Domain\Entities\Trade;
use Platform\Trade\Domain\Services\ProcessTradeEditing\ProcessTradeEditingResult;
use Platform\Trade\Domain\Services\ProcessTradeEditing\ProcessTradeEditingOutput as IProcessTradeEditingOutput;

class ProcessTradeEditingOutput implements IProcessTradeEditingOutput
{
    public function processed(Trade $trade) : ProcessTradeEditingResult
    {
        return new ProcessTradeEditingResult($trade);
    }

    public function failed() : ProcessTradeEditingResult
    {
        return new ProcessTradeEditingResult(null);
    }
}
