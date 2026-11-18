<?php

declare(strict_types=1);

namespace App\Http\Controllers\Trade;

use Illuminate\Http\JsonResponse;
use Platform\Package\Components\Http\Request\Request;
use Platform\Trade\Domain\Services\CrmReopenTrade\CrmReopenTradeInput;
use Platform\Trade\Domain\Services\CrmReopenTrade\CrmReopenTradeRequest;
use Platform\Trade\App\Outputs\CrmReopenTrade\Results\CrmReopenTradeJsonResult;

class ReopenTradeController
{
    /**
     * @param CrmReopenTradeInput $crmReopenTradeInput
     */
    public function __construct(protected CrmReopenTradeInput $crmReopenTradeInput)
    {

    }

    /**
     * @return JsonResponse
     */
    public function __invoke() : JsonResponse
    {
        /** @var CrmReopenTradeJsonResult $result */
        $result = $this->crmReopenTradeInput->reopen(
            new CrmReopenTradeRequest(
                tradeId: Request::getInt('tradeId'),
                accountId: Request::getInt('accountId'),
            ),
        );

        return $result->response();
    }
}
