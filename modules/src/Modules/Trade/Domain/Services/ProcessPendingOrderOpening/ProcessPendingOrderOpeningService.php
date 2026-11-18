<?php

declare(strict_types=1);

namespace Platform\Trade\Domain\Services\ProcessPendingOrderOpening;

use Platform\Package\Components\Collection\Arr;
use Platform\Trade\Domain\Entities\PendingOrder;
use Platform\Package\Components\Log\Contracts\Logger;
use Platform\Trade\Domain\Repositories\MetricRepository;
use Platform\Trade\Domain\ValueObjects\PendingOrderStatus;
use Platform\Package\Components\Events\Contracts\Dispatcher;
use Platform\Trade\Domain\Repositories\PendingOrderRepository;
use Platform\Package\Components\Database\Contracts\Transaction;

class ProcessPendingOrderOpeningService implements ProcessPendingOrderOpeningInput
{
    public function __construct(
        private Logger                           $logger,
        private Transaction                      $transaction,
        private MetricRepository                 $metricRepository,
        private PendingOrderRepository           $orderRepository,
        private ProcessPendingOrderOpeningOutput $output,
        private Dispatcher                       $dispatcher,
    ) {
    }

    public function process(ProcessPendingOrderOpeningRequest $request) : ProcessPendingOrderOpeningResult
    {
        $this->logger->withContext([
            'module'   => 'trade',
            'service'  => class_basename(self::class),
            'orderId'  => $request->id,
        ]);

        try {
            $order = $this->transaction->forMain(function () use ($request) {
                $order = $this->makeEntity($request);

                $this->orderRepository->save($order);

                $request->metric->processedOnServer();

                $this->metricRepository->saveOpeningOrder($order->id, $request->metric);

                $this->dispatcher->dispatch('trade.order.created', [$order->toArray([
                    'leadId'       => $request->account->leadId,
                    'accountLogin' => $request->account->login,
                ])]);

                return $order;
            });

            return $this->output->processed($order);
        } catch (\Throwable $throwable) {
            $this->logger->critical('unexpected error', [
                'error'   => Arr::formatThrowable($throwable),
                'request' => $request->toArray(),
            ]);

            return $this->output->canNotProcess();
        }
    }

    private function makeEntity(ProcessPendingOrderOpeningRequest $request) : PendingOrder
    {
        return new PendingOrder(
            $request->id,
            $request->account->id,
            $request->symbol->id,
            $request->lots,
            $request->price,
            $request->stopLimitPrice,
            $request->stopLoss,
            $request->takeProfit,
            $request->type,
            PendingOrderStatus::PENDING,
            $request->time,
        );
    }
}
