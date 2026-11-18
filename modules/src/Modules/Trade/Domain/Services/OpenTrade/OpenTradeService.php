<?php

declare(strict_types=1);

namespace Platform\Trade\Domain\Services\OpenTrade;

use Platform\Trade\Domain\Dto\QuoteDto;
use Platform\Trade\Domain\Dto\MetricDto;
use Platform\Package\Models\SettingModel;
use Platform\Trade\Domain\Dto\AccountDto;
use Platform\Trade\Domain\Entities\Symbol;
use Platform\Trade\Domain\Dto\TransactionDto;
use Platform\Package\Components\Time\DateTime;
use Platform\Trade\Domain\Adapters\SymbolAdapter;
use Platform\Trade\Domain\Adapters\AccountAdapter;
use Platform\Package\Components\Http\Response\Code;
use Platform\Package\Components\Http\Request\Device;
use Platform\Package\Components\Http\Request\Context;
use Platform\Trade\Domain\Adapters\MetatraderAdapter;
use Platform\Trade\Domain\Repositories\TradeRepository;
use Platform\Trade\Domain\Repositories\MetricRepository;
use Platform\Trade\Domain\Adapters\NativeTradeEngineAdapter;
use Platform\Package\Components\System\ValueObjects\EngineType;
use Platform\Package\Components\System\Contracts\SettingRepository;
use Platform\Package\Components\System\Services\SettingResolver\Params;
use Platform\Trade\Domain\Services\ProcessTradeOpening\ProcessTradeOpeningInput;
use Platform\Trade\Domain\Services\ProcessTradeOpening\ProcessTradeOpeningRequest;

class OpenTradeService implements OpenTradeInput
{
    public function __construct(
        private readonly OpenTradeOutput   $output,
        private readonly SymbolAdapter     $symbolAdapter,
        private readonly AccountAdapter    $accountAdapter,
        private readonly MetatraderAdapter $metatraderAdapter,
        private readonly TradeRepository   $tradeRepository,
        private readonly MetricRepository  $metricRepository,
        private readonly SettingRepository $settingRepository,
        private readonly NativeTradeEngineAdapter $nativeTradeEngineAdapter,
        private readonly ProcessTradeOpeningInput $processTradeOpeningService,
    ) {
    }

    public function open(OpenTradeRequest $request) : OpenTradeResult
    {
        $metric = MetricDto::start($request->requestedFromClient);

        $account = $this->accountAdapter->findAccount($request->accountId);

        if ( ! $account) {
            return $this->output->accountNotFound();
        }

        if ( ! ($account->isEnabled && $account->isTradable)) {
            return $this->output->accountNotTradable();
        }

        $symbol = $this->symbolAdapter->findForAccount($request->symbolId, $account->id);

        if ( ! $symbol) {
            return $this->output->symbolUnavailable();
        }

        if ($symbol->isDisabled()) {
            return $this->output->symbolDisabled();
        }

        if ( ! $symbol->isMarketOpened) {
            return $this->output->symbolMarketClosed();
        }

        if ($output = $this->validateLots($request->lots, $symbol)) {
            return $output;
        }

        $quote = $this->symbolAdapter->findQuote($request->symbolId, $request->accountId);

        if ( ! $quote) {
            return $this->output->quoteNotFound();
        }

        if ($request->stopLoss && $output = $this->validateStopLoss($request, $symbol, $quote)) {
            return $output;
        }

        if ($request->takeProfit && $output = $this->validateTakeProfit($request, $symbol, $quote)) {
            return $output;
        }

        $engineType = $this->settingRepository->findOne('Engine Type', new Params());

        if ( ! $engineType) {

            return $this->output->engineTypeNotFound(Code::TRADE_ENGINE_TYPE_NOT_FOUND);
        }

        return $this->processTradeByEngine($engineType, $account, $symbol, $request, $metric);
    }

    private function validateLots(float $lots, Symbol $symbol) : ?OpenTradeResult
    {
        if ( ! $symbol->isValidVolumeStep($lots)) {
            return $this->output->incorrectVolume(Code::TRADE_INCORRECT_VOLUME_STEP);
        }

        if ( ! $symbol->isValidMinVolume($lots)) {
            return $this->output->incorrectVolume(
                Code::TRADE_INCORRECT_VOLUME_LESS_MIN,
                $symbol->minVolume,
            );
        }

        if ( ! $symbol->isValidMaxVolume($lots)) {
            return $this->output->incorrectVolume(
                Code::TRADE_INCORRECT_VOLUME_MORE_MAX,
                $symbol->maxVolume,
            );
        }

        return null;
    }

