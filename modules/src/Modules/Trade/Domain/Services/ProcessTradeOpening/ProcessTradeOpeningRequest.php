<?php

declare(strict_types=1);

namespace Platform\Trade\Domain\Services\ProcessTradeOpening;

use Platform\Trade\Domain\Dto\MetricDto;
use Platform\Trade\Domain\Dto\AccountDto;
use Platform\Trade\Domain\Entities\Symbol;
use Platform\Trade\Domain\Dto\TransactionDto;
use Platform\Package\Components\Time\DateTime;
use Platform\Trade\Domain\ValueObjects\Direction;
use Platform\Package\Components\Http\Request\Context;

class ProcessTradeOpeningRequest
{
    public function __construct(
        public int            $id,
        public Symbol         $symbol,
        public float          $lots,
        public Direction      $direction,
        public AccountDto     $account,
        public float          $price,
        public DateTime       $time,
        public float          $stopLoss,
        public float          $takeProfit,
        public float          $swap,
        public float          $commission,
        public TransactionDto $transaction,
        public MetricDto      $metric,
        public Context        $context,
    ) {
    }
}
