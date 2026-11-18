<?php

declare(strict_types=1);

namespace Platform\Trade\App\Services\EditTrade;

use Platform\Trade\Domain\Dto\QuoteDto;
use Platform\Package\Models\SettingModel;
use Platform\Trade\Domain\Dto\AccountDto;
use Platform\Trade\Domain\Entities\Trade;
use Platform\Trade\Domain\Entities\Symbol;
use Platform\Package\Components\Time\DateTime;
use Platform\Trade\Domain\Adapters\SymbolAdapter;
use Platform\Trade\Domain\ValueObjects\Direction;
use Platform\Trade\Domain\Adapters\AccountAdapter;
use Platform\Package\Components\Log\Contracts\Logger;
use Platform\Trade\Domain\Adapters\MetatraderAdapter;
use Platform\Trade\Domain\Repositories\TradeRepository;
use Platform\Trade\Domain\Adapters\NativeTradeEngineAdapter;
use Platform\Package\Components\System\ValueObjects\EngineType;
use Platform\Package\Components\System\Contracts\SettingRepository;
use Platform\Package\Components\System\Services\SettingResolver\Params;
use Platform\Trade\Domain\Services\ProcessTradeEditing\ProcessTradeEditingInput;
use Platform\Trade\Domain\Services\ProcessTradeEditing\ProcessTradeEditingRequest;

class EditTradeService implements EditTradeInput
{
    public function __construct(
        private EditTradeOutput          $output,
        private Logger                   $logger,
        private SymbolAdapter            $symbolAdapter,
        private AccountAdapter           $accountAdapter,
        private MetatraderAdapter        $metatraderAdapter,
        private NativeTradeEngineAdapter $nativeTradeEngineAdapter,
        private TradeRepository          $tradeRepository,
        private SettingRepository        $settingRepository,
        private ProcessTradeEditingInput $processTradeEditingService,
    ) {
    }

    public function edit(EditTradeRequest $request) : EditTradeResult
    {
        $this->logger->withContext([
            'module'   => 'trade',
            'service'  => class_basename(self::class),
            'tradeId'  => $request->tradeId,
        ]);

        $trade = $this->tradeRepository->find($request->tradeId);

        if ( ! $trade || $trade->accountId !== $request->accountId) {
            return $this->output->tradeNotFound();
        }

        $account = $this->accountAdapter->findAccount($request->accountId);

        if ( ! $account || ! $account->isTradable || ! $account->isEnabled) {
            return $this->output->accountNotAvailable($account);
        }

        $symbol = $this->symbolAdapter->findForAccount($trade->symbolId, $account->id);

        if ( ! $symbol) {
            return $this->output->symbolUnavailable();
        }

        $quote = $this->symbolAdapter->findQuote($trade->symbolId, $request->accountId);

        if ( ! $quote) {
            return $this->output->quoteNotFound();
        }

        if ($request->stopLoss && $output = $this->validateStopLoss($request, $trade, $symbol, $quote)) {
            return $output;
        }

        if ($request->takeProfit && $output = $this->validateTakeProfit($request, $trade, $symbol, $quote)) {
            return $output;
        }

        $engineType = $this->settingRepository->findOne('Engine Type', new Params());

        if ( ! $engineType) {
            return $this->output->engineTypeNotFound();
        }

        return $this->editTrade($engineType, $request, $account, $trade, $symbol);
    }

    private function validateStopLoss(EditTradeRequest $request, Trade $trade, Symbol $symbol, QuoteDto $quote) : ?EditTradeResult
    {
        if ($request->stopLoss === $trade->stopLoss) {
            return null;
        }

        $stopLoss     = round($request->stopLoss, $symbol->precision);
        $currentPrice = round($trade->direction->isBuy() ? $quote->bid : $quote->ask, $symbol->precision);

        if ($trade->direction->isBuy()) {
            if ($stopLoss > $currentPrice) {
                return $this->output->stopLossHigherThanCurrentPrice();
            }

            return null;
        }

        if ($stopLoss < $currentPrice) {
            return $this->output->stopLossLowerThanCurrentPrice();
        }

        return null;
    }

