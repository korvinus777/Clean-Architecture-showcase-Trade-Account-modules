<?php

declare(strict_types=1);

namespace Platform\Trade\App\Services\EditMtTrade;

use Platform\Trade\Domain\Entities\Trade;

interface EditMtTradeOutput
{
    public function edited(Trade $trade) : EditMtTradeResult;

    public function failed() : EditMtTradeResult;
}
