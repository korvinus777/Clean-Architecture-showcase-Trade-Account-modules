<?php

declare(strict_types=1);

namespace Platform\Trade\Domain\Repositories;

use Platform\Trade\Domain\Dto\MetricDto;

interface MetricRepository
{
    public function saveOpening(int $tradeId, MetricDto $metric) : void;

    public function getOpening(int $tradeId) : MetricDto;

    public function saveClosing(int $tradeId, int $mt5TransactionId, MetricDto $metric) : void;

    public function saveOpeningOrder(int $orderId, MetricDto $openingMetric) : void;

    public function getClosing(int $tradeId, int $mt5TransactionId) : MetricDto;

    public function saveActivating(int $tradeId, int $orderId, MetricDto $activatingMetric) : void;

    public function deleteClosing(int $tradeId, int $mt5TransactionId) : void;

    public function deleteOpening(int $tradeId) : void;
}
