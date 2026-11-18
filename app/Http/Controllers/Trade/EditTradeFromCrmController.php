<?php

declare(strict_types=1);

namespace App\Http\Controllers\Trade;

use App\Http\Requests\Trade\EditTradeFromCrmRequest;
use Platform\Trade\App\Services\EditTrade\EditTradeInput;
use Platform\Package\Components\Http\Response\JsonResponse;
use Platform\Trade\App\Outputs\EditTrade\Results\EditTradeJsonResult;

class EditTradeFromCrmController
{
    /**
     * @param EditTradeInput $editTradeService
     */
    public function __construct(protected EditTradeInput $editTradeService)
    {
    }

    /**
     * @param EditTradeFromCrmRequest $request
     * @return JsonResponse
     */
    public function __invoke(EditTradeFromCrmRequest $request) : JsonResponse
    {
        /** @var EditTradeJsonResult $result */
        $result = $this->editTradeService->edit($request->domainRequest());

        return $result->response();
    }
}