    private function validateTakeProfit(EditTradeRequest $request, Trade $trade, Symbol $symbol, QuoteDto $quote) : ?EditTradeResult
    {
        if ($request->takeProfit === $trade->takeProfit) {
            return null;
        }

        $takeProfit   = round($request->takeProfit, $symbol->precision);
        $currentPrice = round($trade->direction->isBuy() ? $quote->bid : $quote->ask, $symbol->precision);

        if ($trade->direction->isBuy()) {
            if ($takeProfit < $currentPrice) {
                return $this->output->takeProfitLowerThanCurrentPrice();
            }

            return null;
        }

        if ($takeProfit > $currentPrice) {
            return $this->output->takeProfitHigherThanCurrentPrice();
        }

        return null;
    }

    private function editTrade(SettingModel $engineType, EditTradeRequest $request, AccountDto $account, Trade $trade, Symbol $symbol) : EditTradeResult
    {
        return match ($engineType->value()) {
            EngineType::MT->value                  => $this->editMtTrade($request, $account, $trade, $symbol),
            EngineType::NATIVE_TRADE_ENGINE->value => $this->editNtTrade($request, $account, $trade, $symbol),
            default                                => $this->output->engineTypeNotFound(),
        };
    }

    private function editMtTrade(EditTradeRequest $request, AccountDto $account, Trade $trade, Symbol $symbol) : EditTradeResult
    {
        $resultCode = $this->metatraderAdapter->editTrade(
            tradeId: $trade->id,
            login: $account->login,
            symbolId: $trade->symbolId,
            stopLoss: $request->stopLoss ?? $trade->stopLoss,
            takeProfit: $request->takeProfit ?? $trade->takeProfit,
        );

        if ($resultCode->isError()) {
            return $this->output->cannotEditOnMt($resultCode);
        }

        return $this->processTrade($request, $account, $trade, $symbol);
    }

    private function editNtTrade(EditTradeRequest $request, AccountDto $account, Trade $trade, Symbol $symbol) : EditTradeResult
    {
        $result = $this->nativeTradeEngineAdapter->editTrade(
            tradeId: $trade->id,
            login: $account->login,
            symbolId: $trade->symbolId,
            isBuy: $request->direction ? ! Direction::from($request->direction)->isBuy() : ! $trade->direction->isBuy(),
            openDate: $request->openDate ? (new DateTime($request->openDate))->getTimestampMs() : $trade->openTime->getTimestampMs(),
            openPrice: $request->openPrice ?? $trade->openPrice,
            volume: $request->volume ?? $trade->lots,
            swap: $request->swap ?? $trade->swap,
            stopLoss: $request->stopLoss ?? $trade->stopLoss,
            takeProfit: $request->takeProfit ?? $trade->takeProfit,
        );

        if ($result->resultCode->isError()) {
            return $this->output->cannotEditOnNt($result->resultCode);
        }

        if ($result->accountMargin !== null) {
            $this->accountAdapter->updateMargin($account->login, $result->accountMargin);
        }

        return $this->processTrade($request, $account, $trade, $symbol);
    }

    private function processTrade(EditTradeRequest $request, AccountDto $account, Trade $trade, Symbol $symbol) : EditTradeResult
    {
        $result = $this->processTradeEditingService->process(new ProcessTradeEditingRequest(
            trade: $trade,
            account: $account,
            context: $request->context,
            direction: $request->direction ? Direction::from($request->direction) : $trade->direction,
            lots: $request->volume,
            lotsLeft: $request->volume,
            openPrice: $request->openPrice,
            openTime: $request->openDate ? new DateTime($request->openDate) : $trade->openTime,
            swap: $request->swap,
            stopLoss: $request->stopLoss,
            takeProfit: $request->takeProfit,
            profitCurrencyId: $symbol->profitCurrencyId,
        ));

        return $result->trade ? $this->output->edited() : $this->output->savingFailed();
    }
}
