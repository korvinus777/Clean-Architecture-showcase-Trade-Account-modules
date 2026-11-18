<?php

declare(strict_types=1);

namespace Platform\Trade\Domain\Services\ProcessTradeEditing;

use Platform\Trade\Domain\Dto\AccountDto;
use Platform\Trade\Domain\Entities\Trade;
use Platform\Package\Components\Time\DateTime;
use Platform\Trade\Domain\ValueObjects\Direction;
use Platform\Package\Components\Http\Request\Context;

class ProcessTradeEditingRequest
{
    public function __construct(
        public Trade      $trade,
        public AccountDto $account,
        public Context    $context,
        public ?int       $symbolId = null,
        public ?float     $contractSize = null,
        public ?Direction $direction = null,
        public ?float     $lots = null,
        public ?float     $amount = null,
        public ?float     $lotsLeft = null,
        public ?float     $amountLeft = null,
        public ?float     $openPrice = null,
        public ?DateTime  $openTime = null,
        public ?float     $swap = null,
        public ?float     $stopLoss = null,
        public ?float     $takeProfit = null,
        public ?int       $profitCurrencyId = null,
    ) {
    }
}
