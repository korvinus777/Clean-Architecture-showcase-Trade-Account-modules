<?php

declare(strict_types=1);

namespace Platform\Trade\App\Services\CloseMtPendingOrder;

use Platform\Package\Components\Log\Contracts\Logger;
use Platform\Trade\Domain\Repositories\PendingOrderRepository;
use Platform\Trade\Domain\Services\ProcessPendingOrderClosing\ProcessPendingOrderClosingInput;
use Platform\Trade\Domain\Services\ProcessPendingOrderClosing\ProcessPendingOrderClosingRequest;

class CloseMtPendingOrderService implements CloseMtPendingOrderInput
{
    public function __construct(
        private Logger                          $logger,
        private CloseMtPendingOrderOutput       $output,
        private PendingOrderRepository          $pendingOrderRepository,
        private ProcessPendingOrderClosingInput $processPendingOrderClosingService,
    ) {
    }

    public function close(CloseMtPendingOrderRequest $request) : CloseMtPendingOrderResult
    {
        $this->logger->withContext([
            'module'   => 'trade',
            'service'  => class_basename(self::class),
            'orderId'  => $request->orderId,
        ]);

        $pendingOrder = $this->pendingOrderRepository->find($request->orderId);

        if ( ! $pendingOrder) {
            $this->logger->error('pending order not found', ['reason' => 'finding by requested id']);

            return $this->output->failed();
        }

        if ( ! $pendingOrder->status->isPending()) {
            $this->logger->error('order is not pending', ['status' => $pendingOrder->status->name]);

            return $this->output->failed();
        }

        $result = $this->processPendingOrderClosingService->process(new ProcessPendingOrderClosingRequest(
            $pendingOrder,
            $request->closeTime,
        ));

        return $result->order ? $this->output->closed($result->order) : $this->output->failed();
    }
}
