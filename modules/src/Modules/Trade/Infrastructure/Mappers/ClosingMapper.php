<?php

declare(strict_types=1);

namespace Platform\Trade\Infrastructure\Mappers;

use Platform\Trade\Domain\Entities\ClosedTrade;
use Platform\Trade\Infrastructure\Models\ClosingModel;

class ClosingMapper
{
    public static function entityFromModel(ClosingModel $model) : ClosedTrade
    {
        return new ClosedTrade(
            $model->id,
            $model->tradeId,
            $model->mt5TransactionId,
            $model->lots,
            $model->amount,
            $model->closePrice,
            $model->closeReason,
            $model->closeTime,
            $model->pnl,
            $model->pnlSystem,
            $model->swap,
            $model->swapSystem,
            $model->commission,
            (float) $model->turnover,
            (float) $model->turnoverSystem,
        );
    }
}
