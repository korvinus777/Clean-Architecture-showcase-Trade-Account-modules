<?php

declare(strict_types=1);

namespace Platform\Trade\Domain\Services\CloseTrade;

use Platform\Trade\Domain\Dto\MetricDto;
use Platform\Package\Models\SettingModel;
use Platform\Trade\Domain\Dto\AccountDto;
use Platform\Trade\Domain\Entities\Trade;
use Platform\Trade\Domain\Entities\Symbol;
use Platform\Trade\Domain\Dto\ClosedTradeDto;
use Platform\Trade\Domain\Dto\TransactionDto;
use Platform\Package\Components\Time\DateTime;
use Platform\Package\Components\Collection\Arr;
use Platform\Trade\Domain\Adapters\SymbolAdapter;
use Platform\Trade\Domain\Adapters\AccountAdapter;
use Platform\Package\Components\Http\Response\Code;
use Platform\Trade\Domain\Adapters\CurrencyAdapter;
use Platform\Trade\Domain\ValueObjects\CloseReason;
use Platform\Package\Components\Http\Request\Device;
use Platform\Trade\Domain\Dto\NTCloseTransactionDto;
use Platform\Package\Components\Http\Request\Context;
use Platform\Package\Components\Log\Contracts\Logger;
use Platform\Trade\Domain\Adapters\MetatraderAdapter;
use Platform\Trade\Domain\Repositories\TradeRepository;
use Platform\Trade\Domain\Repositories\MetricRepository;
use Platform\Trade\Domain\Adapters\NativeTradeEngineAdapter;
use Platform\Package\Components\Database\Contracts\Transaction;
use Platform\Package\Components\System\ValueObjects\EngineType;
use Platform\Package\Components\System\Contracts\SettingRepository;
use Platform\Package\Components\System\Services\SettingResolver\Params;
use Platform\Trade\Domain\Services\ProcessTradeClosing\ProcessTradeClosingInput;
use Platform\Trade\Domain\Services\ProcessTradeClosing\ProcessTradeClosingResult;
use Platform\Trade\Domain\Services\ProcessTradeClosing\ProcessTradeClosingRequest;

class CloseTradeService implements CloseTradeInput
{
    public function __construct(
        private CloseTradeOutput  $output,
        private Logger            $logger,
        private Transaction       $transaction,
        private SymbolAdapter     $symbolAdapter,
        private AccountAdapter    $accountAdapter,
        private CurrencyAdapter   $currencyAdapter,
        private MetatraderAdapter $metatraderAdapter,
        private TradeRepository   $tradeRepository,
        private MetricRepository  $metricRepository,
        private SettingRepository $settingRepository,
        private NativeTradeEngineAdapter $nativeTradeEngineAdapter,
        private ProcessTradeClosingInput $processTradeClosingService,
    ) {
    }

    public function close(CloseTradeRequest $request) : CloseTradeResult
    {
        $this->logger->withContext([
            'module'   => 'trade',
            'service'  => class_basename(self::class),
            'tradeId'  => $request->tradeId,
        ]);

        $metric = MetricDto::start($request->requestedFromClient);

        $account = $this->accountAdapter->findAccount($request->accountId);

        if ( ! $account) {
            return $this->output->accountNotFound();
        }

        if ( ! ($account->isEnabled && $account->isTradable)) {
            return $this->output->accountNotTradable();
        }

        $trade = $this->tradeRepository->find($request->tradeId);

        if ( ! $trade || $trade->leadId !== $account->leadId) {
            return $this->output->tradeNotFound();
        }

        $symbol = $this->symbolAdapter->findForAccount($trade->symbolId, $account->id);

        if ( ! $symbol) {
            return $this->output->symbolUnavailable();
        }

        if ($symbol->isDisabled()) {
            return $this->output->symbolDisabled();
        }

        if ( ! $symbol->isMarketOpened) {
            return $this->output->symbolMarketClosed();
        }


        $engineType = $this->settingRepository->findOne('Engine Type', new Params());

        if ( ! $engineType) {
            return $this->output->engineTypeNotFound(Code::TRADE_ENGINE_TYPE_NOT_FOUND);
        }

        if ($request->deferred && $engineType->value() === EngineType::MT->value) {
            $this->metatraderAdapter->deferredCloseTrade(
                $trade->id,
                $account->login,
                $symbol->name,
                $trade->lotsLeft,
                $trade->direction->isBuy(),
            );

            return $this->output->asyncClosing();
        }

        return $this->processTradeByEngine($engineType, $account, $symbol, $trade, $metric);
    }

