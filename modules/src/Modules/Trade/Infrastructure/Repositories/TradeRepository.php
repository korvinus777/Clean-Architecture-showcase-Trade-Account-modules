<?php

declare(strict_types=1);

namespace Platform\Trade\Infrastructure\Repositories;

use Illuminate\Support\Collection;
use Platform\Trade\Domain\Entities\Trade;
use Platform\Package\Components\String\Str;
use Platform\Package\Components\Time\DateTime;
use Platform\Trade\Infrastructure\Models\TradeModel;
use Platform\Package\Components\Http\Request\Context;
use Platform\Trade\Infrastructure\Models\ChangeModel;
use Platform\Trade\Infrastructure\Mappers\TradeMapper;
use Platform\Trade\Domain\Repositories\TradeRepository as ITradeRepository;

class TradeRepository implements ITradeRepository
{
    public function getNewId() : int
    {
        TradeModel::query()->getConnection()->beginTransaction();

        /** @var TradeModel $trade */
        $trade = TradeModel::query()->create(TradeModel::defaultAttributes());

        TradeModel::query()->getConnection()->rollBack();

        return $trade->id;
    }

    public function find(int $tradeId) : ?Trade
    {
        /** @var TradeModel|null $model */
        $model = TradeModel::query()->find($tradeId);

        return $model ? TradeMapper::entityFromModel($model) : null;
    }

    public function save(Trade $trade) : void
    {
        TradeModel::query()->upsert(
            [
                'id'           => $trade->id,
                'accountId'    => $trade->accountId,
                'leadId'       => $trade->leadId,
                'symbolId'     => $trade->symbolId,
                'contractSize' => Str::formatContractSize($trade->contractSize),
                'lots'         => Str::formatLots($trade->lots),
                'lotsLeft'     => Str::formatLots($trade->lotsLeft),
                'amount'       => Str::formatAmount($trade->amount),
                'amountLeft'   => Str::formatAmount($trade->amountLeft),
                'direction'    => $trade->direction->value,
                'openPrice'    => Str::formatPrice($trade->openPrice),
                'openTime'     => $trade->openTime->formatDbMs(),
                'stopLoss'     => Str::formatPrice($trade->stopLoss),
                'takeProfit'   => Str::formatPrice($trade->takeProfit),
                'swap'           => Str::formatMoney($trade->swap),
                'swapSystem'     => Str::formatMoney($trade->swapSystem),
                'turnover'       => Str::formatMoney($trade->turnover),
                'turnoverSystem' => Str::formatMoney($trade->turnoverSystem),
                'isClosed'       => $trade->isClosed,
            ],
            ['id'],
        );
    }

    public function update(Trade $trade) : void
    {
        // Оновлюємо тільки редагувані поля, isClosed не торкаємося
        TradeModel::query()->where('id', $trade->id)->update([
            'symbolId'     => $trade->symbolId,
            'contractSize' => Str::formatContractSize($trade->contractSize),
            'direction'    => $trade->direction->value,
            'lots'         => Str::formatLots($trade->lots),
            'lotsLeft'     => Str::formatLots($trade->lotsLeft),
            'amount'       => Str::formatAmount($trade->amount),
            'amountLeft'   => Str::formatAmount($trade->amountLeft),
            'openPrice'    => Str::formatPrice($trade->openPrice),
            'openTime'     => $trade->openTime->formatDbMs(),
            'stopLoss'     => Str::formatPrice($trade->stopLoss),
            'takeProfit'   => Str::formatPrice($trade->takeProfit),
            'swap'           => Str::formatMoney($trade->swap),
            'swapSystem'     => Str::formatMoney($trade->swapSystem),
            'turnover'       => Str::formatMoney($trade->turnover),
            'turnoverSystem' => Str::formatMoney($trade->turnoverSystem),
            // isClosed навмисно не включаємо - в нас php лайно з проблемами, тому ми не можемо довіряти isClosed(поки самий простий варінант).
        ]);
    }

    public function delete(int $tradeId) : void
    {
        TradeModel::query()->where('id', $tradeId)->delete();
    }

    public function saveChanges(int $tradeId, array $diffs, Context $context) : int
    {
        /** @var ChangeModel $change */
        $change = ChangeModel::query()->create([
            'tradeId'   => $tradeId,
            'diffs'     => $diffs,
            'context'   => $context,
            'createdAt' => DateTime::now(),
        ]);

        return $change->id;
    }

    public function get(int $tradeId) : Trade
    {
        /** @var TradeModel $model */
        $model = TradeModel::query()->findOrFail($tradeId);

        return TradeMapper::entityFromModel($model);
    }

    public function getTimestamps(int $tradeId) : array
    {
        /** @var TradeModel|null $model */
        $model = TradeModel::query()->find($tradeId);

        if ( ! $model) {
            return ['createdAt' => 0, 'updatedAt' => 0];
        }

        return [
            'createdAt' => (int) $model->createdAt->getTimestampMs(),
            'updatedAt' => (int) $model->updatedAt->getTimestampMs(),
        ];
    }

    /**
     * @inheritDoc
     */
    public function getOpenedOfAccount(int $accountId) : array
    {
        /** @var Collection<array-key, TradeModel> $trades */
        $trades = TradeModel::query()
            ->where('accountId', $accountId)
            ->where('isClosed', false)
            ->get();

        return array_map([TradeMapper::class, 'entityFromModel'], $trades->all());
    }

    /**
     * @inheritDoc
     */
    public function getOpened(int $page, int $limit) : array
    {
        /** @var Collection<array-key, TradeModel> $trades */
        $trades = TradeModel::query()
            ->where('isClosed', false)
            ->forPage($page, $limit)
            ->get();

        return array_map([TradeMapper::class, 'entityFromModel'], $trades->all());
    }
}
