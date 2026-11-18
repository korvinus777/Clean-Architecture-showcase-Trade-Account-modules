<?php

declare(strict_types=1);

namespace Platform\Trade\App\Services\CloseMtTrade;

use Platform\Trade\Domain\Entities\ClosedTrade;

interface CloseMtTradeOutput
{
    public function closed(ClosedTrade $closedTrade) : CloseMtTradeResult;

    public function tradeNotFound() : CloseMtTradeResult;

    public function accountNotFound() : CloseMtTradeResult;

    public function cannotProcessClosing() : CloseMtTradeResult;

    public function alreadyProcessed() : CloseMtTradeResult;
}
