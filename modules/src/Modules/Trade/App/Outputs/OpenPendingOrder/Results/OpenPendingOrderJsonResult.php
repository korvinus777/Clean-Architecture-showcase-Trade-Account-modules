<?php

declare(strict_types=1);

namespace Platform\Trade\App\Outputs\OpenPendingOrder\Results;

use Platform\Package\Components\Http\Response\Concerns\HttpJsonResult;
use Platform\Trade\Domain\Services\OpenPendingOrder\OpenPendingOrderResult;

class OpenPendingOrderJsonResult extends OpenPendingOrderResult
{
    use HttpJsonResult;

    public function getData() : array
    {
        if ($this->code->isOk()) {
            return [
                'orderId' => $this->orderId,
                'price'   => $this->price,
            ];
        }

        return [];
    }
}
