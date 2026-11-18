<?php

declare(strict_types=1);

namespace Platform\Trade\App\Services\EditMtPendingOrder;

use Platform\Trade\Domain\Entities\PendingOrder;

interface EditMtPendingOrderOutput
{
    public function pendingOrderNotFound() : EditMtPendingOrderResult;

    public function pendingOrderNotInPendingStatus() : EditMtPendingOrderResult;

    public function orderTypeDoesNotExist() : EditMtPendingOrderResult;

    public function canNotProcessedEdit() : EditMtPendingOrderResult;

    public function edited(PendingOrder $order) : EditMtPendingOrderResult;
}
