<?php

declare(strict_types=1);

namespace Platform\Trade\App\Services\ActivateMtOrder;

interface ActivateMtOrderInput
{
    public function activate(ActivateMtOrderRequest $request) : ActivateMtOrderResult;
}
