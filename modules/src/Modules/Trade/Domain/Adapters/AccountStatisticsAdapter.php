<?php

declare(strict_types=1);

namespace Platform\Trade\Domain\Adapters;

interface AccountStatisticsAdapter
{
    public function addOpenTurnover(int $accountId, float $turnover, float $turnoverSystem) : bool;

    public function moveOpenToClosedTurnover(int $accountId, float $turnover, float $turnoverSystem) : bool;

    public function moveClosedToOpenTurnover(int $accountId, float $turnover, float $turnoverSystem) : bool;
}
