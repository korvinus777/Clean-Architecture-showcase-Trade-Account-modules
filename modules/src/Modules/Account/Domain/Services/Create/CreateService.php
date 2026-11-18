<?php

declare(strict_types=1);

namespace Platform\Account\Domain\Services\Create;

use Throwable;
use Platform\Package\Models\SettingModel;
use Platform\Account\Domain\Dto\NTUserDto;
use Platform\Account\Domain\Entities\Group;
use Platform\Package\Components\Collection\Arr;
use Platform\Account\Domain\Adapters\LeadAdapter;
use Platform\Account\Domain\Dto\MetatraderUserDto;
use Platform\Package\Components\Http\Response\Code;
use Platform\Account\Domain\Adapters\CurrencyAdapter;
use Platform\Package\Components\Log\Contracts\Logger;
use Platform\Account\Domain\Adapters\MetatraderAdapter;
use Platform\Account\Domain\Repositories\GroupRepository;
use Platform\Account\Domain\Repositories\AccountRepository;
use Platform\Account\Domain\ValueObjects\AccountValueObject;
use Platform\Package\Components\Events\Contracts\Dispatcher;
use Platform\Account\Domain\Adapters\NativeTradeEngineAdapter;
use Platform\Package\Components\System\ValueObjects\EngineType;
use Platform\Package\Components\System\Contracts\SettingRepository;
use Platform\Package\Components\System\Services\SettingResolver\Params;

class CreateService implements CreateInput
{
    public const int INITIAL_LEVERAGE = 1;

    public function __construct(
        private Logger                   $logger,
        private CreateOutput             $output,
        private GroupRepository          $groupRepository,
        private AccountRepository        $accountRepository,
        private LeadAdapter              $leadAdapter,
        private CurrencyAdapter          $currencyAdapter,
        private MetatraderAdapter        $metatraderAdapter,
        private NativeTradeEngineAdapter $nativeTradeEngineAdapter,
        private SettingRepository        $settingRepository,
        private Dispatcher               $dispatcher,
    ) {
    }

    public function create(CreateRequest $request) : CreateResult
    {
        $this->logger->withContext([
            'module' => 'account',
            'service' => class_basename(self::class),
            'leadHashId' => $request->leadHashId,
        ]);

        $lead = $this->leadAdapter->findByHashId($request->leadHashId);

        if ( ! $lead) {
            $this->logger->error('lead not found', ['reason' => 'finding by lead hash id']);

            return $this->output->leadNotFound(Code::LEAD_NOT_FOUND);
        }

        $currencyId = $this->currencyAdapter->findIdByName($request->currency);

        if ( ! $currencyId) {
            $this->logger->error('currency not found', [
                'reason' => 'finding by name',
                'currencyName' => $request->currency,
            ]);

            return $this->output->currencyNotFound(Code::ACCOUNT_INVALID_CURRENCY);
        }

        $engineType = $this->settingRepository->findOne('Engine Type', new Params());

        if ( ! $engineType) {
            $this->logger->error('system setting not found', ['reason' => 'finding by engine type']);

            return $this->output->engineTypeNotFound(Code::ACCOUNT_ENGINE_TYPE_NOT_FOUND);
        }

        $group = $this->groupRepository->findByCriteria(
            $request->isDemo,
            $lead->brandId,
            $request->specificType,
            $currencyId,
            $engineType->value(),
            $request->type,
        );

        if ( ! $group) {
            $this->logger->error('group not found', [
                'reason' => 'finding by several criteria',
                'isDemo' => intval($request->isDemo),
                'brandId' => $lead->brandId,
                'specificType' => $request->specificType->name,
                'currencyId' => $currencyId,
                'type' => $request->type,
            ]);

            return $this->output->groupNotFound(Code::ACCOUNT_GROUP_NOT_FOUND);
        }

        $tradeUser = $this->addUser($request, $group, $engineType, $currencyId);

        if ( ! $tradeUser) {
            $this->logger->error('cannot add account on trading engine', [
                'engineType' => $engineType->value(),
            ]);

            return $this->output->metatraderError(Code::MT_ADD_USER_ERROR);
        }

        try {
            $account = $this->accountRepository->create(new AccountValueObject(
                $lead->id,
                $tradeUser->login,
                $group->id,
                self::INITIAL_LEVERAGE,
                $request->enabled,
                $request->tradable,
                $request->isStopOutCloseEnabled,
            ));

            $this->dispatcher->dispatch('account.created', [$account->toArray()]);
            $this->dispatcher->dispatch('account.updated.name', [[
                'leadId' => $lead->id,
                'name' => $request->name,
            ]]);

            return $this->output->created(Code::SUCCESS, $account);
        } catch (Throwable $throwable) {
            $this->logger->critical('unexpected error', ['error' => Arr::formatThrowable($throwable)]);

            return $this->output->canNotCreate(Code::ACCOUNT_CREATE_ERROR);
        }
    }

    private function addUser(
        CreateRequest $request,
        Group $group,
        SettingModel $engineType,
        int $currencyId,
    ) : MetatraderUserDto|NTUserDto|null {
        return match ($engineType->value()) {
            EngineType::MT->value                  => $this->addMtUser($request, $group),
            EngineType::NATIVE_TRADE_ENGINE->value => $this->addNtUser($request, $group, $currencyId),
            default                                => null,
        };
    }

    private function addMtUser(CreateRequest $request, Group $group) : MetatraderUserDto|null
    {
        return $this->metatraderAdapter->addUser(
            strval($request->leadHashId),
            $group->name,
            $request->mainPassword,
            $request->investorPassword,
            self::INITIAL_LEVERAGE,
            $request->enabled,
            $request->tradable,
        );
    }

    private function addNtUser(CreateRequest $request, Group $group, int $currencyId) : NTUserDto|null
    {
        return $this->nativeTradeEngineAdapter->addUser(
            $request->leadHashId,
            $currencyId,
            $group->id,
            $group->name,
            $request->name,
            self::INITIAL_LEVERAGE,
            $request->enabled,
            $request->tradable,
            $request->isStopOutCloseEnabled,
        );
    }
}
