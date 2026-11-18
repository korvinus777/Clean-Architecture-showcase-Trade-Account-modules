<?php

declare(strict_types=1);

namespace Platform\Trade\App\Outputs\ProcessPendingOrderOpening;

use Platform\Trade\Domain\Entities\PendingOrder;
use Platform\Trade\Domain\Services\ProcessPendingOrderOpening\ProcessPendingOrderOpeningResult;
use Platform\Trade\Domain\Services\ProcessPendingOrderOpening\ProcessPendingOrderOpeningOutput as IProcessPendingOrderOpeningOutput;

class ProcessPendingOrderOpeningOutput implements IProcessPendingOrderOpeningOutput
{
    public function processed(PendingOrder $order) : ProcessPendingOrderOpeningResult
    {
        return new ProcessPendingOrderOpeningResult($order);
    }

    public function canNotProcess() : ProcessPendingOrderOpeningResult
    {
        return new ProcessPendingOrderOpeningResult(null);
    }
}
