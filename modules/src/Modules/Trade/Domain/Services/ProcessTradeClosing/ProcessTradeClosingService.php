<?php

declare(strict_types=1);

namespace Platform\Trade\Domain\Services\ProcessTradeClosing;

use Platform\Package\Components\Collection\Arr;
use Platform\Trade\Domain\Entities\ClosedTrade;
use Platform\Trade\Domain\Adapters\CurrencyAdapter;
use Platform\Package\Components\Log\Contracts\Logger;
use Platform\Trade\Domain\Dto\TransactionResponseDto;
use Platform\Trade\Domain\Adapters\TransactionAdapter;
use Platform\Trade\Domain\Repositories\TradeRepository;
use Platform\Trade\Domain\Repositories\MetricRepository;
use Platform\Package\Components\Events\Contracts\Dispatcher;
use Platform\Trade\Domain\Adapters\AccountStatisticsAdapter;
use Platform\Trade\Domain\Repositories\ClosedTradeRepository;
use Platform\Package\Components\Database\Contracts\Transaction;
use Platform\Trade\Domain\Exceptions\AlreadyProcessedException;

class ProcessTradeClosingService implements ProcessTradeClosingInput
{
    public function __construct(
        private ProcessTradeClosingOutput $output,
        private Logger                    $logger,
        private Dispatcher                $dispatcher,
        private Transaction               $transaction,
        private TradeRepository           $tradeRepository,
        private CurrencyAdapter           $currencyAdapter,
        private MetricRepository          $metricRepository,
        private TransactionAdapter        $transactionAdapter,
        private ClosedTradeRepository     $closedTradeRepository,
        private AccountStatisticsAdapter  $accountStatisticsAdapter,
    ) {
    }

    public function process(ProcessTradeClosingRequest $request) : ProcessTradeClosingResult
    {
        $this->logger->withContext([
            'module'       => 'trade',
            'service'      => class_basename(self::class),
            'tradeId'      => $request->trade->id,
            'mtDealId'     => $request->transaction->mt5TransactionId,
            'accountId'    => $request->account->id,
            'accountLogin' => $request->account->login,
            'leadId'       => $request->account->leadId,
        ]);

        try {
            [$closedTrade, $transactionResponse] = $this->transaction->forMain(function () use ($request) : array {
                $closedTrade = $this->closedTradeRepository->create($request->closedTrade);

                $request->trade->close($closedTrade);

                $request->trade->swapSystem = $this->currencyAdapter->converseToSystem(
                    $request->trade->swap,
                    $request->account->currencyId,
                    $request->account->groupId,
                );

                $this->tradeRepository->save($request->trade);

                $request->transaction->entityId = $closedTrade->id;

                $transactionResponse = $this->makeCloseTransaction($request, $closedTrade);

                if ( ! $transactionResponse->success) {
                    if ($transactionResponse->isDuplicate) {
                        throw new AlreadyProcessedException();
                    }

                    throw new \Exception('Can not make close trade transaction');
                }

                $this->chargeCommission($request, $closedTrade->id);

                $this->accountStatisticsAdapter->moveOpenToClosedTurnover(
                    $request->account->id,
                    $closedTrade->turnover,
                    $closedTrade->turnoverSystem,
                );

                $request->metric->processedOnServer();

                $this->metricRepository->saveClosing(
                    $request->trade->id,
                    $request->closedTrade->mt5TransactionId,
                    $request->metric,
                );

                return [$closedTrade, $transactionResponse];
            });

            $this->dispatcher->dispatch($this->eventName($request->trade->isClosed), [$request->trade->toArray(
                array_merge(
                    [
                        'closedTrade'   => $closedTrade->toArray(),
                        'context'       => $request->context->toArray(),
                        'transactionId' => $transactionResponse->id,
                        'accountLogin'  => $request->account->login,
                    ],
                    $this->tradeRepository->getTimestamps($request->trade->id),
                ),
            )]);

            return $this->output->processed($closedTrade);
        } catch (AlreadyProcessedException) {
            return $this->output->alreadyProcessed();
        } catch (\Throwable $throwable) {
            $this->logger->critical('unexpected error', ['error' => Arr::formatThrowable($throwable)]);

            return $this->output->cannotProcess();
        }
    }

    private function eventName(bool $isClosed) : string
    {
        return $isClosed ? 'trade.closed' : 'trade.partial.closed';
    }

    private function chargeCommission(ProcessTradeClosingRequest $request, int $closeId) : void
    {
        if ($request->closedTrade->commission === 0.0) {
            return;
        }

        $isCreated = $this->transactionAdapter->closeTradeCommission(
            accountId: $request->account->id,
            tradeId: $request->trade->id,
            closeId: $closeId,
            leadId: $request->account->leadId,
            currencyId: $request->account->currencyId,
            groupId: $request->account->groupId,
            amount: $request->closedTrade->commission,
            createdAt: $request->transaction->mt5TransactionTime,
        );

        if ( ! $isCreated) {
            throw new \Exception('Can not make commission transaction');
        }
    }

    private function makeCloseTransaction(ProcessTradeClosingRequest $request, ClosedTrade $closedTrade) : TransactionResponseDto
    {
        return $this->transactionAdapter->closeTrade(
            $request->trade,
            $closedTrade,
            $request->transaction,
            $request->symbol,
        );
    }
}