    private function validateStopLoss(OpenTradeRequest $request, Symbol $symbol, QuoteDto $quote) : ?OpenTradeResult
    {
        $currentPrice = round($request->direction->isBuy() ? $quote->bid : $quote->ask, $symbol->precision);
        $stopLoss     = round($request->stopLoss, $symbol->precision);

        if ($request->direction->isBuy()) {
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

    private function validateTakeProfit(OpenTradeRequest $request, Symbol $symbol, QuoteDto $quote) : ?OpenTradeResult
    {
        $currentPrice = round($request->direction->isBuy() ? $quote->bid : $quote->ask, $symbol->precision);
        $takeProfit   = round($request->takeProfit, $symbol->precision);

        if ($request->direction->isBuy()) {
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

    private function processTradeByEngine(SettingModel $engineType, AccountDto $account, Symbol $symbol, OpenTradeRequest $request, MetricDto $metric) : OpenTradeResult
    {
        return match ($engineType->value()) {
            EngineType::MT->value                   => $this->openOnMt($request, $account, $symbol, $metric),
            EngineType::NATIVE_TRADE_ENGINE->value  => $this->openOnNt($request, $account, $symbol, $metric),
            default                                 => $this->output->engineTypeNotFound(Code::TRADE_ENGINE_TYPE_NOT_FOUND),
        };
    }

    private function openOnMt(OpenTradeRequest $request, AccountDto $account, Symbol $symbol, MetricDto $metric) : OpenTradeResult
    {
        $tradeId = $this->tradeRepository->getNewId();
        $metric->sendingOnMt();

        $result = $this->metatraderAdapter->openTrade(
            $tradeId,
            $account->login,
            $symbol->name,
            $request->lots,
            $request->direction->isBuy(),
            $symbol->precision,
            $request->stopLoss,
            $request->takeProfit,
        );

        $metric->answeringFromMt();

        if ($result->resultCode->isError()) {
            return $this->output->cannotOpenOnMt($result->resultCode);
        }

        $this->metricRepository->saveOpening($tradeId, $metric);

        return $this->output->opened($tradeId, $result->price);
    }

    private function openOnNt(OpenTradeRequest $request, AccountDto $account, Symbol $symbol, MetricDto $metric) : OpenTradeResult
    {
        $tradeId = $this->tradeRepository->getNewId();

        $metric->sendingOnNt();

        $result = $this->nativeTradeEngineAdapter->openTrade(
            $tradeId,
            $account->login,
            $symbol->id,
            $request->lots,
            $request->direction->isBuy(),
            $symbol->precision,
            $request->stopLoss,
            $request->takeProfit,
            $request->requestedFromClient,
        );

        $metric->answeringFromNt();

        if ($result->resultCode->isError()) {
            return $this->output->cannotOpenOnNt($result->resultCode);
        }

        // Обновляем маржин аккаунта, если он пришел в ответе
        if ($result->accountMargin !== null) {
            $this->accountAdapter->updateMargin($account->login, $result->accountMargin);
        }

        $this->metricRepository->saveOpening($tradeId, $metric);

        $context = new Context(Device::WEB_DESKTOP, '', '', '');
        $response = $this->processTradeOpeningService->process(new ProcessTradeOpeningRequest(
            $tradeId,
            $symbol,
            $result->volume,
            $request->direction, // todo  it is no correct . should give result from response engine Direction::from($result->direction),
            $account,
            $result->price,
            DateTime::createFromTimestamp($result->time),
            $result->stopLoss,
            $result->takeProfit,
            $result->swap,
            $result->commission,
            $this->makeTransaction($account, $tradeId, $result->transactionId, $result->time),
            $metric,
            $context,
        ));

        return $this->output->opened($tradeId, $response->trade->openPrice);
    }

    private function makeTransaction(AccountDto $account, int $tradeId, int $transactionId, int $transactionTime) : TransactionDto
    {
        return new TransactionDto(
            entityId: $tradeId,
            accountId: $account->id,
            leadId: $account->leadId,
            amount: 0,
            currencyId: $account->currencyId,
            groupId: $account->groupId,
            mt5TransactionId: $transactionId,
            mt5TransactionTime: DateTime::createFromTimestamp($transactionTime),
        );
    }
}
