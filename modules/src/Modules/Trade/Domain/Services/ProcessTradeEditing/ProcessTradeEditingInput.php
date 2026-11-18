<?php

declare(strict_types=1);

namespace Platform\Trade\Domain\Services\ProcessTradeEditing;

interface ProcessTradeEditingInput
{
    public function process(ProcessTradeEditingRequest $request) : ProcessTradeEditingResult;
}
