<?php

declare(strict_types=1);

namespace Platform\Trade\Domain\Repositories;

use Platform\Trade\Domain\Dto\ClosedTradeDto;
use Platform\Trade\Domain\Entities\ClosedTrade;

interface ClosedTradeRepository
{
    public function create(ClosedTradeDto $closedTrade) : ClosedTrade;

    public function delete(int $closeId) : void;

    public function findByTradeAndMtDeal(int $tradeId, int $mt5TransactionId) : ?ClosedTrade;

    public function findByMtDeal(int $mt5TransactionId) : ?ClosedTrade;

    public function findByTrade(int $tradeId) : ?ClosedTrade;
}
