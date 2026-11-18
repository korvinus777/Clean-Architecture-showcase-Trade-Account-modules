<?php

declare(strict_types=1);

namespace Platform\Trade\App\Services\EditTrade;

interface EditTradeInput
{
    public function edit(EditTradeRequest $request) : EditTradeResult;
}
