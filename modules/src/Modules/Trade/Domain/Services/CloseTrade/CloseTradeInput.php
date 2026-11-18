<?php

declare(strict_types=1);

namespace Platform\Trade\Domain\Services\CloseTrade;

interface CloseTradeInput
{
    public function close(CloseTradeRequest $request) : CloseTradeResult;
}
