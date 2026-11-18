<?php

declare(strict_types=1);

namespace Platform\Trade\App\Outputs\CloseAccountPendingOrders;

use Platform\Trade\App\Services\CloseAccountPendingOrders\CloseAccountPendingOrdersOutput;
use Platform\Trade\App\Outputs\CloseAccountPendingOrders\Results\CloseAccountPendingOrdersJsonResult;

class CloseAccountPendingOrdersJsonOutput implements CloseAccountPendingOrdersOutput
{
    /**
     * @inheritDoc
     */
    public function done(array $results) : CloseAccountPendingOrdersJsonResult
    {
        return new CloseAccountPendingOrdersJsonResult($results);
    }
}
