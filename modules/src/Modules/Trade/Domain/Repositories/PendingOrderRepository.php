<?php

declare(strict_types=1);

namespace Platform\Trade\Domain\Repositories;

use Platform\Package\Components\Time\DateTime;
use Platform\Trade\Domain\Entities\PendingOrder;

interface PendingOrderRepository
{
    public function getNewId() : int;

    public function find(int $platformOrderId) : ?PendingOrder;

    public function save(PendingOrder $order) : void;

    public function makeActivated(int $orderId, int $tradeId, DateTime $activateTime) : void;

    public function saveChanges(int $orderId, array $diffs, bool $isFromMt) : int;

    /**
     * @param int $accountId
     * @return PendingOrder[]
     */
    public function getPendingOfAccount(int $accountId) : array;
}
