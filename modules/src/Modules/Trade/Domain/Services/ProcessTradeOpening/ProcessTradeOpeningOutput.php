<?php

declare(strict_types=1);

namespace Platform\Trade\Domain\Services\ProcessTradeOpening;

use Platform\Trade\Domain\Entities\Trade;

interface ProcessTradeOpeningOutput
{
    public function processed(Trade $trade) : ProcessTradeOpeningResult;

    public function canNotProcess() : ProcessTradeOpeningResult;

    public function alreadyProcessed() : ProcessTradeOpeningResult;
}
