<?php

declare(strict_types=1);

namespace Platform\Trade\App\Services\DeleteOpenedTrade;

interface DeleteOpenedTradeOutput
{
    public function tradeNotFound() : DeleteOpenedTradeResult;

    public function closedTradeNotDeletes() : DeleteOpenedTradeResult;

    public function deleted() : DeleteOpenedTradeResult;

    public function unexpectedError() : DeleteOpenedTradeResult;
}
