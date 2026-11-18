<?php

declare(strict_types=1);

namespace Platform\Trade\App\Outputs\OpenMtTrade;

use Platform\Trade\Domain\Entities\Trade;
use Platform\Trade\App\Services\OpenMtTrade\OpenMtTradeResult;
use Platform\Trade\App\Services\OpenMtTrade\OpenMtTradeOutput as IOpenMtTradeOutput;

class OpenMtTradeOutput implements IOpenMtTradeOutput
{
    public function opened(Trade $trade) : OpenMtTradeResult
    {
        return new OpenMtTradeResult($trade);
    }

    public function symbolNotAvailable() : OpenMtTradeResult
    {
        return new OpenMtTradeResult(null);
    }

    public function accountNotFound() : OpenMtTradeResult
    {
        return new OpenMtTradeResult(null);
    }

    public function canNotProcessTrade() : OpenMtTradeResult
    {
        return new OpenMtTradeResult(null);
    }

    public function alreadyProcessed() : OpenMtTradeResult
    {
        return new OpenMtTradeResult(
            trade: null,
            alreadyProcessed: true,
        );
    }
}
