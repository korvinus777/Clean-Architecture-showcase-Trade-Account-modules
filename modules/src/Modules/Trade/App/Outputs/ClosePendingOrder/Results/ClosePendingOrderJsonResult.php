<?php

declare(strict_types=1);

namespace Platform\Trade\App\Outputs\ClosePendingOrder\Results;

use Platform\Package\Components\Http\Response\Concerns\HttpJsonResult;
use Platform\Trade\Domain\Services\ClosePendingOrder\ClosePendingOrderResult;

class ClosePendingOrderJsonResult extends ClosePendingOrderResult
{
    use HttpJsonResult;

    public function getData() : array
    {
        if ($this->code->isOk()) {
            return [
                'pendingOrderId' => $this->pendingOrderId,
            ];
        }

        return [];
    }
}
