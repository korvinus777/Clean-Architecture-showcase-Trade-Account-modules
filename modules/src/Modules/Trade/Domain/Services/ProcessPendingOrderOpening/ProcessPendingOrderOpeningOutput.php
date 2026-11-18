<?php

declare(strict_types=1);

namespace Platform\Trade\Domain\Services\ProcessPendingOrderOpening;

use Platform\Trade\Domain\Entities\PendingOrder;

interface ProcessPendingOrderOpeningOutput
{
    public function processed(PendingOrder $order) : ProcessPendingOrderOpeningResult;

    public function canNotProcess() : ProcessPendingOrderOpeningResult;
}
