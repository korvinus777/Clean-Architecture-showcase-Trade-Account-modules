<?php

declare(strict_types=1);

namespace Platform\Trade\Domain\Services\ProcessTradeOpening;

use Platform\Trade\Domain\Entities\Trade;
use Platform\Package\Components\Collection\Arr;
use Platform\Trade\Domain\Adapters\CurrencyAdapter;
use Platform\Package\Components\Log\Contracts\Logger;
use Platform\Trade\Domain\Adapters\TransactionAdapter;
use Platform\Trade\Domain\Services\TurnoverCalculator;
use Platform\Trade\Domain\Repositories\TradeRepository;
use Platform\Trade\Domain\Repositories\MetricRepository;
use Platform\Package\Components\Events\Contracts\Dispatcher;
use Platform\Trade\Domain\Adapters\AccountStatisticsAdapter;
use Platform\Package\Components\Database\Contracts\Transaction;
use Platform\Trade\Domain\Exceptions\AlreadyProcessedException;

class ProcessTradeOpeningService implements ProcessTradeOpeningInput
{
    public function __construct(
        private Logger                    $logger,
        private ProcessTradeOpeningOutput $output,
        private Dispatcher                $dispatcher,
        private Transaction               $transaction,
        private TradeRepository           $tradeRepository,
        private CurrencyAdapter           $currencyAdapter,
        private TurnoverCalculator        $turnoverCalculator,
        private MetricRepository          $metricRepository,
        private TransactionAdapter        $transactionAdapter,
        private AccountStatisticsAdapter  $accountStatisticsAdapter,
    ) {
    }

    public function process(ProcessTradeOpeningRequest $request) : ProcessTradeOpeningResult
    {
        $this->logger->withContext([
            'module'       => 'trade',
            'service'      => class_basename(self::class),
            'tradeId'      => $request->id,
            'mtDealId'     => $request->transaction->mt5TransactionId,
            'accountId'    => $request->account->id,
            'accountLogin' => $request->account->login,
            'leadId'       => $request->account->leadId,
        ]);

        try {
            [$trade, $transactionResponse] = $this->transaction->forMain(function () use ($request) {
                $trade = $this->makeEntity($request);

                $this->tradeRepository->save($trade);

                $transactionResponse = $this->transactionAdapter->openTrade($request->transaction);

                if ( ! $transactionResponse->success) {
                    if ($transactionResponse->isDuplicate) {
                        throw new AlreadyProcessedException();
                    }

                    throw new \Exception('Can not make open trade transaction');
                }

                $this->chargeCommission($request);

                $this->accountStatisticsAdapter->addOpenTurnover(
                    $request->account->id,
                    $trade->turnover,
                    $trade->turnoverSystem,
                );

                $request->metric->processedOnServer();

                $this->metricRepository->saveOpening($trade->id, $request->metric);

                return [$trade, $transactionResponse];
            });

            $this->dispatcher->dispatch('trade.opened', [$trade->toArray(
                array_merge(
                    [
                        'context'       => $request->context->toArray(),
                        'transactionId' => $transactionResponse->id,
                        'accountLogin'  => $request->account->login,
                    ],
                    $this->tradeRepository->getTimestamps($trade->id),
                ),
            )]);

            return $this->output->processed($trade);
        } catch (AlreadyProcessedException) {
            return $this->output->alreadyProcessed();
        } catch (\Throwable $throwable) {
            $this->logger->critical('unexpected error', ['error' => Arr::formatThrowable($throwable)]);

            return $this->output->canNotProcess();
        }
    }

    private function chargeCommission(ProcessTradeOpeningRequest $request) : void
    {
        if ($request->commission === 0.0) {
            return;
        }

        $isCreated = $this->transactionAdapter->openTradeCommission(
            accountId: $request->account->id,
            tradeId: $request->id,
            leadId: $request->account->leadId,
            currencyId: $request->account->currencyId,
            groupId: $request->account->groupId,
            amount: $request->commission,
            createdAt: $request->transaction->mt5TransactionTime,
        );

        if ( ! $isCreated) {
            throw new \Exception('Can not make commission transaction');
        }
    }

    private function makeEntity(ProcessTradeOpeningRequest $request) : Trade
    {
        $amount = $request->symbol->amountFromLots($request->lots);

        $turnover = $this->turnoverCalculator->calculate(
            $request->lots,
            $request->price,
            $request->symbol->contractSize,
            $request->symbol->profitCurrencyId,
            $request->account->currencyId,
            $request->account->groupId,
        );

        return new Trade(
            $request->id,
            $request->symbol->id,
            $request->symbol->contractSize,
            $request->account->id,
            $request->account->leadId,
            $request->direction,
            $request->lots,
            $request->lots,
            $amount,
            $amount,
            $request->price,
            $request->time,
            $request->stopLoss,
            $request->takeProfit,
            $request->swap,
            $this->currencyAdapter->converseToSystem(
                $request->swap,
                $request->account->currencyId,
                $request->account->groupId,
            ),
            false,
            $turnover->inAccountCurrency,
            $turnover->inSystemCurrency,
        );
    }
}
