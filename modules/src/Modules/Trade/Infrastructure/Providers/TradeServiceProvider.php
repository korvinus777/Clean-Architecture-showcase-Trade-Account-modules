<?php

declare(strict_types=1);

namespace Platform\Trade\Infrastructure\Providers;

use Platform\Trade\Api\Api;
use Illuminate\Events\Dispatcher;
use Platform\Trade\Api\ApiService;
use Platform\Package\Providers\ModuleServiceProvider;
use Platform\Trade\App\Services\EditTrade\EditTradeInput;
use Platform\Trade\App\Services\EditTrade\EditTradeOutput;
use Platform\Trade\App\Services\EditTrade\EditTradeService;
use Platform\Trade\Domain\Services\OpenTrade\OpenTradeInput;
use Platform\Trade\App\Outputs\EditMtTrade\EditMtTradeOutput;
use Platform\Trade\App\Outputs\EditTrade\EditTradeJsonOutput;
use Platform\Trade\App\Outputs\OpenMtTrade\OpenMtTradeOutput;
use Platform\Trade\App\Outputs\OpenTrade\OpenTradeJsonOutput;
use Platform\Trade\App\Outputs\ReopenTrade\ReopenTradeOutput;
use Platform\Trade\App\Services\EditMtTrade\EditMtTradeInput;
use Platform\Trade\App\Services\OpenMtTrade\OpenMtTradeInput;
use Platform\Trade\Domain\Services\OpenTrade\OpenTradeOutput;
use Platform\Trade\Domain\Services\CloseTrade\CloseTradeInput;
use Platform\Trade\Domain\Services\OpenTrade\OpenTradeService;
use Platform\Trade\App\Outputs\CloseMtTrade\CloseMtTradeOutput;
use Platform\Trade\App\Outputs\CloseTrade\CloseTradeJsonOutput;
use Platform\Trade\App\Services\CloseMtTrade\CloseMtTradeInput;
use Platform\Trade\App\Services\EditMtTrade\EditMtTradeService;
use Platform\Trade\App\Services\OpenMtTrade\OpenMtTradeService;
use Platform\Trade\Domain\Services\CloseTrade\CloseTradeOutput;
use Platform\Trade\Infrastructure\Repositories\TradeRepository;
use Platform\Trade\Domain\Services\CloseTrade\CloseTradeService;
use Platform\Trade\Domain\Services\ReopenTrade\ReopenTradeInput;
use Platform\Trade\Infrastructure\Adapters\Symbol\SymbolAdapter;
use Platform\Trade\Infrastructure\Listeners\TradeClosedListener;
use Platform\Trade\Infrastructure\Listeners\TradeEditedListener;
use Platform\Trade\Infrastructure\Listeners\TradeOpenedListener;
use Platform\Trade\Infrastructure\Repositories\MetricRepository;
use Platform\Trade\App\Services\CloseMtTrade\CloseMtTradeService;
use Platform\Trade\App\Services\ReopenMtTrade\ReopenMtTradeInput;
use Platform\Trade\Infrastructure\Components\NotificationChannel;
use Platform\Trade\Domain\Services\ReopenTrade\ReopenTradeService;
use Platform\Trade\Infrastructure\Adapters\Account\AccountAdapter;
use Platform\Trade\Infrastructure\Listeners\TradeReopenedListener;
use Platform\Trade\App\Outputs\CrmReopenTrade\CrmReopenTradeOutput;
use Platform\Trade\App\Services\ReopenMtTrade\ReopenMtTradeService;
use Platform\Trade\Domain\Adapters\SymbolAdapter as ISymbolAdapter;
use Platform\Trade\Infrastructure\Adapters\Currency\CurrencyAdapter;
use Platform\Trade\App\Outputs\ActivateMtOrder\ActivateMtOrderOutput;
use Platform\Trade\App\Services\ActivateMtOrder\ActivateMtOrderInput;
use Platform\Trade\Domain\Adapters\AccountAdapter as IAccountAdapter;
use Platform\Trade\Infrastructure\Repositories\ClosedTradeRepository;
use Platform\Trade\Domain\Services\CrmReopenTrade\CrmReopenTradeInput;
use Platform\Trade\Infrastructure\Repositories\PendingOrderRepository;
use Platform\Trade\App\Outputs\CloseNativeTrade\CloseNativeTradeOutput;
use Platform\Trade\App\Services\ActivateMtOrder\ActivateMtOrderService;
use Platform\Trade\App\Services\CloseNativeTrade\CloseNativeTradeInput;
use Platform\Trade\Domain\Adapters\CurrencyAdapter as ICurrencyAdapter;
use Platform\Trade\Domain\Services\CrmReopenTrade\CrmReopenTradeService;
use Platform\Trade\Infrastructure\Adapters\Metatrader\MetatraderAdapter;
use Platform\Trade\App\Services\CloseNativeTrade\CloseNativeTradeService;
use Platform\Trade\App\Services\DeleteOpenedTrade\DeleteOpenedTradeInput;
use Platform\Trade\Domain\Services\EditPendingOrder\EditPendingOrderInput;
use Platform\Trade\Domain\Services\OpenPendingOrder\OpenPendingOrderInput;
use Platform\Trade\Infrastructure\Adapters\Transaction\TransactionAdapter;
use Platform\Trade\App\Outputs\EditMtPendingOrder\EditMtPendingOrderOutput;
use Platform\Trade\App\Outputs\EditPendingOrder\EditPendingOrderJsonOutput;
use Platform\Trade\App\Outputs\OpenMtPendingOrder\OpenMtPendingOrderOutput;
use Platform\Trade\App\Outputs\OpenPendingOrder\OpenPendingOrderJsonOutput;
use Platform\Trade\App\Services\CloseAccountTrades\CloseAccountTradesInput;
use Platform\Trade\App\Services\DeleteOpenedTrade\DeleteOpenedTradeService;
use Platform\Trade\App\Services\EditMtPendingOrder\EditMtPendingOrderInput;
use Platform\Trade\App\Services\OpenMtPendingOrder\OpenMtPendingOrderInput;
use Platform\Trade\Domain\Adapters\MetatraderAdapter as IMetatraderAdapter;
use Platform\Trade\Domain\Repositories\TradeRepository as ITradeRepository;
use Platform\Trade\App\Services\CloseAccountTrades\CloseAccountTradesOutput;
use Platform\Trade\Domain\Services\ClosePendingOrder\ClosePendingOrderInput;
use Platform\Trade\Domain\Services\EditPendingOrder\EditPendingOrderService;
use Platform\Trade\Domain\Services\OpenPendingOrder\OpenPendingOrderService;
use Platform\Trade\App\Outputs\CloseMtPendingOrder\CloseMtPendingOrderOutput;
use Platform\Trade\App\Outputs\ClosePendingOrder\ClosePendingOrderJsonOutput;
use Platform\Trade\App\Outputs\ProcessTradeClosing\ProcessTradeClosingOutput;
use Platform\Trade\App\Outputs\ProcessTradeEditing\ProcessTradeEditingOutput;
use Platform\Trade\App\Outputs\ProcessTradeOpening\ProcessTradeOpeningOutput;
use Platform\Trade\App\Services\CloseAccountTrades\CloseAccountTradesService;
use Platform\Trade\App\Services\CloseMtPendingOrder\CloseMtPendingOrderInput;
use Platform\Trade\App\Services\EditMtPendingOrder\EditMtPendingOrderService;
use Platform\Trade\App\Services\OpenMtPendingOrder\OpenMtPendingOrderService;
use Platform\Trade\Domain\Adapters\TransactionAdapter as ITransactionAdapter;
use Platform\Trade\Domain\Repositories\MetricRepository as IMetricRepository;
use Platform\Trade\Domain\Services\ClosePendingOrder\ClosePendingOrderService;
use Platform\Trade\App\Outputs\CloseAccountTrades\CloseAccountTradesJsonOutput;
use Platform\Trade\App\Outputs\DeleteOpenedTradeOutput\DeleteOpenedTradeOutput;
use Platform\Trade\App\Services\CloseMtPendingOrder\CloseMtPendingOrderService;
use Platform\Trade\Domain\Services\ProcessTradeClosing\ProcessTradeClosingInput;
use Platform\Trade\Domain\Services\ProcessTradeEditing\ProcessTradeEditingInput;
use Platform\Trade\Domain\Services\ProcessTradeOpening\ProcessTradeOpeningInput;
use Platform\Trade\Domain\Components\NotificationChannel as INotificationChannel;
use Platform\Trade\Domain\Services\ProcessTradeClosing\ProcessTradeClosingService;
use Platform\Trade\Domain\Services\ProcessTradeEditing\ProcessTradeEditingService;
use Platform\Trade\Domain\Services\ProcessTradeOpening\ProcessTradeOpeningService;
use Platform\Trade\App\Services\EditMtTrade\EditMtTradeOutput as IEditMtTradeOutput;
use Platform\Trade\App\Services\OpenMtTrade\OpenMtTradeOutput as IOpenMtTradeOutput;
use Platform\Trade\Infrastructure\Adapters\AccountStatistics\AccountStatisticsAdapter;
use Platform\Trade\Infrastructure\Adapters\NativeTradeEngine\NativeTradeEngineAdapter;
use Platform\Trade\App\Services\CloseMtTrade\CloseMtTradeOutput as ICloseMtTradeOutput;
use Platform\Trade\Domain\Repositories\ClosedTradeRepository as IClosedTradeRepository;
use Platform\Trade\Domain\Services\ReopenTrade\ReopenTradeOutput as IReopenTradeOutput;
use Platform\Trade\App\Services\CloseAccountPendingOrders\CloseAccountPendingOrdersInput;
use Platform\Trade\Domain\Adapters\AccountStatisticsAdapter as IAccountStatisticsAdapter;
use Platform\Trade\Domain\Adapters\NativeTradeEngineAdapter as INativeTradeEngineAdapter;
use Platform\Trade\Domain\Repositories\PendingOrderRepository as IPendingOrderRepository;
use Platform\Trade\App\Services\CloseAccountPendingOrders\CloseAccountPendingOrdersOutput;
use Platform\Trade\App\Outputs\ProcessPendingOrderClosing\ProcessPendingOrderClosingOutput;
use Platform\Trade\App\Outputs\ProcessPendingOrderEditing\ProcessPendingOrderEditingOutput;
use Platform\Trade\App\Outputs\ProcessPendingOrderOpening\ProcessPendingOrderOpeningOutput;
use Platform\Trade\App\Services\CloseAccountPendingOrders\CloseAccountPendingOrdersService;
use Platform\Trade\App\Outputs\CloseAccountPendingOrders\CloseAccountPendingOrdersJsonOutput;
use Platform\Trade\Domain\Services\ProcessPendingOrderClosing\ProcessPendingOrderClosingInput;
use Platform\Trade\Domain\Services\ProcessPendingOrderEditing\ProcessPendingOrderEditingInput;
use Platform\Trade\Domain\Services\ProcessPendingOrderOpening\ProcessPendingOrderOpeningInput;
use Platform\Trade\App\Services\ActivateMtOrder\ActivateMtOrderOutput as IActivateMtOrderOutput;
use Platform\Trade\Domain\Services\CrmReopenTrade\CrmReopenTradeOutput as ICrmReopenTradeOutput;
use Platform\Trade\Domain\Services\ProcessPendingOrderClosing\ProcessPendingOrderClosingService;
use Platform\Trade\Domain\Services\ProcessPendingOrderEditing\ProcessPendingOrderEditingService;
use Platform\Trade\Domain\Services\ProcessPendingOrderOpening\ProcessPendingOrderOpeningService;
use Platform\Trade\App\Services\CloseNativeTrade\CloseNativeTradeOutput as ICloseNativeTradeOutput;
use Platform\Trade\App\Services\DeleteOpenedTrade\DeleteOpenedTradeOutput as IDeleteOpenedTradeOutput;
use Platform\Trade\Domain\Services\EditPendingOrder\EditPendingOrderOutput as IEditPendingOrderOutput;
use Platform\Trade\Domain\Services\OpenPendingOrder\OpenPendingOrderOutput as IOpenPendingOrderOutput;
use Platform\Trade\App\Services\EditMtPendingOrder\EditMtPendingOrderOutput as IEditMtPendingOrderOutput;
use Platform\Trade\App\Services\OpenMtPendingOrder\OpenMtPendingOrderOutput as IOpenMtPendingOrderOutput;
use Platform\Trade\Domain\Services\ClosePendingOrder\ClosePendingOrderOutput as IClosePendingOrderOutput;
use Platform\Trade\App\Services\CloseMtPendingOrder\CloseMtPendingOrderOutput as ICloseMtPendingOrderOutput;
use Platform\Trade\Domain\Services\ProcessTradeClosing\ProcessTradeClosingOutput as IProcessTradeClosingOutput;
use Platform\Trade\Domain\Services\ProcessTradeEditing\ProcessTradeEditingOutput as IProcessTradeEditingOutput;
use Platform\Trade\Domain\Services\ProcessTradeOpening\ProcessTradeOpeningOutput as IProcessTradeOpeningOutput;
use Platform\Trade\Domain\Services\ProcessPendingOrderClosing\ProcessPendingOrderClosingOutput as IProcessPendingOrderClosingOutput;
use Platform\Trade\Domain\Services\ProcessPendingOrderEditing\ProcessPendingOrderEditingOutput as IProcessPendingOrderEditingOutput;
use Platform\Trade\Domain\Services\ProcessPendingOrderOpening\ProcessPendingOrderOpeningOutput as IProcessPendingOrderOpeningOutput;

