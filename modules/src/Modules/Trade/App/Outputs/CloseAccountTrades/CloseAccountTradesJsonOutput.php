<?php

declare(strict_types=1);

namespace Platform\Trade\App\Outputs\CloseAccountTrades;

use Platform\Trade\App\Services\CloseAccountTrades\CloseAccountTradesOutput;
use Platform\Trade\App\Outputs\CloseAccountTrades\Results\CloseAccountTradesJsonResult;

class CloseAccountTradesJsonOutput implements CloseAccountTradesOutput
{
    /**
     * @inheritDoc
     */
    public function done(array $results) : CloseAccountTradesJsonResult
    {
        return new CloseAccountTradesJsonResult($results);
    }
}
