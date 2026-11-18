<?php

declare(strict_types=1);

namespace Platform\Trade\App\Outputs\ProcessPendingOrderEditing;

use Platform\Trade\Domain\Entities\PendingOrder;
use Platform\Trade\Domain\Services\ProcessPendingOrderEditing\ProcessPendingOrderEditingResult;
use Platform\Trade\Domain\Services\ProcessPendingOrderEditing\ProcessPendingOrderEditingOutput as IProcessPendingOrderEditingOutput;

class ProcessPendingOrderEditingOutput implements IProcessPendingOrderEditingOutput
{
    public function processed(PendingOrder $order) : ProcessPendingOrderEditingResult
    {
        return new ProcessPendingOrderEditingResult($order);
    }

    public function failed() : ProcessPendingOrderEditingResult
    {
        return new ProcessPendingOrderEditingResult(null);
    }
}
