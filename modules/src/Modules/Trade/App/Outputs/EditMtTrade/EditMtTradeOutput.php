<?php

declare(strict_types=1);

namespace Platform\Trade\App\Outputs\EditMtTrade;

use Platform\Trade\Domain\Entities\Trade;
use Platform\Trade\App\Services\EditMtTrade\EditMtTradeResult;
use Platform\Trade\App\Services\EditMtTrade\EditMtTradeOutput as IEditMtTradeOutput;

class EditMtTradeOutput implements IEditMtTradeOutput
{
    public function edited(Trade $trade) : EditMtTradeResult
    {
        return new EditMtTradeResult($trade);
    }

    public function failed() : EditMtTradeResult
    {
        return new EditMtTradeResult(null);
    }
}