class TradeServiceProvider extends ModuleServiceProvider
{
    public array $bindings = [
        Api::class => ApiService::class,

        INotificationChannel::class => NotificationChannel::class,

        IAccountAdapter::class           => AccountAdapter::class,
        IMetatraderAdapter::class        => MetatraderAdapter::class,
        ISymbolAdapter::class            => SymbolAdapter::class,
        ITransactionAdapter::class       => TransactionAdapter::class,
        ICurrencyAdapter::class             => CurrencyAdapter::class,
        IAccountStatisticsAdapter::class    => AccountStatisticsAdapter::class,
        INativeTradeEngineAdapter::class    => NativeTradeEngineAdapter::class,

        ITradeRepository::class        => TradeRepository::class,
        IMetricRepository::class       => MetricRepository::class,
        IPendingOrderRepository::class => PendingOrderRepository::class,
        IClosedTradeRepository::class  => ClosedTradeRepository::class,

        OpenTradeInput::class  => OpenTradeService::class,
        OpenTradeOutput::class => OpenTradeJsonOutput::class,

        OpenMtTradeInput::class   => OpenMtTradeService::class,
        IOpenMtTradeOutput::class => OpenMtTradeOutput::class,

        OpenMtPendingOrderInput::class   => OpenMtPendingOrderService::class,
        IOpenMtPendingOrderOutput::class => OpenMtPendingOrderOutput::class,

        ProcessTradeOpeningInput::class   => ProcessTradeOpeningService::class,
        IProcessTradeOpeningOutput::class => ProcessTradeOpeningOutput::class,

        ProcessPendingOrderOpeningInput::class   => ProcessPendingOrderOpeningService::class,
        IProcessPendingOrderOpeningOutput::class => ProcessPendingOrderOpeningOutput::class,

        OpenPendingOrderInput::class   => OpenPendingOrderService::class,
        IOpenPendingOrderOutput::class => OpenPendingOrderJsonOutput::class,

        CloseTradeInput::class  => CloseTradeService::class,
        CloseTradeOutput::class => CloseTradeJsonOutput::class,

        EditTradeInput::class  => EditTradeService::class,
        EditTradeOutput::class => EditTradeJsonOutput::class,

        ProcessTradeClosingInput::class   => ProcessTradeClosingService::class,
        IProcessTradeClosingOutput::class => ProcessTradeClosingOutput::class,

        CloseMtTradeInput::class   => CloseMtTradeService::class,
        ICloseMtTradeOutput::class => CloseMtTradeOutput::class,

        ProcessTradeEditingInput::class   => ProcessTradeEditingService::class,
        IProcessTradeEditingOutput::class => ProcessTradeEditingOutput::class,

        EditMtTradeInput::class   => EditMtTradeService::class,
        IEditMtTradeOutput::class => EditMtTradeOutput::class,

        ClosePendingOrderInput::class   => ClosePendingOrderService::class,
        IClosePendingOrderOutput::class => ClosePendingOrderJsonOutput::class,

        ActivateMtOrderInput::class   => ActivateMtOrderService::class,
        IActivateMtOrderOutput::class => ActivateMtOrderOutput::class,

        EditPendingOrderInput::class   => EditPendingOrderService::class,
        IEditPendingOrderOutput::class => EditPendingOrderJsonOutput::class,

        CloseMtPendingOrderInput::class   => CloseMtPendingOrderService::class,
        ICloseMtPendingOrderOutput::class => CloseMtPendingOrderOutput::class,

        ProcessPendingOrderClosingInput::class   => ProcessPendingOrderClosingService::class,
        IProcessPendingOrderClosingOutput::class => ProcessPendingOrderClosingOutput::class,

        EditMtPendingOrderInput::class   => EditMtPendingOrderService::class,
        IEditMtPendingOrderOutput::class => EditMtPendingOrderOutput::class,

        ProcessPendingOrderEditingInput::class   => ProcessPendingOrderEditingService::class,
        IProcessPendingOrderEditingOutput::class => ProcessPendingOrderEditingOutput::class,

        ReopenTradeInput::class   => ReopenTradeService::class,
        IReopenTradeOutput::class => ReopenTradeOutput::class,

        ReopenMtTradeInput::class => ReopenMtTradeService::class,

        CloseAccountTradesInput::class  => CloseAccountTradesService::class,
        CloseAccountTradesOutput::class => CloseAccountTradesJsonOutput::class,

        CloseAccountPendingOrdersInput::class  => CloseAccountPendingOrdersService::class,
        CloseAccountPendingOrdersOutput::class => CloseAccountPendingOrdersJsonOutput::class,

        DeleteOpenedTradeInput::class   => DeleteOpenedTradeService::class,
        IDeleteOpenedTradeOutput::class => DeleteOpenedTradeOutput::class,

        CrmReopenTradeInput::class => CrmReopenTradeService::class,
        ICrmReopenTradeOutput::class => CrmReopenTradeOutput::class,

        CloseNativeTradeInput::class => CloseNativeTradeService::class,
        ICloseNativeTradeOutput::class => CloseNativeTradeOutput::class,
    ];

    public function listen(Dispatcher $dispatcher) : void
    {
        $dispatcher->listen('trade.opened', TradeOpenedListener::class);
        $dispatcher->listen('trade.edited', TradeEditedListener::class);
        $dispatcher->listen(['trade.closed', 'trade.partial.closed'], TradeClosedListener::class);
        $dispatcher->listen('trade.reopened', TradeReopenedListener::class);
    }
}
