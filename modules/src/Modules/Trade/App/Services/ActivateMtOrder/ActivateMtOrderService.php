<?php

declare(strict_types=1);

namespace Platform\Trade\App\Services\ActivateMtOrder;

use Platform\Trade\Domain\Dto\MetricDto;
use Platform\Trade\Domain\Dto\AccountDto;
use Platform\Trade\Domain\Dto\TransactionDto;
use Platform\Package\Components\Time\DateTime;
use Platform\Trade\Domain\Adapters\SymbolAdapter;
use Platform\Trade\Domain\Adapters\AccountAdapter;
use Platform\Package\Components\Http\Request\Device;
use Platform\Package\Components\Http\Request\Context;
use Platform\Package\Components\Log\Contracts\Logger;
use Platform\Trade\Domain\Repositories\TradeRepository;
use Platform\Trade\Domain\Repositories\MetricRepository;
use Platform\Trade\Domain\Repositories\PendingOrderRepository;
use Platform\Trade\Domain\Services\ProcessTradeOpening\ProcessTradeOpeningInput;
use Platform\Trade\Domain\Services\ProcessTradeOpening\ProcessTradeOpeningRequest;

class ActivateMtOrderService implements ActivateMtOrderInput
{
    public function __construct(
        private ActivateMtOrderOutput    $output,
        private Logger                   $logger,
        private SymbolAdapter            $symbolAdapter,
        private AccountAdapter           $accountAdapter,
        private TradeRepository          $tradeRepository,
        private MetricRepository         $metricRepository,
        private PendingOrderRepository   $pendingOrderRepository,
        private ProcessTradeOpeningInput $processTradeOpeningService,
    ) {
    }

    public function activate(ActivateMtOrderRequest $request) : ActivateMtOrderResult
    {
        $this->logger->withContext([
            'module'           => 'trade',
            'service'          => class_basename(self::class),
            'orderId'          => $request->orderId,
            'mt5TransactionId' => $request->mt5TransactionId,
        ]);

        $openMetric = $this->metric($request->time);
        $activateMetric = $this->metric($request->time);

        $pendingOrder = $this->pendingOrderRepository->find($request->orderId);

        if ( ! $pendingOrder) {
            $this->logger->critical('pending order not found', ['reason' => 'finding by id']);

            return $this->output->pendingOrderNotFound();
        }

        if ( ! $pendingOrder->status->isPending()) {
            $this->logger->error('order is not pending', ['status' => $pendingOrder->status->name]);

            return $this->output->orderIsNotPending();
        }

        $symbol = $this->symbolAdapter->findOrCreate($request->symbol);

        if ( ! $symbol) {
            $this->logger->critical('symbol not found', [
                'symbol' => $request->symbol,
                'reason' => 'cannot find or create symbol by name',
            ]);

            return $this->output->symbolNotFound();
        }

        $account = $this->accountAdapter->findAccount($pendingOrder->accountId);

        if ( ! $account) {
            $this->logger->critical('account not found', [
                'accountId' => $pendingOrder->accountId,
                'reason'    => 'finding by order account id',
            ]);

            return $this->output->accountNotFound();
        }

        $tradeId = $this->tradeRepository->getNewId();
        $context = new Context(Device::MT_DESKTOP, '', '', '');

        $result = $this->processTradeOpeningService->process(new ProcessTradeOpeningRequest(
            $tradeId,
            $symbol,
            $request->lots,
            $request->direction,
            $account,
            $request->price,
            $request->time,
            $request->stopLoss,
            $request->takeProfit,
            $request->swap,
            $request->commission,
            $this->makeTransaction($account, $tradeId, $request),
            $openMetric,
            $context,
        ));

        if ($result->alreadyProcessed) {
            return $this->output->alreadyProcessed();
        }

        if ($result->trade) {
            $this->pendingOrderRepository->makeActivated($pendingOrder->id, $tradeId, $request->time);

            $activateMetric->processedOnServer();

            $this->metricRepository->saveActivating($tradeId, $pendingOrder->id, $activateMetric);

            return $this->output->activated($result->trade);
        }

        $this->logger->error('order not activated on platform');

        return $this->output->notActivated();
    }

    private function makeTransaction(AccountDto $account, int $tradeId, ActivateMtOrderRequest $request) : TransactionDto
    {
        return new TransactionDto(
            $tradeId,
            $account->id,
            $account->leadId,
            0,
            $account->currencyId,
            $account->groupId,
            $request->mt5TransactionId,
            $request->time,
        );
    }

    private function metric(DateTime $processedOnMt) : MetricDto
    {
        $metric = new MetricDto();

        $metric->acceptingEvent();
        $metric->processedOnMt($processedOnMt);

        return $metric;
    }
}
