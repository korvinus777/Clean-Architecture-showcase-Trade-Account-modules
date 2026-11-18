<?php

declare(strict_types=1);

namespace Platform\Trade\Domain\Services\ProcessPendingOrderClosing;

interface ProcessPendingOrderClosingInput
{
    public function process(ProcessPendingOrderClosingRequest $request) : ProcessPendingOrderClosingResult;
}
