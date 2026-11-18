<?php

declare(strict_types=1);

namespace Platform\Trade\Infrastructure\Repositories;

use Platform\Trade\Domain\Dto\MetricDto;
use Platform\Trade\Infrastructure\Mappers\MetricMapper;
use Platform\Trade\Infrastructure\Models\ClosingMetricModel;
use Platform\Trade\Infrastructure\Models\OpeningMetricModel;
use Platform\Trade\Infrastructure\Models\OpeningOrderMetricModel;
use Platform\Trade\Infrastructure\Models\ActivatingOrderMetricModel;
use Platform\Trade\Domain\Repositories\MetricRepository as IMetricRepository;

class MetricRepository implements IMetricRepository
{
    public function saveOpening(int $tradeId, MetricDto $metric) : void
    {
        OpeningMetricModel::query()->upsert(
            [
                'tradeId'               => $tradeId,
                'requestedFromClient'   => $metric->requestedFromClient,
                'acceptedOnServer'      => $metric->acceptedOnServer,
                'sentOnMt'              => $metric->sentOnMt,
                'answeredFromMt'        => $metric->answeredFromMt,
                'openedOnMt'            => $metric->processedOnMt,
                'acceptedEventOnServer' => $metric->acceptedEventOnServer,
                'openedOnServer'        => $metric->processedOnServer,
            ],
            ['tradeId'],
        );
    }

    public function getOpening(int $tradeId) : MetricDto
    {
        /** @var OpeningMetricModel $model */
        $model = OpeningMetricModel::query()->firstOrNew(['tradeId' => $tradeId]);

        return MetricMapper::makeOpeningDtoFromModel($model);
    }

    public function saveOpeningOrder(int $orderId, MetricDto $openingMetric) : void
    {
        OpeningOrderMetricModel::query()->upsert(
            [
                'orderId'               => $orderId,
                'requestedFromClient'   => $openingMetric->requestedFromClient,
                'acceptedOnServer'      => $openingMetric->acceptedOnServer,
                'sentOnMt'              => $openingMetric->sentOnMt,
                'answeredFromMt'        => $openingMetric->answeredFromMt,
                'openedOnMt'            => $openingMetric->processedOnMt,
                'acceptedEventOnServer' => $openingMetric->acceptedEventOnServer,
                'openedOnServer'        => $openingMetric->processedOnServer,
                'sentOnNt'              => $openingMetric->sentOnNt,
                'answeredFromNt'        => $openingMetric->answeredFromNt,
            ],
            ['orderId'],
        );
    }

    public function saveClosing(int $tradeId, int $mt5TransactionId, MetricDto $metric) : void
    {
        ClosingMetricModel::query()->upsert(
            [
                'tradeId'               => $tradeId,
                'mt5TransactionId'      => $mt5TransactionId,
                'requestedFromClient'   => $metric->requestedFromClient,
                'acceptedOnServer'      => $metric->acceptedOnServer,
                'sentOnMt'              => $metric->sentOnMt,
                'answeredFromMt'        => $metric->answeredFromMt,
                'closedOnMt'            => $metric->processedOnMt,
                'acceptedEventOnServer' => $metric->acceptedEventOnServer,
                'closedOnServer'        => $metric->processedOnServer,
            ],
            ['tradeId', 'mt5TransactionId'],
        );
    }

    public function getClosing(int $tradeId, int $mt5TransactionId) : MetricDto
    {
        /** @var ClosingMetricModel $model */
        $model = ClosingMetricModel::query()->firstOrNew(['tradeId' => $tradeId, 'mt5TransactionId' => $mt5TransactionId]);

        return MetricMapper::makeClosingDtoFromModel($model);
    }

    public function saveActivating(int $tradeId, int $orderId, MetricDto $activatingMetric) : void
    {
        ActivatingOrderMetricModel::query()->upsert(
            [
                'orderId'               => $orderId,
                'tradeId'               => $tradeId,
                'activatedOnMt'         => $activatingMetric->processedOnMt,
                'acceptedEventOnServer' => $activatingMetric->acceptedEventOnServer,
                'activatedOnServer'     => $activatingMetric->processedOnServer,
            ],
            ['tradeId', 'orderId'],
        );
    }

    public function deleteClosing(int $tradeId, int $mt5TransactionId) : void
    {
        ClosingMetricModel::query()
            ->where('tradeId', $tradeId)
            ->where('mt5TransactionId', $mt5TransactionId)
            ->delete();
    }

    public function deleteOpening(int $tradeId) : void
    {
        OpeningMetricModel::query()
            ->where('tradeId', $tradeId)
            ->delete();
    }
}
