<?php

declare(strict_types=1);

namespace Platform\Account\Infrastructure\Providers;

use Platform\Account\Api\Api;
use Illuminate\Events\Dispatcher;
use Platform\Account\Api\ApiService;
use Illuminate\Console\Scheduling\Schedule;
use Platform\Package\Providers\ModuleServiceProvider;
use Platform\Account\Domain\Services\Create\CreateInput;
use Platform\Account\Domain\Services\Delete\DeleteInput;
use Platform\Account\Domain\Services\Update\UpdateInput;
use Platform\Account\App\Outputs\AddGroup\AddGroupOutput;
use Platform\Account\App\Outputs\Create\CreateJsonOutput;
use Platform\Account\App\Outputs\Delete\DeleteJsonOutput;
use Platform\Account\App\Outputs\Update\UpdateJsonOutput;
use Platform\Account\Domain\Services\Create\CreateOutput;
use Platform\Account\Domain\Services\Delete\DeleteOutput;
use Platform\Account\Domain\Services\Update\UpdateOutput;
use Platform\Account\Domain\Services\Create\CreateService;
use Platform\Account\Domain\Services\Delete\DeleteService;
use Platform\Account\Domain\Services\Details\DetailsInput;
use Platform\Account\Domain\Services\Update\UpdateService;
use Platform\Account\App\Outputs\Details\DetailsJsonOutput;
use Platform\Account\Domain\Services\Details\DetailsOutput;
use Platform\Account\Domain\Services\AddGroup\AddGroupInput;
use Platform\Account\Domain\Services\Details\DetailsService;
use Platform\Account\Infrastructure\Adapters\Crm\CrmAdapter;
use Platform\Account\Infrastructure\Commands\SyncMetatrader;
use Platform\Account\Domain\Services\AddGroup\AddGroupService;
use Platform\Account\Infrastructure\Adapters\Lead\LeadAdapter;
use Platform\Account\Domain\Adapters\CrmAdapter as ICrmAdapter;
use Platform\Account\Domain\Services\SetStopOut\SetStopOutInput;
use Platform\Account\Infrastructure\Adapters\Brand\BrandAdapter;
use Platform\Account\Domain\Adapters\LeadAdapter as ILeadAdapter;
use Platform\Account\Infrastructure\Repositories\GroupRepository;
use Platform\Account\Domain\Services\SetStopOut\SetStopOutService;
use Platform\Account\Infrastructure\Listeners\TradeClosedListener;
use Platform\Account\Infrastructure\Listeners\TradeOpenedListener;
use Platform\Account\App\Outputs\SyncMtAccount\SyncMtAccountOutput;
use Platform\Account\App\Services\SyncMtAccount\SyncMtAccountInput;
use Platform\Account\Domain\Adapters\BrandAdapter as IBrandAdapter;
use Platform\Account\Infrastructure\Components\NotificationChannel;
use Platform\Account\Infrastructure\Repositories\AccountRepository;
use Platform\Account\Infrastructure\Repositories\SettingRepository;
use Platform\Account\App\Outputs\ParseGroupName\ParseGroupNameOutput;
use Platform\Account\App\Services\SyncMtAccount\SyncMtAccountService;
use Platform\Account\Infrastructure\Repositories\GroupTypeRepository;
use Platform\Account\Domain\Services\SelectAccount\SelectAccountInput;
use Platform\Account\Domain\Services\SetMarginCall\SetMarginCallInput;
use Platform\Account\Infrastructure\Adapters\Currency\CurrencyAdapter;
use Platform\Account\Infrastructure\Listeners\ToggleBlockAuthListener;
use Platform\Account\App\Outputs\SelectAccount\SelectAccountJsonOutput;
use Platform\Account\App\Outputs\UpdateMtAccount\UpdateMtAccountOutput;
use Platform\Account\App\Services\UpdateMtAccount\UpdateMtAccountInput;
use Platform\Account\Domain\Services\SelectAccount\SelectAccountOutput;
use Platform\Account\Infrastructure\Listeners\ToggleBlockTradeListener;
use Platform\Account\Domain\Services\ParseGroupName\ParseGroupNameInput;
use Platform\Account\Domain\Services\SelectAccount\SelectAccountService;
use Platform\Account\Domain\Services\SetMarginCall\SetMarginCallService;
use Platform\Account\App\Services\UpdateMtAccount\UpdateMtAccountService;
use Platform\Account\Domain\Adapters\CurrencyAdapter as ICurrencyAdapter;
use Platform\Account\Domain\Services\LeadAccountList\LeadAccountListInput;
use Platform\Account\Domain\Services\ParseGroupName\ParseGroupNameService;
use Platform\Account\Infrastructure\Adapters\Metatrader\MetatraderAdapter;
use Platform\Account\App\Outputs\LeadAccountList\LeadAccountListJsonOutput;
use Platform\Account\Domain\Services\LeadAccountList\LeadAccountListOutput;
use Platform\Account\Domain\Services\LeadAccountList\LeadAccountListService;
use Platform\Account\Infrastructure\Adapters\Transaction\TransactionAdapter;
use Platform\Account\Domain\Adapters\MetatraderAdapter as IMetatraderAdapter;
use Platform\Account\Domain\Repositories\GroupRepository as IGroupRepository;
use Platform\Account\Infrastructure\Repositories\AccountStatisticsRepository;
use Platform\Account\Domain\Services\GetWithdrawalInfo\GetWithdrawalInfoInput;
use Platform\Account\App\Outputs\GetWithdrawalInfo\GetWithdrawalInfoJsonOutput;
use Platform\Account\Domain\Adapters\TransactionAdapter as ITransactionAdapter;
use Platform\Account\Domain\Services\GetWithdrawalInfo\GetWithdrawalInfoOutput;
use Platform\Account\Domain\Services\AddGroup\AddGroupOutput as IAddGroupOutput;
use Platform\Account\Domain\Services\GetWithdrawalInfo\GetWithdrawalInfoService;
use Platform\Account\Domain\Repositories\AccountRepository as IAccountRepository;
use Platform\Account\Domain\Repositories\SettingRepository as ISettingRepository;
use Platform\Account\Domain\Services\SyncExternalBalance\SyncExternalBalanceInput;
use Platform\Account\Domain\Components\NotificationChannel as INotificationChannel;
use Platform\Account\Domain\Services\SyncExternalBalance\SyncExternalBalanceService;
use Platform\Account\Domain\Repositories\GroupTypeRepository as IGroupTypeRepository;
use Platform\Account\Infrastructure\Adapters\NativeTradeEngine\NativeTradeEngineAdapter;
use Platform\Account\Domain\Adapters\NativeTradeEngineAdapter as INativeTradeEngineAdapter;
use Platform\Account\App\Services\SyncMtAccount\SyncMtAccountOutput as ISyncMtAccountOutput;
use Platform\Account\App\Services\UpdateMtAccount\UpdateMtAccountOutput as IUpdateMtAccountOutput;
use Platform\Account\Domain\Services\ParseGroupName\ParseGroupNameOutput as IParseGroupNameOutput;
use Platform\Account\Domain\Repositories\AccountStatisticsRepository as IAccountStatisticsRepository;

