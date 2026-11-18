<?php

declare(strict_types=1);

namespace Platform\Trade\Domain\Services\OpenPendingOrder;

use Platform\Trade\Domain\Dto\QuoteDto;
use Platform\Trade\Domain\Dto\MetricDto;
use Platform\Package\Models\SettingModel;
use Platform\Trade\Domain\Dto\AccountDto;
use Platform\Trade\Domain\Entities\Symbol;
use Platform\Package\Components\Time\DateTime;
use Platform\Trade\Domain\Adapters\SymbolAdapter;
use Platform\Trade\Domain\Adapters\AccountAdapter;
use Platform\Package\Components\Http\Response\Code;
use Platform\Trade\Domain\Adapters\MetatraderAdapter;
use Platform\Trade\Domain\Repositories\MetricRepository;
use Platform\Trade\Domain\ValueObjects\PendingOrderType;
use Platform\Trade\Domain\Adapters\NativeTradeEngineAdapter;
use Platform\Trade\Domain\Repositories\PendingOrderRepository;
use Platform\Package\Components\System\ValueObjects\EngineType;
use Platform\Package\Components\System\Contracts\SettingRepository;
use Platform\Package\Components\System\Services\SettingResolver\Params;
use Platform\Trade\Domain\Services\ProcessPendingOrderOpening\ProcessPendingOrderOpeningInput;
use Platform\Trade\Domain\Services\ProcessPendingOrderOpening\ProcessPendingOrderOpeningRequest;

class OpenPendingOrderService implements OpenPendingOrderInput
{
    public function __construct(
        private SymbolAdapter                   $symbolAdapter,
        private AccountAdapter                  $accountAdapter,
        private MetricRepository                $metricRepository,
        private MetatraderAdapter               $metatraderAdapter,
        private NativeTradeEngineAdapter        $nativeTradeEngineAdapter,
        private OpenPendingOrderOutput          $output,
        private PendingOrderRepository          $pendingOrderRepository,
        private SettingRepository               $settingRepository,
        private ProcessPendingOrderOpeningInput $pendingOrderOpeningService,
    ) {
    }

    public function open(OpenPendingOrderRequest $request) : OpenPendingOrderResult
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

        $quoteDto = $this->symbolAdapter->findQuote($symbol->id, $request->accountId);

        if ( ! $quoteDto) {
            return $this->output->unexpectedError();
        }

        if ($output = $this->validatePrice($request, $symbol, $quoteDto)) {
            return $output;
        }

        if ($request->stopLoss && $output = $this->validateStopLoss($request, $symbol)) {
            return $output;
        }

        if ($request->takeProfit && $output = $this->validateTakeProfit($request, $symbol)) {
            return $output;
        }

        $engineType = $this->settingRepository->findOne('Engine Type', new Params());

        if ( ! $engineType) {
            return $this->output->engineTypeNotFound();
        }

        return $this->openPendingOrder($engineType, $request, $account, $symbol, $quoteDto, $metric);
    }

    private function validatePrice(OpenPendingOrderRequest $request, Symbol $symbol, QuoteDto $quoteDto) : ?OpenPendingOrderResult
    {
        $openPrice    = round($request->price, $symbol->precision);
        $currentPrice = round($request->direction->isBuy() ? $quoteDto->bid : $quoteDto->ask, $symbol->precision);

        if ($openPrice === $currentPrice) {
            return $this->output->priceEqualCurrentPrice();
        }

        return null;
    }

    private function validateStopLoss(OpenPendingOrderRequest $request, Symbol $symbol) : ?OpenPendingOrderResult
    {
        $stopLoss = round($request->stopLoss, $symbol->precision);
        $price    = round($request->price, $symbol->precision);

        if ($request->direction->isBuy()) {
            if ($stopLoss > $price) {
                return $this->output->stopLossHigherThanPrice();
            }

            return null;
        }

        if ($stopLoss < $price) {
            return $this->output->stopLossLowerThanPrice();
        }

        return null;
    }

    private function validateTakeProfit(OpenPendingOrderRequest $request, Symbol $symbol) : ?OpenPendingOrderResult
    {
        $takeProfit = round($request->takeProfit, $symbol->precision);
        $price      = round($request->price, $symbol->precision);

        if ($request->direction->isBuy()) {
            if ($takeProfit < $price) {
                return $this->output->takeProfitLowerThanPrice();
            }

            return null;
        }

        if ($takeProfit > $price) {
            return $this->output->takeProfitHigherThanPrice();
        }

        return null;
    }

    private function validateLots(float $lots, Symbol $symbol) : ?OpenPendingOrderResult
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

    private function openPendingOrder(
        SettingModel $engineType,
        OpenPendingOrderRequest $request,
        AccountDto $account,
        Symbol $symbol,
        QuoteDto $quoteDto,
        MetricDto $metric,
    ) : OpenPendingOrderResult {
        return match ($engineType->value()) {
            EngineType::MT->value                  => $this->openMtPendingOrder($request, $account, $symbol, $quoteDto, $metric),
            EngineType::NATIVE_TRADE_ENGINE->value => $this->openNtPendingOrder($request, $account, $symbol, $metric),
            default                                => $this->output->engineTypeNotFound(),
        };
    }

    private function openMtPendingOrder(
        OpenPendingOrderRequest $request,
        AccountDto $account,
        Symbol $symbol,
        QuoteDto $quoteDto,
        MetricDto $metric,
    ) : OpenPendingOrderResult {
        $orderId = $this->pendingOrderRepository->getNewId();

        $metric->sendingOnMt();

        $result = $this->metatraderAdapter->openPendingOrder(
            $orderId,
            $account->login,
            $symbol->name,
            $request->price,
            $quoteDto->ask,
            $quoteDto->bid,
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

        $this->metricRepository->saveOpeningOrder($orderId, $metric);

        return $this->output->opened($orderId, $result->price);
    }

    private function openNtPendingOrder(
        OpenPendingOrderRequest $request,
        AccountDto $account,
        Symbol $symbol,
        MetricDto $metric,
    ) : OpenPendingOrderResult {
        $orderId = $this->pendingOrderRepository->getNewId();

        $metric->sendingOnNt();

        $order = $this->nativeTradeEngineAdapter
            ->openPendingOrder(
                $orderId,
                $account->login,
                $symbol->id,
                $request->price,
                $request->lots,
                $symbol->precision,
                $request->direction->get(),
                $request->stopLoss,
                $request->takeProfit,
                $request->requestedFromClient,
            );

        $metric->answeringFromNt();

        if ($order->resultCode->isError()) {
            return $this->output->cannotOpenOnNt($order->resultCode);
        }

        $result = $this->pendingOrderOpeningService
            ->process(
                new ProcessPendingOrderOpeningRequest(
                    $orderId,
                    $order->volumeInitialExt,
                    $order->orderTrigger,
                    $order->orderPrice,
                    $order->priceSl,
                    $order->priceTp,
                    PendingOrderType::from($order->type),
                    DateTime::createFromMtTimestampMs($order->timestampMs),
                    $symbol,
                    $account,
                    $metric,
                ),
            );

        if ( ! $result->order) {
            $this->output->canNotProcessOrder();
        }

        return $this->output->opened($orderId, $request->price);
    }
}
