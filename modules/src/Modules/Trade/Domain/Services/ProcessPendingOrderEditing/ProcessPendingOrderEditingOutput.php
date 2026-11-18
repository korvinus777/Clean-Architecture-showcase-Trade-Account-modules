<?php

declare(strict_types=1);

namespace Platform\Trade\Domain\Services\ProcessPendingOrderEditing;

use Platform\Trade\Domain\Entities\PendingOrder;

interface ProcessPendingOrderEditingOutput
{
    public function processed(PendingOrder $order) : ProcessPendingOrderEditingResult;

    public function failed() : ProcessPendingOrderEditingResult;
}
