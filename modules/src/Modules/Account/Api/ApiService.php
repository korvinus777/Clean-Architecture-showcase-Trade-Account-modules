<?php

declare(strict_types=1);

namespace Platform\Account\Api;

use Platform\Account\Api\Dto\GroupDto;
use Platform\Account\Api\Dto\AccountDto;
use Platform\Account\Api\Dto\ShortAccountDto;
use Platform\Account\Domain\Dto\AccountBalancesDto;
use Platform\Account\Domain\Entities\SpecificGroupType;
use Platform\Account\Domain\Services\Create\CreateInput;
use Platform\Account\Domain\Services\Delete\DeleteInput;
use Platform\Account\Domain\Services\Update\UpdateInput;
use Platform\Account\Domain\Repositories\GroupRepository;
use Platform\Account\Domain\Services\Create\CreateRequest;
use Platform\Account\Domain\Services\Delete\DeleteRequest;
use Platform\Account\Domain\Services\Update\UpdateRequest;
use Platform\Account\Domain\Repositories\AccountRepository;
use Platform\Account\Domain\Repositories\SettingRepository;
use Platform\Account\Domain\Services\AddGroup\AddGroupInput;
use Platform\Account\Domain\Services\AddGroup\AddGroupRequest;
use Platform\Package\Components\Container\Contracts\Container;
use Platform\Account\Domain\Services\SetStopOut\SetStopOutInput;
use Platform\Account\Domain\Services\SetStopOut\SetStopOutRequest;
use Platform\Account\Domain\Repositories\AccountStatisticsRepository;
use Platform\Account\Domain\Services\SetMarginCall\SetMarginCallInput;
use Platform\Account\App\Services\UpdateMtAccount\UpdateMtAccountInput;
use Platform\Account\Domain\Services\SetMarginCall\SetMarginCallRequest;
use Platform\Account\App\Services\UpdateMtAccount\UpdateMtAccountRequest;
use Platform\Account\Domain\Services\LeadAccountList\LeadAccountListInput;
use Platform\Account\Domain\Services\LeadAccountList\LeadAccountListRequest;
use Platform\Account\Domain\Services\SyncExternalBalance\SyncExternalBalanceInput;
use Platform\Account\Domain\Services\SyncExternalBalance\SyncExternalBalanceRequest;

class ApiService implements Api
{
    public function __construct(
        private Container                    $container,
        private AccountRepository            $accountRepository,
        private GroupRepository              $groupRepository,
        private SettingRepository            $settingRepository,
        private AccountStatisticsRepository  $accountStatisticsRepository,
    ) {
    }

    public function findGroupIdByName(string $group) : ?int
    {
        $group = $this->groupRepository->findByName($group);

        return $group?->id;
    }

    public function addAccountGroup(
        string  $name,
        ?bool   $isDemo = null,
        ?string $currency = null,
        ?int    $currencyPrecision = null,
    ) : ?GroupDto {
        $result = $this->container->make(AddGroupInput::class)->add(new AddGroupRequest(
            name: $name,
            isDemo: $isDemo,
            currency: $currency,
            currencyPrecision: $currencyPrecision,
        ));

        return $result->group ? GroupDto::fromEntity($result->group) : null;
    }

    public function getById(int $id) : ?AccountDto
    {
        $account = $this->accountRepository->find($id, true);

        return $account ? AccountDto::fromEntity($account) : null;
    }

    public function updateBalance(int $accountId, float $amount, float $amountSystem) : bool
    {
        return $this->accountRepository->updateBalance($accountId, $amount, $amountSystem);
    }

    public function updateCredit(int $accountId, float $amount, float $amountSystem) : bool
    {
        return $this->accountRepository->updateCredit($accountId, $amount, $amountSystem);
    }

    public function getGroupOfAccount(int $accountId) : ?GroupDto
    {
        $group = $this->groupRepository->getGroupOfAccount($accountId);

        if ($group) {
            return GroupDto::fromEntity($group);
        }

        return null;
    }

    public function getDefaultGroup() : GroupDto
    {
        $group = $this->groupRepository->getDefaultGroup();

        return GroupDto::fromEntity($group);
    }

    public function getAccountLeverage(int $accountId) : int
    {
        $account = $this->accountRepository->find($accountId);

        return $account?->leverage ?? 0;
    }

    public function getDefaultLeverage() : int
    {
        return $this->settingRepository->defaultLeverage();
    }

    public function create(
        int    $leadHashId,
        string $name,
        string $type,
        int    $specificType,
        string $currency,
        bool   $isDemo,
        string $mainPassword,
        string $investorPassword,
        bool   $enabled,
        bool   $tradable,
        bool   $isStopOutCloseEnabled,
    ) : ?AccountDto {
        $result = $this->container->make(CreateInput::class)->create(new CreateRequest(
            $leadHashId,
            $name,
            $type,
            SpecificGroupType::from($specificType),
            $currency,
            $isDemo,
            $mainPassword,
            $investorPassword,
            $enabled,
            $tradable,
            $isStopOutCloseEnabled,
        ));

        return $result->account ? AccountDto::fromEntity($result->account) : null;
    }

    public function findGroup(int $groupId) : ?GroupDto
    {
        if ($group = $this->groupRepository->find($groupId, true)) {
            return GroupDto::fromEntity($group);
        }

        return null;
    }

