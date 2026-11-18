<?php

declare(strict_types=1);

namespace Platform\Trade\Domain\Services\ProcessTradeEditing;

use Platform\Package\Components\Collection\Arr;
use Platform\Trade\Domain\Adapters\CurrencyAdapter;
use Platform\Package\Components\Log\Contracts\Logger;
use Platform\Trade\Domain\Services\TurnoverCalculator;
use Platform\Trade\Domain\Repositories\TradeRepository;
use Platform\Package\Components\Collection\ObjectChanger;
use Platform\Package\Components\Events\Contracts\Dispatcher;
use Platform\Trade\Domain\Adapters\AccountStatisticsAdapter;
use Platform\Package\Components\Database\Contracts\Transaction;

class ProcessTradeEditingService implements ProcessTradeEditingInput
{
    public function __construct(
        private ProcessTradeEditingOutput $output,
        private Logger                    $logger,
        private Dispatcher                $dispatcher,
        private Transaction               $transaction,
        private TradeRepository           $tradeRepository,
        private CurrencyAdapter           $currencyAdapter,
        private TurnoverCalculator        $turnoverCalculator,
        private AccountStatisticsAdapter  $accountStatisticsAdapter,
    ) {
    }

    public function process(ProcessTradeEditingRequest $request) : ProcessTradeEditingResult
    {
        $this->logger->withContext([
            'module'   => 'trade',
            'service'  => class_basename(self::class),
            'tradeId'  => $request->trade->id,
        ]);

        /**
         * Changes affects on next properties:
         * @uses Trade::$symbolId
         * @uses Trade::$contractSize
         * @uses Trade::$direction
         * @uses Trade::$lots
         * @uses Trade::$amount
         * @uses Trade::$lotsLeft
         * @uses Trade::$amountLeft
         * @uses Trade::$openPrice
         * @uses Trade::$openTime
         * @uses Trade::$stopLoss
         * @uses Trade::$takeProfit
         */
        $changer = (new ObjectChanger($request->trade))
            ->changeNotNull('symbolId', $request->symbolId)
            ->changeNotNull('contractSize', $request->contractSize)
            ->changeNotNull('direction', $request->direction)
            ->changeNotNull('lots', $request->lots)
            ->changeNotNull('amount', $request->amount)
            ->changeNotNull('lotsLeft', $request->lotsLeft)
            ->changeNotNull('amountLeft', $request->amountLeft)
            ->changeNotNull('openPrice', $request->openPrice)
            ->changeNotNull('openTime', $request->openTime)
            ->changeNotNull('stopLoss', $request->stopLoss)
            ->changeNotNull('takeProfit', $request->takeProfit);

        $this->changeSwap($request, $changer);
        $this->changeTurnover($request, $changer);

        if ( ! $changer->getChanges()) {
            return $this->output->processed($request->trade);
        }

        try {
            $changeId = $this->transaction->forMain(function () use ($request, $changer) : int {
                // Оновлюємо тільки редагувані поля, isClosed не торкаємося(race condition проблема)
                $this->tradeRepository->update($request->trade);

                $this->updateTurnoverStatistics($request, $changer);

                return $this->tradeRepository->saveChanges(
                    $request->trade->id,
                    $changer->getChanges(),
                    $request->context,
                );
            });

            $this->dispatcher->dispatch('trade.edited', [$request->trade->toArray(
                array_merge(
                    [
                        'context'      => $request->context->toArray(),
                        'changeId'     => $changeId,
                        'accountLogin' => $request->account->login,
                        'changes'      => $changer->getChanges(),
                    ],
                    $this->tradeRepository->getTimestamps($request->trade->id),
                ),
            )]);

            return $this->output->processed($request->trade);
        } catch (\Throwable $throwable) {
            $changer->rollback();

            $this->logger->critical('unexpected error', [
                'error'   => Arr::formatThrowable($throwable, true),
                'request' => (array) $request,
            ]);

            return $this->output->failed();
        }
    }

    private function changeSwap(ProcessTradeEditingRequest $request, ObjectChanger $changer) : void
    {
        /**
         * @uses Trade::$swap
         * @uses Trade::$swapSystem
         */
        $changer->changeNotNull('swap', $request->swap);

        if ( ! $changer->isChanged('swap')) {
            return;
        }

        $swapSystem = $this->currencyAdapter->converseToSystem(
            $request->trade->swap,
            $request->account->currencyId,
            $request->account->groupId,
        );

        $changer->change('swapSystem', $swapSystem);
    }

    /**
     * Recalculate turnover when lots, openPrice, or contractSize changes.
     */
    private function changeTurnover(ProcessTradeEditingRequest $request, ObjectChanger $changer) : void
    {
        if ( ! $request->profitCurrencyId) {
            return;
        }

        if ( ! $changer->isChanged('lots') && ! $changer->isChanged('openPrice')) {
            return;
        }

        $trade = $request->trade;

        $turnover = $this->turnoverCalculator->calculate(
            $trade->lotsLeft,
            $trade->openPrice,
            $trade->contractSize,
            $request->profitCurrencyId ?? 0,
            $request->account->currencyId,
            $request->account->groupId,
        );

        /**
         * @uses Trade::$turnover
         * @uses Trade::$turnoverSystem
         */
        $changer->changeNotNull('turnover', $turnover->inAccountCurrency !== $trade->turnover ? $turnover->inAccountCurrency : null);
        $changer->changeNotNull('turnoverSystem', $turnover->inSystemCurrency !== $trade->turnoverSystem ? $turnover->inSystemCurrency : null);
    }

    private function updateTurnoverStatistics(ProcessTradeEditingRequest $request, ObjectChanger $changer) : void
    {
        if ( ! $changer->isChanged('turnover') && ! $changer->isChanged('turnoverSystem')) {
            return;
        }

        $changes = $changer->getChanges();

        // ObjectChanger already mutated trade, so trade->turnover is the new value
        $oldTurnover       = $changes['turnover']->old ?? 0.0;
        $oldTurnoverSystem = $changes['turnoverSystem']->old ?? 0.0;

        $delta       = $request->trade->turnover - $oldTurnover;
        $deltaSystem = $request->trade->turnoverSystem - $oldTurnoverSystem;

        if ($delta === 0.0 && $deltaSystem === 0.0) {
            return;
        }

        $this->accountStatisticsAdapter->addOpenTurnover(
            $request->trade->accountId,
            $delta,
            $deltaSystem,
        );
    }
}
