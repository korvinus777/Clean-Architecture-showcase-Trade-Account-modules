<?php

declare(strict_types=1);

namespace Platform\Trade\App\Services\ActivateMtOrder;

use Platform\Trade\Domain\Entities\Trade;

interface ActivateMtOrderOutput
{
    public function activated(Trade $trade) : ActivateMtOrderResult;

    public function accountNotFound() : ActivateMtOrderResult;

    public function symbolNotFound() : ActivateMtOrderResult;

    public function pendingOrderNotFound() : ActivateMtOrderResult;

    public function notActivated() : ActivateMtOrderResult;

    public function orderIsNotPending() : ActivateMtOrderResult;

    public function alreadyProcessed() : ActivateMtOrderResult;
}
