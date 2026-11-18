<?php

declare(strict_types=1);

namespace Platform\Trade\Infrastructure\Adapters\AccountStatistics;

use Platform\Account\Api\Api;
use Platform\Trade\Domain\Adapters\AccountStatisticsAdapter as IAccountStatisticsAdapter;

class AccountStatisticsAdapter implements IAccountStatisticsAdapter
{
    public function __construct(
        private Api $api,
    ) {
    }

    public function addOpenTurnover(int $accountId, float $turnover, float $turnoverSystem) : bool
    {
        return $this->api->addOpenTurnover($accountId, $turnover, $turnoverSystem);
    }

    public function moveOpenToClosedTurnover(int $accountId, float $turnover, float $turnoverSystem) : bool
    {
        return $this->api->moveOpenToClosedTurnover($accountId, $turnover, $turnoverSystem);
    }

    public function moveClosedToOpenTurnover(int $accountId, float $turnover, float $turnoverSystem) : bool
    {
        return $this->api->moveClosedToOpenTurnover($accountId, $turnover, $turnoverSystem);
    }
}
