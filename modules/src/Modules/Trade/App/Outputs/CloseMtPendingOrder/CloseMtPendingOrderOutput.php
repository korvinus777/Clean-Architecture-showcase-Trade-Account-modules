<?php

declare(strict_types=1);

namespace Platform\Trade\App\Outputs\CloseMtPendingOrder;

use Platform\Trade\Domain\Entities\PendingOrder;
use Platform\Trade\App\Services\CloseMtPendingOrder\CloseMtPendingOrderResult;
use Platform\Trade\App\Services\CloseMtPendingOrder\CloseMtPendingOrderOutput as ICloseMtPendingOrderOutput;

class CloseMtPendingOrderOutput implements ICloseMtPendingOrderOutput
{
    public function failed() : CloseMtPendingOrderResult
    {
        return new CloseMtPendingOrderResult(null);
    }

    public function closed(PendingOrder $order) : CloseMtPendingOrderResult
    {
        return new CloseMtPendingOrderResult($order);
    }
}
