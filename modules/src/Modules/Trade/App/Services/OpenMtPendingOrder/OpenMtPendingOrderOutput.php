<?php

declare(strict_types=1);

namespace Platform\Trade\App\Services\OpenMtPendingOrder;

use Platform\Trade\Domain\Entities\PendingOrder;

interface OpenMtPendingOrderOutput
{
    public function success(PendingOrder $order) : OpenMtPendingOrderResult;

    public function symbolNotAvailable() : OpenMtPendingOrderResult;

    public function accountNotFound() : OpenMtPendingOrderResult;

    public function canNotProcessOrder() : OpenMtPendingOrderResult;
}
