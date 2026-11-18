<?php

declare(strict_types=1);

namespace Platform\Trade\App\Services\DeleteOpenedTrade;

use Platform\Package\Components\Collection\Arr;
use Platform\Trade\Domain\Dto\TransactionInfoDto;
use Platform\Package\Components\Log\Contracts\Logger;
use Platform\Trade\Domain\Adapters\TransactionAdapter;
use Platform\Trade\Domain\Repositories\TradeRepository;
use Platform\Trade\Domain\Repositories\MetricRepository;
use Platform\Package\Components\Events\Contracts\Dispatcher;
use Platform\Package\Components\Database\Contracts\Transaction;

class DeleteOpenedTradeService implements DeleteOpenedTradeInput
{
    public function __construct(
        private Logger                  $logger,
        private Dispatcher              $dispatcher,
        private Transaction             $transaction,
        private TradeRepository         $tradeRepository,
        private MetricRepository        $metricRepository,
        private TransactionAdapter      $transactionAdapter,
        private DeleteOpenedTradeOutput $output,
    ) {
    }

    public function delete(DeleteOpenedTradeRequest $request) : DeleteOpenedTradeResult
    {
        $this->logger->withContext([
            'module'  => 'trade',
            'service' => class_basename(self::class),
            'tradeId' => $request->tradeId,
        ]);

        $trade = $this->tradeRepository->find($request->tradeId);

        if ( ! $trade) {
            return $this->output->tradeNotFound();
        }

        if ($trade->isClosed) {
            $this->logger->error('can not delete closed trade');

            return $this->output->closedTradeNotDeletes();
        }

        try {
            /** @var TransactionInfoDto $transaction */
            $transaction = $this->transaction->forMain(function () use ($trade) : TransactionInfoDto {
                $this->metricRepository->deleteOpening($trade->id);

                $this->transactionAdapter->cancelOpenCommission($trade->id);

                $transaction = $this->transactionAdapter->cancelOpen($trade->id);

                $this->tradeRepository->delete($trade->id);

                return $transaction;
            });

            $this->dispatcher->dispatch('trade.open.cancelled', [$trade->toArray([
                'transaction' => $transaction->toArray(),
            ])]);

            return $this->output->deleted();
        } catch (\Throwable $throwable) {
            $this->logger->critical('unexpected error', ['error' => Arr::formatThrowable($throwable, true)]);

            return $this->output->unexpectedError();
        }
    }
}
