<?php

declare(strict_types=1);

namespace Platform\Account\Domain\Services\Update;

use Platform\Account\Domain\Dto\LeadDto;
use Platform\Package\Models\SettingModel;
use Platform\Account\Domain\Entities\Group;
use Platform\Account\Domain\Entities\Account;
use Platform\Package\Components\Collection\Arr;
use Platform\Account\Domain\Adapters\LeadAdapter;
use Platform\Account\Domain\Adapters\CurrencyAdapter;
use Platform\Package\Components\Log\Contracts\Logger;
use Platform\Account\Domain\Adapters\MetatraderAdapter;
use Platform\Account\Domain\Repositories\GroupRepository;
use Platform\Package\Components\Collection\ObjectChanger;
use Platform\Account\Domain\Repositories\AccountRepository;
use Platform\Package\Components\Events\Contracts\Dispatcher;
use Platform\Account\Domain\Repositories\GroupTypeRepository;
use Platform\Account\Domain\Adapters\NativeTradeEngineAdapter;
use Platform\Package\Components\System\ValueObjects\EngineType;
use Platform\Package\Components\System\Repositories\SettingRepository;
use Platform\Package\Components\System\Services\SettingResolver\Params;

class UpdateService implements UpdateInput
{
    public function __construct(
        private Logger              $logger,
        private UpdateOutput        $output,
        private MetatraderAdapter   $mtAdapter,
        private NativeTradeEngineAdapter $nativeTradeEngineAdapter,
        private Dispatcher          $dispatcher,
        private GroupRepository     $groupRepository,
        private CurrencyAdapter     $currencyAdapter,
        private AccountRepository   $accountRepository,
        private GroupTypeRepository $groupTypeRepository,
        private SettingRepository   $settingRepository,
        private LeadAdapter         $leadAdapter,
    ) {
    }

    public function update(UpdateRequest $request) : UpdateResult
    {
        $this->logger->withContext([
            'module'    => 'account',
            'service'   => class_basename(self::class),
            'accountId' => $request->accountId,
        ]);

        if ($request->isEmpty()) {
            return $this->output->emptyRequest();
        }

        $account = $this->accountRepository->find($request->accountId, true);

        if ( ! $account) {
            return $this->output->accountNotFound();
        }

        if ($request->name) {
            $this->dispatcher->dispatch('account.updated.name', [[
                'leadId' => $account->leadId,
                'name'   => $request->name,
            ]]);
        }

        /**
         * Changes affects on next properties:
         * @uses Account::$leverage
         * @uses Account::$enabled
         * @uses Account::$tradable
         * @uses Account::$groupId
         * @uses Account::$marginCall
         * @uses Account::$stopOut
         */
        $changer = (new ObjectChanger($account))
            ->changeNotNull('leverage', $request->leverage)
            ->changeNotNull('enabled', $request->enabled)
            ->changeNotNull('tradable', $request->tradable)
            ->changeNotNull('isStopOutCloseEnabled', $request->isStopOutCloseEnabled);

        $this->changeMargin($request, $changer);

        $engineType = $this->settingRepository->findOne('Engine Type', new Params());

        if ($this->isChangeGroup($request)) {
            if ( ! $account->group) {
                $this->logger->critical('group not found', [
                    'groupId' => $account->groupId,
                    'reason'  => 'finding group of account',
                ]);

                return $this->output->groupNotFound();
            }

            $lead = $this->leadAdapter->getById($account->leadId);

            /** @var Group|null $newGroup */
            $newGroup = $this->findNewGroup($account, $lead, $request, $engineType);

            if ( ! $newGroup) {
                /** @var Group $group */
                $group = $account->group;
                $this->logger->critical('group not found', [
                    'reason'       => 'finding by several criteria',
                    'isDemo'       => intval($group->isDemo),
                    'brandId'      => $group->brandId,
                    'specificType' => $group->specificType->name,
                    'currencyId'   => $group->currencyId,
                    'type'         => $request->type,
                ]);

                return $this->output->groupNotFound();
            }

            $changer->changeNotNull('groupId', $newGroup->id);
        }

        if ( ! $request->isFromMt) {
            if ($engineType->value() == EngineType::MT->value) {
                if ($output = $this->sendToMt($account, $request, $changer, $newGroup ?? null)) {
                    return $output;
                }
            }

            if ($engineType->value() == EngineType::NATIVE_TRADE_ENGINE->value) {
                if ($output = $this->sendToNT($account, $request, $changer, $newGroup ?? null)) {
                    return $output;
                }
            }
        }

        if ($changer->getChanges()) {
            if ($output = $this->updatePlatform($account, $request, $changer)) {
                return $output;
            }
        }

        if ($output = $this->handlePasswords($account, $request)) {
            return $output;
        }

        return $this->output->updated();
    }

