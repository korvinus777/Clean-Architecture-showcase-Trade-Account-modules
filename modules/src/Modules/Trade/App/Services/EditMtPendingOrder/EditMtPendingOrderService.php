<?php

declare(strict_types=1);

namespace Platform\Trade\App\Services\EditMtPendingOrder;

use Platform\Package\Components\Log\Contracts\Logger;
use Platform\Trade\Domain\ValueObjects\PendingOrderType;
use Platform\Trade\Domain\Repositories\PendingOrderRepository;
use Platform\Trade\Domain\Services\ProcessPendingOrderEditing\ProcessPendingOrderEditingInput;
use Platform\Trade\Domain\Services\ProcessPendingOrderEditing\ProcessPendingOrderEditingRequest;

class EditMtPendingOrderService implements EditMtPendingOrderInput
{
    public function __construct(
        private Logger                          $logger,
        private EditMtPendingOrderOutput        $output,
        private PendingOrderRepository          $pendingOrderRepository,
        private ProcessPendingOrderEditingInput $processPendingOrderEditingService,
    ) {
    }

    public function edit(EditMtPendingOrderRequest $request) : EditMtPendingOrderResult
    {
        $this->logger->withContext([
            'module'   => 'trade',
            'service'  => class_basename(self::class),
            'orderId'  => $request->orderId,
        ]);

        $pendingOrder = $this->pendingOrderRepository->find($request->orderId);

        if ( ! $pendingOrder) {
            $this->logger->error('pending order not found', ['reason' => 'finding by requested id']);

            return $this->output->pendingOrderNotFound();
        }

        if ( ! $pendingOrder->status->isPending()) {
            $this->logger->error('order is not pending', ['status' => $pendingOrder->status->name]);

            return $this->output->pendingOrderNotInPendingStatus();
        }

        $type = PendingOrderType::tryFrom($request->type);

        if ( ! $type) {
            $this->logger->error('request  has wrong type', ['type' => $request->type]);

            return $this->output->orderTypeDoesNotExist();
        }

        $result = $this->processPendingOrderEditingService->process(new ProcessPendingOrderEditingRequest(
            $pendingOrder,
            $type,
            $request->lots,
            $request->time,
            true,
            $request->price,
            $request->stopLimitPrice,
            $request->stopLoss,
            $request->takeProfit,
        ));

        return $result->order ? $this->output->edited($result->order) : $this->output->canNotProcessedEdit();
    }
}
