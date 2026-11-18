<?php

declare(strict_types=1);

namespace Platform\Trade\App\Outputs\ActivateMtOrder;

use Platform\Trade\Domain\Entities\Trade;
use Platform\Trade\App\Services\ActivateMtOrder\ActivateMtOrderResult;
use Platform\Trade\App\Services\ActivateMtOrder\ActivateMtOrderOutput as IActivateMtOrderOutput;

class ActivateMtOrderOutput implements IActivateMtOrderOutput
{
    public function activated(Trade $trade) : ActivateMtOrderResult
    {
        return new ActivateMtOrderResult($trade);
    }

    public function accountNotFound() : ActivateMtOrderResult
    {
        return new ActivateMtOrderResult();
    }

    public function symbolNotFound() : ActivateMtOrderResult
    {
        return new ActivateMtOrderResult();
    }

    public function pendingOrderNotFound() : ActivateMtOrderResult
    {
        return new ActivateMtOrderResult();
    }

    public function notActivated() : ActivateMtOrderResult
    {
        return new ActivateMtOrderResult();
    }

    public function orderIsNotPending() : ActivateMtOrderResult
    {
        return new ActivateMtOrderResult();
    }

    public function alreadyProcessed() : ActivateMtOrderResult
    {
        return new ActivateMtOrderResult(alreadyProcessed: true);
    }
}
