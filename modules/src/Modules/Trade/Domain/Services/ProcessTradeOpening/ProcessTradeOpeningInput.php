<?php

declare(strict_types=1);

namespace Platform\Trade\Domain\Services\ProcessTradeOpening;

interface ProcessTradeOpeningInput
{
    public function process(ProcessTradeOpeningRequest $request) : ProcessTradeOpeningResult;
}
