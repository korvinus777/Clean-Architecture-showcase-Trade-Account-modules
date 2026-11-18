<?php

declare(strict_types=1);

namespace Platform\Trade\Domain\Services\ProcessPendingOrderClosing;

use Platform\Package\Components\Collection\Arr;
use Platform\Package\Components\Log\Contracts\Logger;
use Platform\Trade\Domain\ValueObjects\PendingOrderStatus;
use Platform\Package\Components\Events\Contracts\Dispatcher;
use Platform\Trade\Domain\Repositories\PendingOrderRepository;

class ProcessPendingOrderClosingService implements ProcessPendingOrderClosingInput
{
    public function __construct(
        private Logger                           $logger,
        private Dispatcher                       $dispatcher,
        private PendingOrderRepository           $pendingOrderRepository,
        private ProcessPendingOrderClosingOutput $output,
    ) {
    }

    public function process(ProcessPendingOrderClosingRequest $request) : ProcessPendingOrderClosingResult
    {
        $this->logger->withContext([
            'module'   => 'trade',
            'service'  => class_basename(self::class),
            'orderId'  => $request->pendingOrder->id,
        ]);

        $pendingOrder = $request->pendingOrder;
        $pendingOrder->status = PendingOrderStatus::CANCELED;
        $pendingOrder->closeTime = $request->closeTime;

        try {
            $this->pendingOrderRepository->save($pendingOrder);

            $this->dispatcher->dispatch('trade.order.close', [$pendingOrder->toArray()]);

            return $this->output->processed($pendingOrder);
        } catch (\Throwable $throwable) {
            $this->logger->critical('unexpected error', [
                'error'   => Arr::formatThrowable($throwable),
                'request' => (array) $request,
            ]);

            return $this->output->failed();
        }
    }
}
