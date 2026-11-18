<?php

declare(strict_types=1);

namespace Platform\Trade\App\Services\CloseAccountTrades;

use Platform\Trade\Domain\Repositories\TradeRepository;
use Platform\Trade\Domain\Services\CloseTrade\CloseTradeInput;
use Platform\Trade\Domain\Services\CloseTrade\CloseTradeRequest;

class CloseAccountTradesService implements CloseAccountTradesInput
{
    public function __construct(
        private CloseAccountTradesOutput $output,
        private TradeRepository          $tradeRepository,
        private CloseTradeInput          $closeTradeService,
    ) {
    }

    public function close(CloseAccountTradesRequest $request) : CloseAccountTradesResult
    {
        $trades = $this->tradeRepository->getOpenedOfAccount($request->accountId);

        $results = [];

        foreach ($trades as $trade) {
            $results[$trade->id] = $this->closeTradeService->close(new CloseTradeRequest(
                $trade->id,
                $request->accountId,
                $request->requestedFromClient,
                true,
            ));
        }

        return $this->output->done($results);
    }
}
