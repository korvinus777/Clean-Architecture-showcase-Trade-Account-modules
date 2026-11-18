<?php

declare(strict_types=1);

namespace Platform\Trade\App\Outputs\ProcessPendingOrderClosing;

use Platform\Trade\Domain\Entities\PendingOrder;
use Platform\Trade\Domain\Services\ProcessPendingOrderClosing\ProcessPendingOrderClosingResult;
use Platform\Trade\Domain\Services\ProcessPendingOrderClosing\ProcessPendingOrderClosingOutput as IProcessPendingOrderClosingOutput;

class ProcessPendingOrderClosingOutput implements IProcessPendingOrderClosingOutput
{
    public function processed(PendingOrder $order) : ProcessPendingOrderClosingResult
    {
        return new ProcessPendingOrderClosingResult($order);
    }

    public function failed() : ProcessPendingOrderClosingResult
    {
        return new ProcessPendingOrderClosingResult(null);
    }
}
