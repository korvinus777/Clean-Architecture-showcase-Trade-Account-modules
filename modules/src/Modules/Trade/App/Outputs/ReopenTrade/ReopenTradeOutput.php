<?php

declare(strict_types=1);

namespace Platform\Trade\App\Outputs\ReopenTrade;

use Platform\Trade\Domain\Services\ReopenTrade\ReopenTradeResult;
use Platform\Trade\Domain\Services\ReopenTrade\ReopenTradeOutput as IReopenTradeOutput;

class ReopenTradeOutput implements IReopenTradeOutput
{
    public function reopened() : ReopenTradeResult
    {
        return new ReopenTradeResult(true);
    }

    public function unexpectedError() : ReopenTradeResult
    {
        return new ReopenTradeResult(false);
    }
}
