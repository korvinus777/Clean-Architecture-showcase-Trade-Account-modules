<?php

declare(strict_types=1);

namespace Platform\Trade\App\Outputs\CrmReopenTrade\Results;

use Platform\Package\Components\Http\Response\Concerns\HttpJsonResult;
use Platform\Trade\Domain\Services\CrmReopenTrade\CrmReopenTradeResult;

class CrmReopenTradeJsonResult extends CrmReopenTradeResult
{
    use HttpJsonResult;

    public function getData() : array
    {
        return [
            'tradeId' => $this->tradeId,
        ];
    }
}
