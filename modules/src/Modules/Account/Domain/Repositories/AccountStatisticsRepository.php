<?php

declare(strict_types=1);

namespace Platform\Account\Domain\Repositories;

interface AccountStatisticsRepository
{
    public function findByAccountId(int $accountId) : ?\Platform\Account\Domain\Dto\AccountStatisticsDto;

    public function addOpenTurnover(int $accountId, float $turnover, float $turnoverSystem) : bool;

    public function moveOpenToClosedTurnover(int $accountId, float $turnover, float $turnoverSystem) : bool;

    public function moveClosedToOpenTurnover(int $accountId, float $turnover, float $turnoverSystem) : bool;

    /**
     * @param int[] $leadIds
     * @return array<int, \Platform\Account\Domain\Dto\AccountStatisticsDto> keyed by leadId
     */
    public function sumTurnoverOfLeads(array $leadIds) : array;
}
