<?php

declare(strict_types=1);

namespace Platform\Trade\Domain\Services\ClosePendingOrder;

use Platform\Package\Models\SettingModel;
use Platform\Trade\Domain\Dto\AccountDto;
use Platform\Trade\Domain\Entities\Symbol;
use Platform\Package\Components\Time\DateTime;
use Platform\Trade\Domain\Entities\PendingOrder;
use Platform\Trade\Domain\Adapters\SymbolAdapter;
use Platform\Trade\Domain\Adapters\AccountAdapter;
use Platform\Trade\Domain\Adapters\MetatraderAdapter;
use Platform\Trade\Domain\ValueObjects\PendingOrderStatus;
use Platform\Trade\Domain\Adapters\NativeTradeEngineAdapter;
use Platform\Trade\Domain\Repositories\PendingOrderRepository;
use Platform\Package\Components\System\ValueObjects\EngineType;
use Platform\Package\Components\System\Contracts\SettingRepository;
use Platform\Package\Components\System\Services\SettingResolver\Params;
use Platform\Trade\Domain\Services\ProcessPendingOrderClosing\ProcessPendingOrderClosingInput;
use Platform\Trade\Domain\Services\ProcessPendingOrderClosing\ProcessPendingOrderClosingRequest;

class ClosePendingOrderService implements ClosePendingOrderInput
{
    public function __construct(
        private SymbolAdapter                  $symbolAdapter,
        private AccountAdapter                 $accountAdapter,
        private MetatraderAdapter              $metatraderAdapter,
        private NativeTradeEngineAdapter       $nativeTradeEngineAdapter,
        private PendingOrderRepository         $pendingOrderRepository,
        private SettingRepository              $settingRepository,
        private ClosePendingOrderOutput        $output,
        private ProcessPendingOrderClosingInput $processPendingOrderClosingService,
    ) {
    }

    public function close(ClosePendingOrderRequest $request) : ClosePendingOrderResult
    {
        $account = $this->accountAdapter->findAccount($request->accountId);

        if ( ! $account) {
            return $this->output->accountNotFound();
        }

        if ( ! ($account->isEnabled && $account->isTradable)) {
            return $this->output->accountNotTradable();
        }

        $pendingOrder = $this->pendingOrderRepository->find($request->pendingOrderId);

        if ( ! $pendingOrder || $pendingOrder->accountId !== $request->accountId) {
            return $this->output->pendingOrderNotFound();
        }

        if ($pendingOrder->status !== PendingOrderStatus::PENDING) {
            return $this->output->pendingOrderNotInPendingStatus();
        }

        $symbol = $this->symbolAdapter->findForAccount($pendingOrder->symbolId, $account->id);

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
            return $this->output->engineTypeNotFound();
        }

        return $this->closePendingOrder($engineType, $request, $account, $symbol, $pendingOrder);
    }

    private function closePendingOrder(
        SettingModel $engineType,
        ClosePendingOrderRequest $request,
        AccountDto $account,
        Symbol $symbol,
        PendingOrder $pendingOrder,
    ) : ClosePendingOrderResult {
        return match ($engineType->value()) {
            EngineType::MT->value                  => $this->closeMtPendingOrder($request, $account, $symbol, $pendingOrder),
            EngineType::NATIVE_TRADE_ENGINE->value => $this->closeNtPendingOrder($account, $pendingOrder),
            default                                => $this->output->engineTypeNotFound(),
        };
    }

    private function closeMtPendingOrder(
        ClosePendingOrderRequest $request,
        AccountDto $account,
        Symbol $symbol,
        PendingOrder $pendingOrder,
    ) : ClosePendingOrderResult {
        if ($request->deferred) {
            $this->metatraderAdapter->deferredClosePendingOrder(
                $account->login,
                $symbol->name,
                $pendingOrder->id,
                $pendingOrder->type,
            );

            return $this->output->asyncClosing();
        }

        $resultCode = $this->metatraderAdapter->closePendingOrder(
            $account->login,
            $symbol->name,
            $pendingOrder->id,
            $pendingOrder->type,
        );

        if ($resultCode->isError()) {
            return $this->output->cannotCloseOnMt($resultCode);
        }

        return $this->output->closed($pendingOrder->id);
    }

    private function closeNtPendingOrder(AccountDto $account, PendingOrder $pendingOrder) : ClosePendingOrderResult
    {
        $order = $this->nativeTradeEngineAdapter->closePendingOrder(
            $pendingOrder->id,
            $account->login,
        );

        if ($order->resultCode->isError()) {
            return $this->output->cannotCloseOnNt($order->resultCode);
        }

        $this->processPendingOrderClosingService->process(
            new ProcessPendingOrderClosingRequest($pendingOrder, DateTime::now()),
        );

        return $this->output->closed($pendingOrder->id);
    }
}
