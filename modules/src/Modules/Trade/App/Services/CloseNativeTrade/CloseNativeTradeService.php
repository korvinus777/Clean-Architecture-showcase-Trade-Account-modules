<?php

declare(strict_types=1);

namespace Platform\Trade\App\Services\CloseNativeTrade;

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

class CloseNativeTradeService implements CloseNativeTradeInput
{
    public function __construct(
        private CloseNativeTradeOutput    $output,
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

    public function close(CloseNativeTradeRequest $request) : CloseNativeTradeResult
    {
        $this->logger->withContext([
            'module'                  => 'trade',
            'service'                 => class_basename(self::class),
            'tradeId'                 => $request->tradeId,
            'nativeTransactionId'     => $request->nativeTransactionId,
            'nativePositionId'        => $request->nativePositionId,
        ]);

        $metric = $this->metricRepository->getClosing($request->tradeId, $request->nativeTransactionId);

        $metric->acceptingEvent();

        if ($closedTrade = $this->closedTradeRepository->findByTrade($request->tradeId)) {
            $this->logger->info('trade already closed');

            return $this->output->closed($closedTrade);
        }

        $trade = $this->tradeRepository->find($request->tradeId);

        if ( ! $trade) {
            return $this->output->tradeNotFound();
        }


        $account = $this->accountAdapter->findByLogin($request->login);

        if ( ! $account) {
            return $this->output->accountNotFound();
        }

        $symbol = $this->symbolAdapter->getSymbol($trade->symbolId);

        $context = new Context(Device::WEB_DESKTOP, '', '', '');

        // Оновлюємо маржу на рахунок, якщо вона змінилася
        if ($request->margin !== null) {
            $this->accountAdapter->updateMargin($account->login, $request->margin, true);
        }

        // Convert to system currency
        $pnlSystem = $this->currencyAdapter->converseToSystem($request->pnl, $account->currencyId, $account->groupId);
        $swapSystem = $this->currencyAdapter->converseToSystem($request->swap, $account->currencyId, $account->groupId);

        $result = $this->processTradeClosingService->process(new ProcessTradeClosingRequest(
            $trade,
            $account,
            new ClosedTradeDto(
                $trade->id,
                $request->nativeTransactionId,
                $request->price,
                $request->time,
                $request->lots,
                $symbol->amountFromLots($request->lots),
                $request->swap,
                $swapSystem,
                $request->commission,
                $request->pnl,
                $pnlSystem,
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
                mt5TransactionId: $request->nativeTransactionId,
                mt5TransactionTime: $request->time,
                mtPositionId: $request->nativePositionId,
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
