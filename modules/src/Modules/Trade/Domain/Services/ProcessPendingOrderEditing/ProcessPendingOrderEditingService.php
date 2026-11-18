<?php

declare(strict_types=1);

namespace Platform\Trade\Domain\Services\ProcessPendingOrderEditing;

use Platform\Package\Components\Collection\Arr;
use Platform\Package\Components\Log\Contracts\Logger;
use Platform\Package\Components\Collection\ObjectChanger;
use Platform\Package\Components\Events\Contracts\Dispatcher;
use Platform\Trade\Domain\Repositories\PendingOrderRepository;
use Platform\Package\Components\Database\Contracts\Transaction;

class ProcessPendingOrderEditingService implements ProcessPendingOrderEditingInput
{
    public function __construct(
        private Logger                           $logger,
        private Dispatcher                       $dispatcher,
        private ProcessPendingOrderEditingOutput $output,
        private PendingOrderRepository           $pendingOrderRepository,
        private Transaction                      $transaction,
    ) {
    }

    public function process(ProcessPendingOrderEditingRequest $request) : ProcessPendingOrderEditingResult
    {
        $this->logger->withContext([
            'module'   => 'trade',
            'service'  => class_basename(self::class),
            'orderId'  => $request->pendingOrder->id,
        ]);

        /**
         * Changes affects on next properties:
         * @uses PendingOrder::$type
         * @uses PendingOrder::$lots
         * @uses PendingOrder::$openTime
         * @uses PendingOrder::$price
         * @uses PendingOrder::$stopLimitPrice
         * @uses PendingOrder::stopLoss
         * @uses PendingOrder::takeProfit
         */
        $changer = (new ObjectChanger($request->pendingOrder))
            ->change('type', $request->type)
            ->change('lots', $request->lots)
            ->change('openTime', $request->time)
            ->change('price', $request->price)
            ->change('stopLimitPrice', $request->stopLimitPrice)
            ->change('stopLoss', $request->stopLoss)
            ->change('takeProfit', $request->takeProfit);

        if ( ! $changer->getChanges()) {
            return $this->output->processed($request->pendingOrder);
        }

        try {
            $changeId = $this->transaction->forMain(function () use ($request, $changer) : int {
                $this->pendingOrderRepository->save($request->pendingOrder);

                return $this->pendingOrderRepository->saveChanges(
                    $request->pendingOrder->id,
                    $changer->getChanges(),
                    $request->isFromMt,
                );
            });

            $this->dispatcher->dispatch('trade.order.edit', [$request->pendingOrder->toArray([
                'changeId' => $changeId,
            ])]);

            return $this->output->processed($request->pendingOrder);
        } catch (\Throwable $throwable) {
            $changer->rollback();

            $this->logger->critical('unexpected error', [
                'error'   => Arr::formatThrowable($throwable),
                'request' => (array) $request,
            ]);

            return $this->output->failed();
        }
    }
}
