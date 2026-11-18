<?php

declare(strict_types=1);

namespace Platform\Account\Domain\Dto;

class AccountStatisticsDto
{
    public function __construct(
        public float $openTurnover = 0,
        public float $openTurnoverSystem = 0,
        public float $closedTurnover = 0,
        public float $closedTurnoverSystem = 0,
        public float $totalTurnover = 0,
    ) {
    }
}
