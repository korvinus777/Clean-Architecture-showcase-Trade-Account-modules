<?php

declare(strict_types=1);

namespace Platform\Trade\Domain\Repositories;

use Platform\Trade\Domain\Entities\Trade;
use Platform\Package\Components\Http\Request\Context;

interface TradeRepository
{
    public function getNewId() : int;

    public function find(int $tradeId) : ?Trade;

    public function save(Trade $trade) : void;

    /**
     * Оновлює тільки вказані поля trade (без isClosed)
     * @param Trade $trade Об'єкт з новими значеннями полів
     * @return void
     */
    public function update(Trade $trade) : void;

    public function delete(int $tradeId) : void;

    public function saveChanges(int $tradeId, array $diffs, Context $context) : int;

    public function get(int $tradeId) : Trade;

    public function getTimestamps(int $tradeId) : array;

    /**
     * @param int $accountId
     * @return Trade[]
     */
    public function getOpenedOfAccount(int $accountId) : array;

    /**
     * @param int $page
     * @param int $limit
     * @return Trade[]
     */
    public function getOpened(int $page, int $limit) : array;
}