    public function findByLogin(int $login) : ?AccountDto
    {
        $account = $this->accountRepository->findByLogin($login, true);

        return $account ? AccountDto::fromEntity($account) : null;
    }

    public function getGroupIds() : array
    {
        return $this->groupRepository->getGroupIds();
    }

    public function updateMtAccount(
        int     $login,
        ?string $group = null,
        ?int    $leverage = null,
        ?bool   $isEnabled = null,
        ?bool   $isTradable = null,
        ?float  $margin = null,
    ) : bool {
        $result = $this->container->make(UpdateMtAccountInput::class)
            ->update(new UpdateMtAccountRequest(
                login: $login,
                groupName: $group,
                leverage: $leverage,
                isEnabled: $isTradable,
                isTradable: $isEnabled,
                margin: $margin,
            ));

        return $result->success;
    }

    public function delete(int $accountId, bool $isFromMt = false) : bool
    {
        $result = $this->container->make(DeleteInput::class)
            ->delete(new DeleteRequest(
                accountId: $accountId,
                isFromMt: $isFromMt,
            ));

        return $result->throwable === null;
    }

    public function getSelectedAccountId(int $leadId) : int
    {
        return $this->accountRepository->getSelectedAccountId($leadId);
    }

    /**
     * @inheritDoc
     */
    public function getAccountsByLogin(array $logins) : array
    {
        $accounts = $this->accountRepository->findByLogins($logins);

        return array_map([AccountDto::class, 'fromEntity'], $accounts);
    }

    /**
     * @param int $login
     * @return bool
     */
    public function marginCallEnter(int $login) : bool
    {
        $account = $this->accountRepository->findByLogin($login);

        if ( ! $account) {
            return false;
        }

        return $this->container->make(SetMarginCallInput::class)
            ->set(new SetMarginCallRequest(
                accountId: $account->id,
                isEnter: true,
            ));
    }

    /**
     * @param int $login
     * @return bool
     */
    public function marginCallLeave(int $login) : bool
    {
        $account = $this->accountRepository->findByLogin($login);

        if ( ! $account) {
            return false;
        }

        return $this->container->make(SetMarginCallInput::class)
            ->set(new SetMarginCallRequest(
                accountId: $account->id,
                isEnter: false,
            ));
    }

    /**
     * @param int $login
     * @return bool
     */
    public function stopOutEnter(int $login) : bool
    {
        $account = $this->accountRepository->findByLogin($login);

        if ( ! $account) {
            return false;
        }

        return $this->container->make(SetStopOutInput::class)
            ->set(new SetStopOutRequest(
                accountId: $account->id,
                isEnter: true,
            ));
    }

    /**
     * @param int $login
     * @return bool
     */
    public function stopOutLeave(int $login) : bool
    {
        $account = $this->accountRepository->findByLogin($login);

        if ( ! $account) {
            return false;
        }

        return $this->container->make(SetStopOutInput::class)
            ->set(new SetStopOutRequest(
                accountId: $account->id,
                isEnter: false,
            ));
    }

    public function sumSystemBalanceOfLeads(array $leadIds) : float
    {
        return $this->accountRepository->sumSystemBalanceOfLeads($leadIds);
    }

    public function findAccountBalances(int $accountId) : ?AccountBalancesDto
    {
        return $this->accountRepository->findBalances($accountId);
    }

    /**
     * @inheritDoc
     */
    public function getAccountsOfLead(int $leadId) : array
    {
        $service = $this->container->make(LeadAccountListInput::class);

        $result = $service->get(new LeadAccountListRequest(leadId: $leadId));

        return array_map([ShortAccountDto::class, 'fromDto'], $result->accountList);
    }

    /**
     * @param int $login
     * @param float $balance
     * @param float $credit
     * @return bool
     */
    public function updateAccountBalanceByLogin(int $login, float $balance, float $credit) : bool
    {
        return $this->container->make(SyncExternalBalanceInput::class)
            ->sync(new SyncExternalBalanceRequest(
                login: $login,
                balance: $balance,
                credit: $credit,
            ));
    }

    /**
     * @param int $login
     * @param float $margin
     * @param bool $isFromMt
     * @return bool
     */
    public function updateMargin(int $login, float $margin, bool $isFromMt) : bool
    {
        $account = $this->accountRepository->findByLogin($login);

        if ( ! $account) {
            return false;
        }

        $result = $this->container->make(UpdateInput::class)->update(new UpdateRequest(
            accountId: $account->id,
            margin: $margin,
            isFromMt: $isFromMt,
        ));

        return $result->isOk();
    }

    public function sumTurnoverOfLeads(array $leadIds) : array
    {
        return $this->accountStatisticsRepository->sumTurnoverOfLeads($leadIds);
    }

    public function addOpenTurnover(int $accountId, float $turnover, float $turnoverSystem) : bool
    {
        return $this->accountStatisticsRepository->addOpenTurnover($accountId, $turnover, $turnoverSystem);
    }

    public function moveOpenToClosedTurnover(int $accountId, float $turnover, float $turnoverSystem) : bool
    {
        return $this->accountStatisticsRepository->moveOpenToClosedTurnover($accountId, $turnover, $turnoverSystem);
    }

    public function moveClosedToOpenTurnover(int $accountId, float $turnover, float $turnoverSystem) : bool
    {
        return $this->accountStatisticsRepository->moveClosedToOpenTurnover($accountId, $turnover, $turnoverSystem);
    }
}
