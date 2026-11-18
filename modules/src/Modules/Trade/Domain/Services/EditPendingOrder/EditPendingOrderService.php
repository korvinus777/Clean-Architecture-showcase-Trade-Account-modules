<?php

declare(strict_types=1);

namespace Platform\Trade\Domain\Services\EditPendingOrder;

use Platform\Trade\Domain\Dto\QuoteDto;
use Platform\Package\Models\SettingModel;
use Platform\Trade\Domain\Dto\AccountDto;
use Platform\Trade\Domain\Entities\Symbol;
use Platform\Package\Components\Collection\Arr;
use Platform\Trade\Domain\Entities\PendingOrder;
use Platform\Trade\Domain\Adapters\SymbolAdapter;
use Platform\Trade\Domain\Adapters\AccountAdapter;
use Platform\Package\Components\Log\Contracts\Logger;
use Platform\Trade\Domain\Adapters\MetatraderAdapter;
use Platform\Trade\Domain\ValueObjects\PendingOrderType;
use Platform\Package\Components\Collection\ObjectChanger;
use Platform\Trade\Domain\Adapters\NativeTradeEngineAdapter;
use Platform\Trade\Domain\Repositories\PendingOrderRepository;
use Platform\Package\Components\Database\Contracts\Transaction;
use Platform\Package\Components\System\ValueObjects\EngineType;
use Platform\Package\Components\System\Contracts\SettingRepository;
use Platform\Package\Components\System\Services\SettingResolver\Params;

class EditPendingOrderService implements EditPendingOrderInput
{
    public function __construct(
        private AccountAdapter           $accountAdapter,
        private PendingOrderRepository   $pendingOrderRepository,
        private EditPendingOrderOutput   $output,
        private SettingRepository        $settingRepository,
        private SymbolAdapter            $symbolAdapter,
        private MetatraderAdapter        $metatraderAdapter,
        private NativeTradeEngineAdapter $nativeTradeEngineAdapter,
        private Transaction              $transaction,
        private Logger                   $logger,
    ) {
    }

    public function edit(EditPendingOrderRequest $request) : EditPendingOrderResult
    {
        $this->logger->withContext([
            'module'   => 'trade',
            'service'  => class_basename(self::class),
            'orderId'  => $request->orderId,
        ]);

        $pendingOrder = $this->pendingOrderRepository->find($request->orderId);

        if ( ! $pendingOrder || $pendingOrder->accountId !== $request->accountId) {
            return $this->output->pendingOrderNotFound();
        }

        if ( ! $pendingOrder->status->isPending()) {
            return $this->output->pendingOrderNotInPendingStatus();
        }

        $account = $this->accountAdapter->findAccount($request->accountId);

        if ( ! $account) {
            return $this->output->accountNotFound();
        }

        if ( ! ($account->isEnabled && $account->isTradable)) {
            return $this->output->accountNotTradable();
        }

        $symbol = $this->symbolAdapter->findForAccount($pendingOrder->symbolId, $account->id);

        if ( ! $symbol) {
            return $this->output->symbolUnavailable();
        }

        $quote = $this->symbolAdapter->findQuote($pendingOrder->symbolId, $request->accountId);

        if ( ! $quote) {
            return $this->output->quoteNotFound();
        }

        if ($request->price && $output = $this->validateOrderOpenPrice($request, $pendingOrder, $symbol, $quote)) {
            return $output;
        }

        if ($request->stopLoss && $output = $this->validateStopLoss($request, $pendingOrder, $symbol)) {
            return $output;
        }

        if ($request->takeProfit && $output = $this->validateTakeProfit($request, $pendingOrder, $symbol)) {
            return $output;
        }

        $engineType = $this->settingRepository->findOne('Engine Type', new Params());

        if ( ! $engineType) {
            return $this->output->engineTypeNotFound();
        }

        return $this->editPendingOrder($engineType, $request, $account, $pendingOrder);
    }

    private function validateOrderOpenPrice(EditPendingOrderRequest $request, PendingOrder $pendingOrder, Symbol $symbol, QuoteDto $quote) : ?EditPendingOrderResult
    {
        $currentPrice   = round($pendingOrder->type->isBuy() ? $quote->bid : $quote->ask, $symbol->precision);
        $priceOrder     = round($request->price, $symbol->precision);
        $stopLimitPrice = round($pendingOrder->stopLimitPrice, $symbol->precision);

        if ($priceOrder === $currentPrice) {
            return $this->output->priceEqualCurrentPrice();
        }

        if ($pendingOrder->type === PendingOrderType::BUY_LIMIT && $priceOrder > $currentPrice) {
            return $this->output->priceHigherThenCurrentPrice();
        }

        if ($pendingOrder->type === PendingOrderType::SELL_LIMIT && $priceOrder < $currentPrice) {
            return $this->output->priceLowerThenCurrentPrice();
        }

        if ($pendingOrder->type === PendingOrderType::BUY_STOP && $priceOrder < $currentPrice) {
            return $this->output->priceLowerThenCurrentPrice();
        }

        if ($pendingOrder->type === PendingOrderType::SELL_STOP && $priceOrder > $currentPrice) {
            return $this->output->priceHigherThenCurrentPrice();
        }

        if ($pendingOrder->type === PendingOrderType::BUY_STOP_LIMIT) {
            if ($priceOrder < $currentPrice) {
                return $this->output->priceLowerThenCurrentPrice();
            }

            if ($stopLimitPrice > $priceOrder) {
                return $this->output->stopLimitPriceHigherThenPrice();
            }
        }

        if ($pendingOrder->type === PendingOrderType::SELL_STOP_LIMIT) {
            if ($priceOrder > $currentPrice) {
                return $this->output->priceHigherThenCurrentPrice();
            }

            if ($stopLimitPrice < $priceOrder) {
                return $this->output->stopLimitPriceLowerThenPrice();
            }
        }

        return null;
    }

