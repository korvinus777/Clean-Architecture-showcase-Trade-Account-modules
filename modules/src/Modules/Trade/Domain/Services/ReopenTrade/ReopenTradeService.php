<?php

declare(strict_types=1);

namespace Platform\Trade\Domain\Services\ReopenTrade;

use Platform\Trade\Domain\Entities\Trade;
use Platform\Package\Components\Time\DateTime;
use Platform\Package\Components\Collection\Arr;
use Platform\Trade\Domain\Entities\ClosedTrade;
use Platform\Trade\Domain\Dto\TransactionInfoDto;
use Platform\Package\Components\Http\Request\Context;
use Platform\Package\Components\Log\Contracts\Logger;
use Platform\Trade\Domain\Adapters\TransactionAdapter;
use Platform\Trade\Domain\Repositories\TradeRepository;
use Platform\Trade\Domain\Repositories\MetricRepository;
use Platform\Package\Components\Events\Contracts\Dispatcher;
use Platform\Trade\Domain\Adapters\AccountStatisticsAdapter;
use Platform\Trade\Domain\Repositories\ClosedTradeRepository;
use Platform\Package\Components\Database\Contracts\Transaction;

class ReopenTradeService implements ReopenTradeInput
{
    public function __construct(
        private ReopenTradeOutput        $output,
        private Logger                   $logger,
        private Dispatcher               $dispatcher,
        private Transaction              $transaction,
        private TradeRepository          $tradeRepository,
        private MetricRepository         $metricRepository,
        private TransactionAdapter       $transactionAdapter,
        private ClosedTradeRepository    $closedTradeRepository,
        private AccountStatisticsAdapter $accountStatisticsAdapter,
    ) {
    }

    public function reopen(ReopenTradeRequest $request) : ReopenTradeResult
    {
        $this->logger->withContext([
            'module'           => 'trade',
            'service'          => class_basename(self::class),
            'closeId'          => $request->closedTrade->id,
            'tradeId'          => $request->closedTrade->tradeId,
            'mt5TransactionId' => $request->closedTrade->mt5TransactionId,
        ]);

        try {
            /**
             * @var Trade $trade
             * @var TransactionInfoDto $closeTransaction
             */
            [$trade, $closeTransaction] = $this->transaction->forMain(function () use ($request) : array {
                $trade = $this->tradeRepository->get($request->closedTrade->tradeId);

                if ($request->closedTrade->commission) {
                    $this->transactionAdapter->cancelCloseCommission(
                        $request->closedTrade->tradeId,
                        $request->closedTrade->id,
                    );
                }

                $closeTransaction = $this->cancelClosedTransaction($request->closedTrade);

                if ($closeTransaction === null) {
                    throw new \RuntimeException('Close transaction not found or could not be cancelled');
                }

                $this->reopenTrade($trade, $request->closedTrade, $request->context);

                $this->accountStatisticsAdapter->moveClosedToOpenTurnover(
                    $trade->accountId,
                    $request->closedTrade->turnover,
                    $request->closedTrade->turnoverSystem,
                );

                $this->metricRepository->deleteClosing(
                    $request->closedTrade->tradeId,
                    $request->closedTrade->mt5TransactionId,
                );

                $this->closedTradeRepository->delete($request->closedTrade->id);

                return [$trade, $closeTransaction];
            });

            $this->dispatcher->dispatch('trade.close.cancelled', [$request->closedTrade->toArray([
                'transaction' => $closeTransaction->toArray(),
                'accountId'   => $trade->accountId,
            ])]);

            $this->dispatcher->dispatch('trade.reopened', [$trade->toArray(
                array_merge(
                    [
                        'transactionId'    => $closeTransaction->id,
                        'closeId'          => $request->closedTrade->id,
                        'closedPnl'        => $request->closedTrade->pnl,
                        'closedPnlSystem'  => $request->closedTrade->pnlSystem,
                        'closedSwap'       => $request->closedTrade->swap,
                        'closedSwapSystem' => $request->closedTrade->swapSystem,
                        'updatedTimestamp' => DateTime::now()->getTimestamp(),
                        'context'          => $request->context,
                    ],
                    $this->tradeRepository->getTimestamps($trade->id),
                ),
            )]);

            return $this->output->reopened();
        } catch (\Throwable $throwable) {
            $this->logger->critical('unexpected error', ['error' => Arr::formatThrowable($throwable, true)]);

            return $this->output->unexpectedError();
        }
    }

    private function cancelClosedTransaction(ClosedTrade $closedTrade) : ?TransactionInfoDto
    {
        if ( ! $closedTransaction = $this->transactionAdapter->findClosedTrade($closedTrade->id)) {
            $this->logger->critical('close transaction not found');

            return null;
        }

        if ( ! $this->transactionAdapter->cancelClose($closedTrade->id)) {
            $this->logger->critical('close transaction not cancelled');

            return null;
        }

        return $closedTransaction;
    }

    private function reopenTrade(Trade $trade, ClosedTrade $closedTrade, Context $context) : void
    {
        $changer = $trade->cancelClose($closedTrade);

        $this->tradeRepository->save($trade);

        $this->tradeRepository->saveChanges($trade->id, $changer->getChanges(), $context);
    }
}
