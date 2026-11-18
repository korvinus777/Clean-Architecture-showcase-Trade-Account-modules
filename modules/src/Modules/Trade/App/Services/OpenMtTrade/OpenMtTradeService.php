<?php

declare(strict_types=1);

namespace Platform\Trade\App\Services\OpenMtTrade;

use Platform\Trade\Domain\Dto\AccountDto;
use Platform\Trade\Domain\Dto\TransactionDto;
use Platform\Trade\Domain\Adapters\SymbolAdapter;
use Platform\Trade\Domain\Adapters\AccountAdapter;
use Platform\Package\Components\Http\Request\Device;
use Platform\Package\Components\Http\Request\Context;
use Platform\Package\Components\Log\Contracts\Logger;
use Platform\Trade\Domain\Repositories\TradeRepository;
use Platform\Trade\Domain\Repositories\MetricRepository;
use Platform\Trade\Domain\Services\ProcessTradeOpening\ProcessTradeOpeningInput;
use Platform\Trade\Domain\Services\ProcessTradeOpening\ProcessTradeOpeningRequest;

class OpenMtTradeService implements OpenMtTradeInput
{
    public function __construct(
        private Logger                   $logger,
        private OpenMtTradeOutput        $output,
        private SymbolAdapter            $symbolAdapter,
        private AccountAdapter           $accountAdapter,
        private TradeRepository          $tradeRepository,
        private MetricRepository         $metricRepository,
        private ProcessTradeOpeningInput $processTradeOpeningService,
    ) {
    }

    public function open(OpenMtTradeRequest $request) : OpenMtTradeResult
    {
        $this->logger->withContext([
            'module'           => 'trade',
            'service'          => class_basename(self::class),
            'tradeId'          => $request->tradeId,
            'mt5TransactionId' => $request->mt5TransactionId,
        ]);

        $metric = $this->metricRepository->getOpening($request->tradeId);

        $metric->acceptingEvent();

        $metric->processedOnMt($request->time);

        if ($trade = $this->tradeRepository->find($request->tradeId)) {
            $this->logger->info('trade already opened');

            return $this->output->opened($trade);
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

        $context = new Context(Device::MT_DESKTOP, '', '', '');

        $result = $this->processTradeOpeningService->process(new ProcessTradeOpeningRequest(
            $request->tradeId,
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
            $this->makeTransaction($account, $request),
            $metric,
            $context,
        ));

        if ($result->alreadyProcessed) {
            return $this->output->alreadyProcessed();
        }

        if ($result->trade) {
            return $this->output->opened($result->trade);
        }

        return $this->output->canNotProcessTrade();
    }

    private function makeTransaction(AccountDto $account, OpenMtTradeRequest $request) : TransactionDto
    {
        return new TransactionDto(
            entityId: $request->tradeId,
            accountId: $account->id,
            leadId: $account->leadId,
            amount: 0,
            currencyId: $account->currencyId,
            groupId: $account->groupId,
            mt5TransactionId: $request->mt5TransactionId,
            mt5TransactionTime: $request->mt5TransactionTime,
        );
    }
}