    private function validateStopLoss(EditPendingOrderRequest $request, PendingOrder $pendingOrder, Symbol $symbol) : ?EditPendingOrderResult
    {
        if ($request->stopLoss === $pendingOrder->stopLoss) {
            return null;
        }

        $priceOrder = round($request->price ?: $pendingOrder->price, $symbol->precision);
        $stopLoss   = round($request->stopLoss, $symbol->precision);

        if ($pendingOrder->type->isBuy() && $stopLoss > $priceOrder) {
            return $this->output->stopLossHigherThanPrice();
        }

        if ($pendingOrder->type->isSell() && $stopLoss < $priceOrder) {
            return $this->output->stopLossLowerThanPrice();
        }

        return null;
    }

    private function validateTakeProfit(EditPendingOrderRequest $request, PendingOrder $pendingOrder, Symbol $symbol) : ?EditPendingOrderResult
    {
        if ($request->takeProfit === $pendingOrder->takeProfit) {
            return null;
        }

        $priceOrder = round($request->price ?: $pendingOrder->price, $symbol->precision);
        $takeProfit = round($request->takeProfit, $symbol->precision);

        if ($pendingOrder->type->isBuy() && $takeProfit < $priceOrder) {
            return $this->output->takeProfitLowerThanPrice();
        }

        if ($pendingOrder->type->isSell() && $takeProfit > $priceOrder) {
            return $this->output->takeProfitHigherThanPrice();
        }

        return null;
    }

    private function editPendingOrder(
        SettingModel $engineType,
        EditPendingOrderRequest $request,
        AccountDto $account,
        PendingOrder $pendingOrder,
    ) : EditPendingOrderResult {
        return match ($engineType->value()) {
            EngineType::MT->value                  => $this->editMtPendingOrder($request, $account, $pendingOrder),
            EngineType::NATIVE_TRADE_ENGINE->value => $this->editNtPendingOrder($request, $account, $pendingOrder),
            default                                => $this->output->engineTypeNotFound(),
        };
    }

    private function editMtPendingOrder(
        EditPendingOrderRequest $request,
        AccountDto $account,
        PendingOrder $pendingOrder,
    ) : EditPendingOrderResult {
        $resultCode = $this->metatraderAdapter->editPendingOrder(
            $pendingOrder->id,
            $account->login,
            $pendingOrder->type,
            $pendingOrder->symbolId,
            is_null($request->price) ? $pendingOrder->price : $request->price,
            is_null($request->stopLoss) ? $pendingOrder->stopLoss : $request->stopLoss,
            is_null($request->takeProfit) ? $pendingOrder->takeProfit : $request->takeProfit,
        );

        if ($resultCode->isError()) {
            return $this->output->cannotEditOnMt($resultCode);
        }

        return $this->changePendingOrder($request, $pendingOrder);
    }

    private function editNtPendingOrder(
        EditPendingOrderRequest $request,
        AccountDto $account,
        PendingOrder $pendingOrder,
    ) : EditPendingOrderResult {
        $result = $this->nativeTradeEngineAdapter->modifyPendingOrder(
            $pendingOrder->id,
            $account->login,
            $pendingOrder->symbolId,
            is_null($request->price) ? $pendingOrder->price : $request->price,
            is_null($request->stopLoss) ? $pendingOrder->stopLoss : $request->stopLoss,
            is_null($request->takeProfit) ? $pendingOrder->takeProfit : $request->takeProfit,
        );

        if ($result->resultCode->isError()) {
            return $this->output->cannotEditOnNt($result->resultCode);
        }

        return $this->changePendingOrder($request, $pendingOrder);
    }

    private function changePendingOrder(EditPendingOrderRequest $request, PendingOrder $pendingOrder) : EditPendingOrderResult
    {
        $changer = (new ObjectChanger($pendingOrder))
            /** @uses PendingOrder::$price */
            ->changeNotNull('price', $request->price)
            /** @uses PendingOrder::$stopLoss */
            ->changeNotNull('stopLoss', $request->stopLoss)
            /** @uses PendingOrder::$takeProfit */
            ->changeNotNull('takeProfit', $request->takeProfit);

        try {
            $this->transaction->forMain(function () use ($pendingOrder, $changer) : void {
                if ($changer->getChanges()) {
                    $this->pendingOrderRepository->save($pendingOrder);

                    $this->pendingOrderRepository->saveChanges($pendingOrder->id, $changer->getChanges(), false);
                }
            });

            return $this->output->edited();
        } catch (\Throwable $throwable) {
            $this->logger->critical('unexpected error', [
                'error'      => Arr::formatThrowable($throwable),
                'orderId'    => $pendingOrder->id,
                'priceOrder' => $request->price,
                'stopLoss'   => $request->stopLoss,
                'takeProfit' => $request->takeProfit,
            ]);

            return $this->output->savingFailed();
        }
    }
}
