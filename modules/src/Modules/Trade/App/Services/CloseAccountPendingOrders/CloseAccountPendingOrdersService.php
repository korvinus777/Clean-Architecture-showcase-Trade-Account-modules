<?php

declare(strict_types=1);

namespace Platform\Trade\App\Services\CloseAccountPendingOrders;

use Platform\Trade\Domain\Repositories\PendingOrderRepository;
use Platform\Trade\Domain\Services\ClosePendingOrder\ClosePendingOrderInput;
use Platform\Trade\Domain\Services\ClosePendingOrder\ClosePendingOrderRequest;

class CloseAccountPendingOrdersService implements CloseAccountPendingOrdersInput
{
    public function __construct(
        private PendingOrderRepository          $pendingOrderRepository,
        private ClosePendingOrderInput          $closePendingOrderService,
        private CloseAccountPendingOrdersOutput $output,
    ) {
    }

    public function close(CloseAccountPendingOrdersRequest $request) : CloseAccountPendingOrdersResult
    {
        $orders = $this->pendingOrderRepository->getPendingOfAccount($request->accountId);

        $results = [];

        foreach ($orders as $order) {
            $results[$order->id] = $this->closePendingOrderService->close(new ClosePendingOrderRequest(
                $order->id,
                $request->accountId,
                true,
            ));
        }

        return $this->output->done($results);
    }
}
