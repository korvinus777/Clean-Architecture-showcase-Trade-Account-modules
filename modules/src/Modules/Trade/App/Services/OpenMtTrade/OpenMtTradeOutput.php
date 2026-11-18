<?php

declare(strict_types=1);

namespace Platform\Trade\App\Services\OpenMtTrade;

use Platform\Trade\Domain\Entities\Trade;

interface OpenMtTradeOutput
{
    public function opened(Trade $trade) : OpenMtTradeResult;

    public function symbolNotAvailable() : OpenMtTradeResult;

    public function accountNotFound() : OpenMtTradeResult;

    public function canNotProcessTrade() : OpenMtTradeResult;

    public function alreadyProcessed() : OpenMtTradeResult;
}
