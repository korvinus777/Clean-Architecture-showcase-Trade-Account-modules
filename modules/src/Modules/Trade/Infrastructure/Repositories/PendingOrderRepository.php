<?php

declare(strict_types=1);

namespace Platform\Trade\Infrastructure\Repositories;

use Illuminate\Support\Collection;
use Platform\Package\Components\String\Str;
use Platform\Package\Components\Time\DateTime;
use Platform\Trade\Domain\Entities\PendingOrder;
use Platform\Trade\Domain\ValueObjects\PendingOrderStatus;
use Platform\Trade\Infrastructure\Models\PendingOrderModel;
use Platform\Trade\Infrastructure\Mappers\PendingOrderMapper;
use Platform\Trade\Infrastructure\Models\PendingOrderChangeModel;
use Platform\Trade\Infrastructure\Models\PendingOrderActivationModel;
use Platform\Trade\Domain\Repositories\PendingOrderRepository as IPendingOrderRepository;

class PendingOrderRepository implements IPendingOrderRepository
{
    public function getNewId() : int
    {
        PendingOrderModel::query()->getConnection()->beginTransaction();

        /** @var PendingOrderModel $order */
        $order = PendingOrderModel::query()->create(PendingOrderModel::defaultAttributes());

        PendingOrderModel::query()->getConnection()->rollBack();

        return $order->id;
    }

    public function find(int $platformOrderId) : ?PendingOrder
    {
        /** @var PendingOrderModel|null $pendingOrder */
        $pendingOrder = PendingOrderModel::query()->find($platformOrderId);

        return $pendingOrder ? PendingOrderMapper::entityFromModel($pendingOrder) : null;
    }

    public function save(PendingOrder $order) : void
    {
        PendingOrderModel::query()->upsert(
            [
                'id'             => $order->id,
                'accountId'      => $order->accountId,
                'symbolId'       => $order->symbolId,
                'lots'           => Str::formatLots($order->lots),
                'price'          => Str::formatPrice($order->price),
                'stopLimitPrice' => Str::formatPrice($order->stopLimitPrice),
                'stopLoss'       => Str::formatPrice($order->stopLoss),
                'takeProfit'     => Str::formatPrice($order->takeProfit),
                'type'           => $order->type,
                'status'         => $order->status,
                'openTime'       => $order->openTime->formatDbMs(),
                'closeTime'      => $order->closeTime ? (string) $order->closeTime : null,
            ],
            ['id'],
        );
    }

    public function makeActivated(int $orderId, int $tradeId, DateTime $activateTime) : void
    {
        PendingOrderModel::query()->whereKey($orderId)->update(['status' => PendingOrderStatus::ACTIVATED]);

        PendingOrderActivationModel::query()->create([
            'orderId'      => $orderId,
            'tradeId'      => $tradeId,
            'activateTime' => $activateTime->formatDbMs(),
        ]);
    }

    public function saveChanges(int $orderId, array $diffs, bool $isFromMt) : int
    {
        /** @var PendingOrderChangeModel $change */
        $change = PendingOrderChangeModel::query()->create([
            'orderId'   => $orderId,
            'diffs'     => $diffs,
            'isFromMt'  => $isFromMt,
            'createdAt' => DateTime::now(),
        ]);

        return $change->id;
    }

    /**
     * @inheritDoc
     */
    public function getPendingOfAccount(int $accountId) : array
    {
        /** @var Collection<array-key, PendingOrderModel> $orders */
        $orders = PendingOrderModel::query()
            ->where('accountId', $accountId)
            ->where('status', PendingOrderStatus::PENDING)
            ->get();

        return array_map([PendingOrderMapper::class, 'entityFromModel'], $orders->all());
    }
}
