<?php

declare(strict_types=1);

namespace Platform\Account\Infrastructure\Repositories;

use Platform\Package\Components\Collection\Arr;
use Platform\Account\Domain\Dto\AccountStatisticsDto;
use Platform\Package\Components\Log\Contracts\Logger;
use Platform\Account\Infrastructure\Models\AccountModel;
use Platform\Account\Infrastructure\Models\AccountStatisticsModel;
use Platform\Account\Domain\Repositories\AccountStatisticsRepository as IAccountStatisticsRepository;

class AccountStatisticsRepository implements IAccountStatisticsRepository
{
    public function __construct(
        private Logger $logger,
    ) {
        $this->logger->withContext([
            'module'  => 'account',
            'service' => class_basename(self::class),
        ]);
    }

    public function addOpenTurnover(int $accountId, float $turnover, float $turnoverSystem) : bool
    {
        return $this->increment($accountId, [
            'openTurnover'       => $turnover,
            'openTurnoverSystem' => $turnoverSystem,
        ]);
    }

    public function findByAccountId(int $accountId) : ?AccountStatisticsDto
    {
        /** @var AccountStatisticsModel|null $stats */
        $stats = AccountStatisticsModel::query()
            ->where('accountId', $accountId)
            ->first();

        if ( ! $stats) {
            return null;
        }

        return new AccountStatisticsDto(
            openTurnover: (float) $stats->openTurnover,
            openTurnoverSystem: (float) $stats->openTurnoverSystem,
            closedTurnover: (float) $stats->closedTurnover,
            closedTurnoverSystem: (float) $stats->closedTurnoverSystem,
        );
    }

    public function moveOpenToClosedTurnover(int $accountId, float $turnover, float $turnoverSystem) : bool
    {
        return $this->increment($accountId, [
            'openTurnover'         => -$turnover,
            'openTurnoverSystem'   => -$turnoverSystem,
            'closedTurnover'       => $turnover,
            'closedTurnoverSystem' => $turnoverSystem,
        ]);
    }

    public function moveClosedToOpenTurnover(int $accountId, float $turnover, float $turnoverSystem) : bool
    {
        return $this->increment($accountId, [
            'openTurnover'         => $turnover,
            'openTurnoverSystem'   => $turnoverSystem,
            'closedTurnover'       => -$turnover,
            'closedTurnoverSystem' => -$turnoverSystem,
        ]);
    }

    public function sumTurnoverOfLeads(array $leadIds) : array
    {
        if (empty($leadIds)) {
            return [];
        }

        $rows = AccountModel::query()
            ->selectRaw('accounts.leadId, SUM(ast.openTurnover) as openTurnover, SUM(ast.closedTurnover) as closedTurnover')
            ->join('accountsStatistics as ast', 'accounts.id', '=', 'ast.accountId')
            ->whereIn('accounts.leadId', $leadIds)
            ->groupBy('accounts.leadId')
            ->get();

        $result = [];

        foreach ($rows as $row) {
            $open = (float) $row->openTurnover;
            $closed = (float) $row->closedTurnover;

            $result[(int) $row->leadId] = new AccountStatisticsDto(
                openTurnover: $open,
                totalTurnover: $open + $closed,
            );
        }

        return $result;
    }

    /**
     * @param array<string, float> $deltas
     */
    private function increment(int $accountId, array $deltas) : bool
    {
        try {
            AccountStatisticsModel::query()
                ->where('accountId', $accountId)
                ->incrementEach($deltas);

            return true;
        } catch (\Throwable $throwable) {
            $this->logger->critical('unexpected error', [
                'accountId' => $accountId,
                'method'    => __METHOD__,
                'error'     => Arr::formatThrowable($throwable),
            ]);

            return false;
        }
    }
}
