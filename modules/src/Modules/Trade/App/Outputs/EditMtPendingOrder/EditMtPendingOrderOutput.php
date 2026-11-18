<?php

declare(strict_types=1);

namespace Platform\Trade\App\Outputs\EditMtPendingOrder;

use Platform\Trade\Domain\Entities\PendingOrder;
use Platform\Trade\App\Services\EditMtPendingOrder\EditMtPendingOrderResult;
use Platform\Trade\App\Services\EditMtPendingOrder\EditMtPendingOrderOutput as IEditMtPendingOrderOutput;

class EditMtPendingOrderOutput implements IEditMtPendingOrderOutput
{
    public function edited(PendingOrder $order) : EditMtPendingOrderResult
    {
        return new EditMtPendingOrderResult($order);
    }

    public function pendingOrderNotFound() : EditMtPendingOrderResult
    {
        return new EditMtPendingOrderResult(null);
    }

    public function pendingOrderNotInPendingStatus() : EditMtPendingOrderResult
    {
        return new EditMtPendingOrderResult(null);
    }

    public function orderTypeDoesNotExist() : EditMtPendingOrderResult
    {
        return new EditMtPendingOrderResult(null);
    }

    public function canNotProcessedEdit() : EditMtPendingOrderResult
    {
        return new EditMtPendingOrderResult(null);
    }
}
