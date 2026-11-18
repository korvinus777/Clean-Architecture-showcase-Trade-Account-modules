<?php

declare(strict_types=1);

namespace Platform\Trade\Infrastructure\Mappers;

use Platform\Trade\Domain\Entities\Trade;
use Platform\Package\Components\Time\DateTime;
use Platform\Trade\Infrastructure\Models\TradeModel;

class TradeMapper
{
    public static function entityFromModel(TradeModel $model) : Trade
    {
        return new Trade(
            $model->id,
            $model->symbolId,
            $model->contractSize,
            $model->accountId,
            $model->leadId,
            $model->direction,
            $model->lots,
            $model->lotsLeft,
            $model->amount,
            $model->amountLeft,
            $model->openPrice,
            $model->openTime->setFormat(DateTime::DB_MS),
            $model->stopLoss,
            $model->takeProfit,
            $model->swap,
            $model->swapSystem,
            $model->isClosed,
            (float) $model->turnover,
            (float) $model->turnoverSystem,
        );
    }
}
