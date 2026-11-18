<?php

declare(strict_types=1);

namespace Platform\Trade\Infrastructure\Repositories;

use Platform\Package\Components\String\Str;
use Platform\Trade\Domain\Dto\ClosedTradeDto;
use Platform\Trade\Domain\Entities\ClosedTrade;
use Platform\Trade\Infrastructure\Models\ClosingModel;
use Platform\Trade\Infrastructure\Mappers\ClosingMapper;
use Platform\Trade\Domain\Repositories\ClosedTradeRepository as IClosedTradeRepository;

class ClosedTradeRepository implements IClosedTradeRepository
{
    public function create(ClosedTradeDto $closedTrade) : ClosedTrade
    {
        /** @var ClosingModel $closing */
        $closing = ClosingModel::query()->create([
            'tradeId'          => $closedTrade->tradeId,
            'mt5TransactionId' => $closedTrade->mt5TransactionId,
            'lots'             => Str::formatLots($closedTrade->lots),
            'amount'           => Str::formatAmount($closedTrade->amount),
            'closePrice'       => Str::formatPrice($closedTrade->price),
            'closeTime'        => $closedTrade->time->formatDbMs(),
            'swap'             => Str::formatMoney($closedTrade->swap),
            'swapSystem'       => Str::formatMoney($closedTrade->swapSystem),
            'commission'       => Str::formatMoney($closedTrade->commission),
            'pnl'              => Str::formatMoney($closedTrade->pnl),
            'pnlSystem'        => Str::formatMoney($closedTrade->pnlSystem),
            'turnover'         => Str::formatMoney($closedTrade->turnover),
            'turnoverSystem'   => Str::formatMoney($closedTrade->turnoverSystem),
            'closeReason'      => $closedTrade->reason,
        ]);

        return ClosingMapper::entityFromModel($closing);
    }

    public function delete(int $closeId) : void
    {
        ClosingModel::query()->whereKey($closeId)->delete();
    }

    public function findByTradeAndMtDeal(int $tradeId, int $mt5TransactionId) : ?ClosedTrade
    {
        /** @var ClosingModel|null $closing */
        $closing = ClosingModel::query()->where(['tradeId' => $tradeId, 'mt5TransactionId' => $mt5TransactionId])->first();

        return $closing ? ClosingMapper::entityFromModel($closing) : null;
    }

    public function findByMtDeal(int $mt5TransactionId) : ?ClosedTrade
    {
        /** @var ClosingModel|null $closing */
        $closing = ClosingModel::query()->where('mt5TransactionId', $mt5TransactionId)->first();

        return $closing ? ClosingMapper::entityFromModel($closing) : null;
    }

    public function findByTrade(int $tradeId) : ?ClosedTrade
    {
        /** @var ClosingModel|null $closing */
        $closing = ClosingModel::query()->where(['tradeId' => $tradeId])->first();

        return $closing ? ClosingMapper::entityFromModel($closing) : null;
    }
}
