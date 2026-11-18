<?php

declare(strict_types=1);

namespace Platform\Trade\App\Services\OpenMtTrade;

interface OpenMtTradeInput
{
    public function open(OpenMtTradeRequest $request) : OpenMtTradeResult;
}
