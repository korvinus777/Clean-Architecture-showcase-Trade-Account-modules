<?php

declare(strict_types=1);

namespace Platform\Trade\App\Services\EditMtPendingOrder;

interface EditMtPendingOrderInput
{
    public function edit(EditMtPendingOrderRequest $request) : EditMtPendingOrderResult;
}
