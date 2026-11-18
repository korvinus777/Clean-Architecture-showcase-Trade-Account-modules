<?php

declare(strict_types=1);

namespace Platform\Trade\App\Outputs\CloseMtTrade;

use Platform\Trade\Domain\Entities\ClosedTrade;
use Platform\Trade\App\Services\CloseMtTrade\CloseMtTradeResult;
use Platform\Trade\App\Services\CloseMtTrade\CloseMtTradeOutput as ICloseMtTradeOutput;

class CloseMtTradeOutput implements ICloseMtTradeOutput
{
    public function closed(ClosedTrade $closedTrade) : CloseMtTradeResult
    {
        return new CloseMtTradeResult($closedTrade);
    }

    public function tradeNotFound() : CloseMtTradeResult
    {
        return new CloseMtTradeResult(error: new \Exception('Cannot close not existed trade'));
    }

    public function accountNotFound() : CloseMtTradeResult
    {
        return new CloseMtTradeResult(error: new \Exception('Cannot close trade of not existed account'));
    }

    public function cannotProcessClosing() : CloseMtTradeResult
    {
        return new CloseMtTradeResult(error: new \Exception('Cannot close trade due to unexpected error'));
    }

    public function alreadyProcessed() : CloseMtTradeResult
    {
        return new CloseMtTradeResult(alreadyProcessed: true);
    }
}
