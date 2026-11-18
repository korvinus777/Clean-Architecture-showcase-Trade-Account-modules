<?php

declare(strict_types=1);

namespace Platform\Trade\Domain\Services\ReopenTrade;

interface ReopenTradeOutput
{
    public function reopened() : ReopenTradeResult;

    public function unexpectedError() : ReopenTradeResult;
}
