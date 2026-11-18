<?php

declare(strict_types=1);

namespace Platform\Trade\App\Outputs\OpenTrade\Results;

use Platform\Trade\Domain\Services\OpenTrade\OpenTradeResult;
use Platform\Package\Components\Http\Response\Concerns\HttpJsonResult;

class OpenTradeJsonResult extends OpenTradeResult
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