class AccountServiceProvider extends ModuleServiceProvider
{
    public array $bindings = [
        Api::class => ApiService::class,

        ICurrencyAdapter::class          => CurrencyAdapter::class,
        IMetatraderAdapter::class        => MetatraderAdapter::class,
        ILeadAdapter::class              => LeadAdapter::class,
        IBrandAdapter::class             => BrandAdapter::class,
        ICrmAdapter::class               => CrmAdapter::class,
        ITransactionAdapter::class       => TransactionAdapter::class,
        INativeTradeEngineAdapter::class => NativeTradeEngineAdapter::class,

        IGroupRepository::class     => GroupRepository::class,
        IAccountRepository::class            => AccountRepository::class,
        IAccountStatisticsRepository::class  => AccountStatisticsRepository::class,
        IGroupTypeRepository::class          => GroupTypeRepository::class,
        ISettingRepository::class            => SettingRepository::class,

        CreateInput::class  => CreateService::class,
        CreateOutput::class => CreateJsonOutput::class,

        DeleteInput::class  => DeleteService::class,
        DeleteOutput::class => DeleteJsonOutput::class,

        UpdateInput::class  => UpdateService::class,
        UpdateOutput::class => UpdateJsonOutput::class,

        DetailsInput::class  => DetailsService::class,
        DetailsOutput::class => DetailsJsonOutput::class,

        LeadAccountListInput::class  => LeadAccountListService::class,
        LeadAccountListOutput::class => LeadAccountListJsonOutput::class,

        ParseGroupNameInput::class   => ParseGroupNameService::class,
        IParseGroupNameOutput::class => ParseGroupNameOutput::class,

        AddGroupInput::class   => AddGroupService::class,
        IAddGroupOutput::class => AddGroupOutput::class,

        SyncMtAccountInput::class   => SyncMtAccountService::class,
        ISyncMtAccountOutput::class => SyncMtAccountOutput::class,

        UpdateMtAccountInput::class   => UpdateMtAccountService::class,
        IUpdateMtAccountOutput::class => UpdateMtAccountOutput::class,

        SelectAccountInput::class  => SelectAccountService::class,
        SelectAccountOutput::class => SelectAccountJsonOutput::class,

        SetMarginCallInput::class => SetMarginCallService::class,

        SetStopOutInput::class => SetStopOutService::class,

        GetWithdrawalInfoInput::class  => GetWithdrawalInfoService::class,
        GetWithdrawalInfoOutput::class => GetWithdrawalInfoJsonOutput::class,

        SyncExternalBalanceInput::class => SyncExternalBalanceService::class,

        INotificationChannel::class => NotificationChannel::class,

    ];

    // public function registers() : void
    // {
    //     $this->commands([
    //         SyncMetatrader::class,
    //     ]);
    // }

    // public function schedule(Schedule $schedule) : void
    // {
    //     $schedule->command(SyncMetatrader::class, ['--scheduled'])
    //         ->runInBackground()
    //         ->withoutOverlapping()
    //         ->everyMinute();
    // }

    public function listen(Dispatcher $dispatcher) : void
    {
        $dispatcher->listen(['lead.block.trade','lead.unblock.trade'], ToggleBlockTradeListener::class);
        $dispatcher->listen(['lead.block.auth', 'lead.unblock.auth'], ToggleBlockAuthListener::class);
        $dispatcher->listen('trade.opened', TradeOpenedListener::class);
        $dispatcher->listen(['trade.closed', 'trade.partial.closed'], TradeClosedListener::class);
    }
}