    private function processTradeByEngine(SettingModel $engineType, AccountDto $account, Symbol $symbol, Trade $trade, MetricDto $metric) : CloseTradeResult
    {
        return match ($engineType->value()) {
            EngineType::MT->value                   => $this->closeOnMt($trade, $account, $symbol, $metric),
            EngineType::NATIVE_TRADE_ENGINE->value  => $this->closeOnNt($trade, $account, $symbol, $metric),
            default                                 => $this->output->engineTypeNotFound(Code::TRADE_ENGINE_TYPE_NOT_FOUND),
        };
    }

    private function closeOnMt(Trade $trade, AccountDto $account, Symbol $symbol, MetricDto $metric) : CloseTradeResult
    {
        $metric->sendingOnMt();

        try {
            return $this->transaction->forMain(
                function () use ($trade, $account, $symbol, $metric) : CloseTradeResult {
                    $result = $this->metatraderAdapter->closeTrade(
                        $trade->id,
                        $account->login,
                        $symbol->name,
                        $trade->lotsLeft,
                        $trade->direction->isBuy(),
                    );

                    $metric->answeringFromMt();

                    if ($result->resultCode->isError()) {
                        return $this->output->cannotCloseOnMt($result->resultCode);
                    }

                    $this->metricRepository->saveClosing($trade->id, $result->dealId, $metric);

                    return $this->output->closed($trade->id, $result->price);
                },
            );
        } catch (\Throwable $throwable) {
            $this->logger->critical('unexpected error', [
                'request' => ['tradeId' => $trade->id],
                'error'   => Arr::formatThrowable($throwable),
            ]);

            return $this->output->error();
        }
    }

    private function closeOnNt(Trade $trade, AccountDto $account, Symbol $symbol, MetricDto $metric) : CloseTradeResult
    {
        $metric->sendingOnNt();

        try {
            return $this->transaction->forMain(
                function () use ($trade, $account, $symbol, $metric) : CloseTradeResult {
                    $result = $this->nativeTradeEngineAdapter->closeTrade(
                        $trade->id,
                        $account->login,
                    );

                    $metric->answeringFromMt();

                    if ($result->resultCode->isError()) {
                        return $this->output->cannotCloseOnNt($result->resultCode);
                    }

                    // Обновляем маржин аккаунта, если он пришел в ответе
                    if ($result->accountMargin !== null) {
                        $this->accountAdapter->updateMargin($account->login, $result->accountMargin);
                    }

                    $this->metricRepository->saveClosing($trade->id, $result->transactionId, $metric);

                    // Теперь нужно обработать закрытие сделки через ProcessTradeClosingInput
                    $response = $this->processTradeClosing($trade, $account, $symbol, $result, $metric);

                    if ( ! $response->closedTrade) {
                        return $this->output->cannotProcessClosing();
                    }

                    return $this->output->closed($trade->id, $result->price);
                },
            );
        } catch (\Throwable $throwable) {
            $this->logger->critical('unexpected error', [
                'request' => ['tradeId' => $trade->id],
                'error'   => Arr::formatThrowable($throwable),
            ]);

            return $this->output->error();
        }
    }

    private function processTradeClosing(Trade $trade, AccountDto $account, Symbol $symbol, NTCloseTransactionDto $result, MetricDto $metric) : ProcessTradeClosingResult
    {
        // Convert PnL and swap to system currency
        $pnlSystem = $this->currencyAdapter->converseToSystem($result->pnl, $account->currencyId, $account->groupId);
        $swapSystem = $this->currencyAdapter->converseToSystem($result->swap, $account->currencyId, $account->groupId);

        $closeReason = $result->reason !== null
            ? CloseReason::fromNativeEngineReason($result->reason)
            : CloseReason::USER;

        $closedTradeDto = new ClosedTradeDto(
            $trade->id,
            $result->transactionId,
            $result->price,
            DateTime::createFromTimestamp($result->time),
            $result->lots, // используем lots из close transaction
            $symbol->amountFromLots($result->lots),
            $result->swap,
            $swapSystem,
            $result->commission,
            $result->pnl, // PnL from native engine (already in account currency)
            $pnlSystem,
            $closeReason,
            $trade->turnover,
            $trade->turnoverSystem,
        );

        $transactionDto = new TransactionDto(
            entityId: $trade->id,
            accountId: $account->id,
            leadId: $account->leadId,
            amount: $result->pnl + $result->swap, // total amount from closing position
            currencyId: $account->currencyId,
            groupId: $account->groupId,
            mt5TransactionId: $result->transactionId,
            mt5TransactionTime: DateTime::createFromTimestamp($result->transactionTime),
        );

        $context = new Context(Device::WEB_DESKTOP, '', '', '');

        return $this->processTradeClosingService->process(
            new ProcessTradeClosingRequest(
                $trade,
                $account,
                $closedTradeDto,
                $transactionDto,
                $metric,
                $symbol,
                $context,
            ),
        );
    }
}
