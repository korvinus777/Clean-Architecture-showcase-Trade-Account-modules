<?php

declare(strict_types=1);

namespace Platform\Trade\Domain\Services\ProcessPendingOrderOpening;

interface ProcessPendingOrderOpeningInput
{
    public function process(ProcessPendingOrderOpeningRequest $request) : ProcessPendingOrderOpeningResult;
}
