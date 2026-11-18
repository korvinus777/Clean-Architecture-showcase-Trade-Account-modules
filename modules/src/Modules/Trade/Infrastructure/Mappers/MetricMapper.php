<?php

declare(strict_types=1);

namespace Platform\Trade\Infrastructure\Mappers;

use Platform\Trade\Domain\Dto\MetricDto;
use Platform\Trade\Infrastructure\Models\ClosingMetricModel;
use Platform\Trade\Infrastructure\Models\OpeningMetricModel;

class MetricMapper
{
    public static function makeOpeningDtoFromModel(OpeningMetricModel $model) : MetricDto
    {
        return new MetricDto(
            $model->requestedFromClient,
            $model->acceptedOnServer,
            $model->sentOnMt,
            $model->answeredFromMt,
            $model->openedOnMt,
            $model->acceptedEventOnServer,
            $model->openedOnServer,
        );
    }

    public static function makeClosingDtoFromModel(ClosingMetricModel $model) : MetricDto
    {
        return new MetricDto(
            $model->requestedFromClient,
            $model->acceptedOnServer,
            $model->sentOnMt,
            $model->answeredFromMt,
            $model->closedOnMt,
            $model->acceptedEventOnServer,
            $model->closedOnServer,
        );
    }
}
