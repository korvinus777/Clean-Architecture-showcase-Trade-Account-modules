<?php

declare(strict_types=1);

namespace Platform\Trade\Domain\Services\ProcessTradeClosing;

interface ProcessTradeClosingInput
{
    public function process(ProcessTradeClosingRequest $request) : ProcessTradeClosingResult;
}
