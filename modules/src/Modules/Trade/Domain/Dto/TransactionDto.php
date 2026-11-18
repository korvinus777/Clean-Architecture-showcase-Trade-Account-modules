<?php

declare(strict_types=1);

namespace Platform\Trade\Domain\Dto;

use Platform\Package\Components\Time\DateTime;

class TransactionDto
{
    public function __construct(
        public int      $entityId,
        public int      $accountId,
        public int      $leadId,
        public float    $amount,
        public int      $currencyId,
        public int      $groupId,
        public int      $mt5TransactionId,
        public DateTime $mt5TransactionTime,
        public int      $mtPositionId = 0,
    ) {
    }
}
