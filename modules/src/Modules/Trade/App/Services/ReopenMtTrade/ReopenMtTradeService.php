<?php

declare(strict_types=1);

namespace Platform\Trade\App\Services\ReopenMtTrade;

use Platform\Package\Components\Http\Request\Device;
use Platform\Package\Components\Http\Request\Context;
use Platform\Package\Components\Log\Contracts\Logger;
use Platform\Trade\Domain\Repositories\ClosedTradeRepository;
use Platform\Trade\Domain\Services\ReopenTrade\ReopenTradeInput;
use Platform\Trade\Domain\Services\ReopenTrade\ReopenTradeRequest;

class ReopenMtTradeService implements ReopenMtTradeInput
{
    public function __construct(
        private Logger                $logger,
        private ReopenTradeInput      $reopenTradeService,
        private ClosedTradeRepository $closedTradeRepository,
    ) {
    }

    public function reopen(ReopenMtTradeRequest $request) : bool
    {
        $this->logger->withContext([
            'module'           => 'trade',
            'service'          => class_basename(self::class),
            'mt5TransactionId' => $request->mt5TransactionId,
        ]);

        $closedTrade = $this->closedTradeRepository->findByMtDeal($request->mt5TransactionId);

        if ( ! $closedTrade) {
            $this->logger->critical('closed trade not found', ['reason' => 'finding by mt deal id']);

            return false;
        }

        $result = $this->reopenTradeService->reopen(new ReopenTradeRequest(
            $closedTrade,
            new Context(Device::MT_DESKTOP, '', '', ''),
        ));

        return $result->reopened;
    }
}
