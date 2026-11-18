<?php

declare(strict_types=1);

namespace Platform\Trade\App\Outputs\CloseTrade\Results;

use Platform\Trade\Domain\Services\CloseTrade\CloseTradeResult;
use Platform\Package\Components\Http\Response\Concerns\HttpJsonResult;

class CloseTradeJsonResult extends CloseTradeResult
{
    use HttpJsonResult;

    public function getData() : array
    {
        if ($this->code->isOk()) {
            return [
                'tradeId' => $this->tradeId,
                'price'   => $this->price,
            ];
        }

        return [];
    }
}
