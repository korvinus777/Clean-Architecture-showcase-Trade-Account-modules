<?php

declare(strict_types=1);

namespace Platform\Trade\Domain\Services\EditPendingOrder;

interface EditPendingOrderInput
{
    public function edit(EditPendingOrderRequest $request) : EditPendingOrderResult;
}
