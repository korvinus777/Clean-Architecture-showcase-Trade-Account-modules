<?php

declare(strict_types=1);

namespace Platform\Trade\Domain\Services\ProcessPendingOrderEditing;

interface ProcessPendingOrderEditingInput
{
    public function process(ProcessPendingOrderEditingRequest $request) : ProcessPendingOrderEditingResult;
}
