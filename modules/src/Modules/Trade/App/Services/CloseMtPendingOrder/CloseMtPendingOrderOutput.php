<?php

declare(strict_types=1);

namespace Platform\Trade\App\Services\CloseMtPendingOrder;

use Platform\Trade\Domain\Entities\PendingOrder;

interface CloseMtPendingOrderOutput
{
    public function failed() : CloseMtPendingOrderResult;

    public function closed(PendingOrder $order) : CloseMtPendingOrderResult;
}
