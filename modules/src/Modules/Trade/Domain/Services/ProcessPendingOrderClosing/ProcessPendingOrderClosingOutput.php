<?php

declare(strict_types=1);

namespace Platform\Trade\Domain\Services\ProcessPendingOrderClosing;

use Platform\Trade\Domain\Entities\PendingOrder;

interface ProcessPendingOrderClosingOutput
{
    public function processed(PendingOrder $order) : ProcessPendingOrderClosingResult;

    public function failed() : ProcessPendingOrderClosingResult;
}
