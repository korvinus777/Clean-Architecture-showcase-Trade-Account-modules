<?php

declare(strict_types=1);

namespace Platform\Account\Domain\Dto;

class AccountBalancesDto
{
    public function __construct(
        public int   $leadId,
        public int   $accountId,
        public int   $login,
        public float $balance,
        public float $balanceSystem,
        public float $credit,
        public float $creditSystem,
        public float $equity,
        public float $equitySystem,
        public float $openPnl,
        public float $openPnlSystem,
        public float $margin,
        public float $marginSystem,
        public float $freeMargin,
        public float $freeMarginSystem,
        public float $availableWithdrawal,
        public int   $marginLevel,
    ) {
    }
}
