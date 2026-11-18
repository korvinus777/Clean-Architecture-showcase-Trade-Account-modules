<?php

declare(strict_types=1);

namespace Platform\Trade\Infrastructure\Mappers;

use Platform\Trade\Domain\Entities\PendingOrder;
use Platform\Trade\Infrastructure\Models\PendingOrderModel;

class PendingOrderMapper
{
    public static function entityFromModel(PendingOrderModel $model) : PendingOrder
    {
        return new PendingOrder(
            $model->id,
            $model->accountId,
            $model->symbolId,
            $model->lots,
            $model->price,
            $model->stopLimitPrice,
            $model->stopLoss,
            $model->takeProfit,
            $model->type,
            $model->status,
            $model->openTime,
            $model->closeTime,
        );
    }
}
