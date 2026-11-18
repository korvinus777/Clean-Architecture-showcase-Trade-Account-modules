<?php

declare(strict_types=1);

namespace Platform\Trade\Domain\Services\ProcessTradeClosing;

use Platform\Trade\Domain\Dto\MetricDto;
use Platform\Trade\Domain\Dto\AccountDto;
use Platform\Trade\Domain\Entities\Trade;
use Platform\Trade\Domain\Entities\Symbol;
use Platform\Trade\Domain\Dto\ClosedTradeDto;
use Platform\Trade\Domain\Dto\TransactionDto;
use Platform\Package\Components\Http\Request\Context;

class ProcessTradeClosingRequest
{
    public function __construct(
        public Trade          $trade,
        public AccountDto     $account,
        public ClosedTradeDto $closedTrade,
        public TransactionDto $transaction,
        public MetricDto      $metric,
        public Symbol         $symbol,
        public Context        $context,
    ) {
    }
}
