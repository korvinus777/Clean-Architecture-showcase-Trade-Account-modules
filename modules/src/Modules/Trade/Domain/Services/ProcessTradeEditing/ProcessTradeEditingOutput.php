<?php

declare(strict_types=1);

namespace Platform\Trade\Domain\Services\ProcessTradeEditing;

use Platform\Trade\Domain\Entities\Trade;

interface ProcessTradeEditingOutput
{
    public function processed(Trade $trade) : ProcessTradeEditingResult;

    public function failed() : ProcessTradeEditingResult;
}
