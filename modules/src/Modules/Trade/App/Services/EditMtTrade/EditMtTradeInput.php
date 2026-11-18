<?php

declare(strict_types=1);

namespace Platform\Trade\App\Services\EditMtTrade;

interface EditMtTradeInput
{
    public function edit(EditMtTradeRequest $request) : EditMtTradeResult;
}
