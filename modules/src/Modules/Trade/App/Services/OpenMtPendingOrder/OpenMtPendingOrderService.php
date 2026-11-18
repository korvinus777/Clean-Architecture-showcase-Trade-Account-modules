<?php

declare(strict_types=1);

namespace Platform\Trade\App\Services\OpenMtPendingOrder;

use Platform\Trade\Domain\Adapters\SymbolAdapter;
use Platform\Trade\Domain\Adapters\AccountAdapter;
use Platform\Package\Components\Log\Contracts\Logger;
use Platform\Trade\Domain\Repositories\MetricRepository;
use Platform\Trade\Domain\Repositories\PendingOrderRepository;
use Platform\Trade\Domain\Services\ProcessPendingOrderOpening\ProcessPendingOrderOpeningInput;
use Platform\Trade\Domain\Services\ProcessPendingOrderOpening\ProcessPendingOrderOpeningRequest;

class OpenMtPendingOrderService implements OpenMtPendingOrderInput
{
    public function __construct(
        private Logger                          $logger,
        private SymbolAdapter                   $symbolAdapter,
        private AccountAdapter                  $accountAdapter,
        private MetricRepository                $metricRepository,
        private PendingOrderRepository          $orderRepository,
        private OpenMtPendingOrderOutput        $output,
        private ProcessPendingOrderOpeningInput $pendingOrderOpeningService,
    ) {
    }

    public function open(OpenMtPendingOrderRequest $request) : OpenMtPendingOrderResult
    {
        $this->logger->withContext([
            'module'   => 'trade',
            'service'  => class_basename(self::class),
            'orderId'  => $request->orderId,
        ]);

        $metric = $this->metricRepository->getOpening($request->orderId);

        $metric->acceptingEvent();

        $metric->processedOnMt($request->time);

        if ($order = $this->orderRepository->find($request->orderId)) {
            $this->logger->info('pending order already opened');

            return $this->output->success($order);
        }

        $symbol = $this->symbolAdapter->findOrCreate($request->symbol);

        if ( ! $symbol) {
            $this->logger->critical('symbol not found', [
                'symbol' => $request->symbol,
                'reason' => 'finding or creating symbol by name',
            ]);

            return $this->output->symbolNotAvailable();
        }

        $account = $this->accountAdapter->findByLogin($request->login);

        if ( ! $account) {
            $this->logger->critical('account not found', [
                'login'  => $request->login,
                'reason' => 'finding by MT5 login',
            ]);

            return $this->output->accountNotFound();
        }

        $result = $this->pendingOrderOpeningService
            ->process(
                new ProcessPendingOrderOpeningRequest(
                    $request->orderId,
                    $request->lots,
                    $request->price,
                    $request->stopLimitPrice,
                    $request->stopLoss,
                    $request->takeProfit,
                    $request->type,
                    $request->time,
                    $symbol,
                    $account,
                    $metric,
                ),
            );

        if ($result->order) {
            return $this->output->success($result->order);
        }

        return $this->output->canNotProcessOrder();
    }
}
