<?php

declare(strict_types=1);

namespace Platform\Trade\App\Services\DeleteOpenedTrade;

interface DeleteOpenedTradeInput
{
    public function delete(DeleteOpenedTradeRequest $request) : DeleteOpenedTradeResult;
}