    private function handlePasswords(Account $account, UpdateRequest $request) : ?UpdateResult
    {
        $mainPasswordNotChanged = (
            $request->mainPassword
            && ! $this->mtAdapter->changeMainPassword($account->login, $request->mainPassword)
        );

        if ($mainPasswordNotChanged) {
            return $this->output->mainPasswordNotChanged();
        }

        $investorPasswordNotChanged = (
            $request->investorPassword
            && ! $this->mtAdapter->changeInvestorPassword($account->login, $request->investorPassword)
        );

        if ($investorPasswordNotChanged) {
            return $this->output->investorPasswordNotChanged();
        }

        return null;
    }

    private function sendToMt(Account $account, UpdateRequest $request, ObjectChanger $changer, ?Group $group) : ?UpdateResult
    {
        if ($changer->getChanges() || $request->name) {
            $mtUser = $this->mtAdapter->updateUser(
                login: $account->login,
                name: $request->name,
                group: $group?->name,
                leverage: $changer->getNewValueInt('leverage'),
                enabled: $changer->getNewValueBool('enabled'),
                tradable: $changer->getNewValueBool('tradable'),
            );

            if ( ! $mtUser) {
                return $this->output->notUpdatedOnMetatrader();
            }
        }

        return null;
    }

    /**
     * @param Account $account
     * @param UpdateRequest $request
     * @param ObjectChanger $changer
     * @param Group|null $group
     * @return UpdateResult|null
     */
    private function sendToNt(Account $account, UpdateRequest $request, ObjectChanger $changer, ?Group $group) : ?UpdateResult
    {
        $group = $group ?: $this->groupRepository->find($account->groupId) ;

        if ($changer->getChanges() || $request->name) {
            $ntUser = $this->nativeTradeEngineAdapter->updateUser(
                login: $account->login,
                currencyId: $account->getCurrencyId(),
                groupId: $group->id,
                group: $group->name,
                name: $request->name ?? $this->leadAdapter->getById($account->leadId)->name,
                leverage: $account->leverage,
                enabled: $account->enabled,
                tradable: $account->tradable,
                isStopOutCloseEnabled: $account->isStopOutCloseEnabled,
            );

            if ( ! $ntUser) {
                return $this->output->notUpdatedOnNativeTradeEngine();
            }
        }

        return null;
    }

    private function updatePlatform(Account $account, UpdateRequest $request, ObjectChanger $changer) : ?UpdateResult
    {
        try {
            $this->accountRepository->update($account);

            $changeId = $this->accountRepository->saveChanges(
                $account->id,
                $changer->getChanges(),
                $request->isFromMt,
            );
        } catch (\Throwable $throwable) {
            $this->logger->critical('unexpected error', ['error' => Arr::formatThrowable($throwable)]);

            return $this->output->unableToUpdate();
        }

        $this->dispatcher->dispatch('account.updated', [[
            'accountId' => $account->id,
            'leadId'    => $account->leadId,
            'changeId'  => $changeId,
        ]]);

        return null;
    }

    private function findNewGroup(Account $account, LeadDto $lead, UpdateRequest $request, SettingModel $engineType) : ?Group
    {
        /** @var Group $group */
        $group = $account->group;

        $specificType = $request->specificGroupType ?? $group->specificType;
        $accountType  = $request->type ?? strval($this->groupTypeRepository->findNameById($group->typeId));

        return $this->groupRepository->findByCriteria(
            $group->isDemo,
            $lead->brandId,
            $specificType,
            $group->currencyId,
            $engineType->value(),
            $accountType,
        );
    }

    private function isChangeGroup(UpdateRequest $request) : bool
    {
        return ($request->type !== null || $request->specificGroupType !== null);
    }

    private function changeMargin(UpdateRequest $request, ObjectChanger $changer) : void
    {
        if (is_null($request->margin)) {
            return;
        }

        /** @var Account $account */
        $account = $changer->object;

        $marginSystem = $this->currencyAdapter->converseToSystem(
            amount: $request->margin,
            fromCurrencyId: (int) $account->group?->currencyId,
            groupId: $account->groupId,
        );

        /**
         * Changes affects on next properties:
         * @uses Account::$margin
         * @uses Account::$marginSystem
         */
        $changer->change('margin', $request->margin)
            ->change('marginSystem', $marginSystem);
    }
}
