<?php

declare(strict_types=1);

namespace Platform\Trade\App\Outputs\OpenMtPendingOrder;

use Platform\Trade\Domain\Entities\PendingOrder;
use Platform\Trade\App\Services\OpenMtPendingOrder\OpenMtPendingOrderResult;
use Platform\Trade\App\Services\OpenMtPendingOrder\OpenMtPendingOrderOutput as IOpenMtPendingOrderOutput;

class OpenMtPendingOrderOutput implements IOpenMtPendingOrderOutput
{
    public function success(PendingOrder $order) : OpenMtPendingOrderResult
    {
        return new OpenMtPendingOrderResult($order);
    }

    public function symbolNotAvailable() : OpenMtPendingOrderResult
    {
        return new OpenMtPendingOrderResult(null);
    }

    public function accountNotFound() : OpenMtPendingOrderResult
    {
        return new OpenMtPendingOrderResult(null);
    }

    public function canNotProcessOrder() : OpenMtPendingOrderResult
    {
        return new OpenMtPendingOrderResult(null);
    }
}
