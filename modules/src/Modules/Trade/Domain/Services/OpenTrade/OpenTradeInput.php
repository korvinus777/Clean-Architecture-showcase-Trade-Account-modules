<?php

declare(strict_types=1);

namespace Platform\Trade\Domain\Services\OpenTrade;

interface OpenTradeInput
{
    public function open(OpenTradeRequest $request) : OpenTradeResult;
}
