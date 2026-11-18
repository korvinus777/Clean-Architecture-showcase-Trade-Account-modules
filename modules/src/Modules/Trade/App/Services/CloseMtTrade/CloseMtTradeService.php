<?php

declare(strict_types=1);

namespace Platform\Trade\App\Services\CloseMtTrade;

use Platform\Trade\Domain\Dto\ClosedTradeDto;
use Platform\Trade\Domain\Dto\TransactionDto;
use Platform\Trade\Domain\Adapters\SymbolAdapter;
use Platform\Trade\Domain\Adapters\AccountAdapter;
use Platform\Trade\Domain\Adapters\CurrencyAdapter;
use Platform\Package\Components\Http\Request\Device;
use Platform\Package\Components\Http\Request\Context;
use Platform\Package\Components\Log\Contracts\Logger;
use Platform\Trade\Domain\Repositories\TradeRepository;
use Platform\Trade\Domain\Repositories\MetricRepository;
use Platform\Trade\Domain\Repositories\ClosedTradeRepository;
use Platform\Trade\Domain\Services\ProcessTradeClosing\ProcessTradeClosingInput;
use Platform\Trade\Domain\Services\ProcessTradeClosing\ProcessTradeClosingRequest;

class CloseMtTradeService implements CloseMtTradeInput
{
    public function __construct(
        private CloseMtTradeOutput       $output,
        private Logger                   $logger,
        private SymbolAdapter            $symbolAdapter,
        private AccountAdapter           $accountAdapter,
        private CurrencyAdapter          $currencyAdapter,
        private TradeRepository          $tradeRepository,
        private MetricRepository         $metricRepository,
        private ClosedTradeRepository    $closedTradeRepository,
        private ProcessTradeClosingInput $processTradeClosingService,
    ) {
    }

    public function close(CloseMtTradeRequest $request) : CloseMtTradeResult
    {
        $this->logger->withContext([
            'module'           => 'trade',
            'service'          => class_basename(self::class),
            'tradeId'          => $request->tradeId,
            'mt5TransactionId' => $request->mt5TransactionId,
            'mtPositionId'     => $request->mtPositionId,
        ]);

        $metric = $this->metricRepository->getClosing($request->tradeId, $request->mt5TransactionId);

        $metric->acceptingEvent();

        $metric->processedOnMt($request->time);

        if ($closedTrade = $this->closedTradeRepository->findByTradeAndMtDeal($request->tradeId, $request->mt5TransactionId)) {
            $this->logger->info('trade already closed');

            return $this->output->closed($closedTrade);
        }

        $trade = $this->tradeRepository->find($request->tradeId);

        if ( ! $trade) {
            return $this->output->tradeNotFound();
        }

        if ($trade->lotsLeft < $request->lots) {
            $this->logger->critical('left less lots than need to close');
        }

        $account = $this->accountAdapter->findByLogin($request->login);

        if ( ! $account) {
            return $this->output->accountNotFound();
        }

        $symbol = $this->symbolAdapter->getSymbol($trade->symbolId);

        $context = new Context(Device::MT_DESKTOP, '', '', '');

        $result = $this->processTradeClosingService->process(new ProcessTradeClosingRequest(
            $trade,
            $account,
            new ClosedTradeDto(
                $trade->id,
                $request->mt5TransactionId,
                $request->price,
                $request->time,
                $request->lots,
                $symbol->amountFromLots($request->lots),
                $request->swap,
                $this->currencyAdapter->converseToSystem($request->swap, $account->currencyId, $account->groupId),
                $request->commission,
                $request->pnl,
                $this->currencyAdapter->converseToSystem($request->pnl, $account->currencyId, $account->groupId),
                $request->reason,
                $trade->turnover,
                $trade->turnoverSystem,
            ),
            new TransactionDto(
                entityId: $trade->id,
                accountId: $account->id,
                leadId: $account->leadId,
                amount: $request->pnl + $request->swap,
                currencyId: $account->currencyId,
                groupId: $account->groupId,
                mt5TransactionId: $request->mt5TransactionId,
                mt5TransactionTime: $request->time,
                mtPositionId: $request->mtPositionId,
            ),
            $metric,
            $symbol,
            $context,
        ));

        if ($result->alreadyProcessed) {
            return $this->output->alreadyProcessed();
        }

        return $result->closedTrade ? $this->output->closed($result->closedTrade) : $this->output->cannotProcessClosing();
    }
}
