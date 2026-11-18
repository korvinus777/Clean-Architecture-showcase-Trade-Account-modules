<?php

declare(strict_types=1);

namespace Platform\Account\Infrastructure\Repositories;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;
use Platform\Account\Domain\Entities\Account;
use Platform\Package\Components\Time\DateTime;
use Platform\Package\Components\Collection\Arr;
use Platform\Account\Domain\Dto\AccountBalancesDto;
use Platform\Package\Components\Log\Contracts\Logger;
use Platform\Account\Infrastructure\Models\ChangeModel;
use Platform\Account\Infrastructure\Models\AccountModel;
use Platform\Account\Infrastructure\Mappers\AccountMapper;
use Platform\Account\Domain\ValueObjects\AccountValueObject;
use Platform\Account\Domain\Repositories\AccountRepository as IAccountRepository;

class AccountRepository implements IAccountRepository
{
    public function __construct(
        private Logger $logger,
    ) {
        $this->logger->withContext([
            'module'  => 'account',
            'service' => class_basename(self::class),
        ]);
    }

    public function create(AccountValueObject $accountValueObject) : Account
    {
        /** @var AccountModel $accountModel */
        $accountModel = AccountModel::query()->create([
            'login'            => $accountValueObject->login,
            'leadId'           => $accountValueObject->leadId,
            'groupId'          => $accountValueObject->groupId,
            'leverage'         => $accountValueObject->leverage,
            'isEnabled'        => $accountValueObject->enabled,
            'isTradable'       => $accountValueObject->tradable,
            'isSelected'       => false,
            'isMarginCall'     => false,
            'isStopOut'        => false,
            'isStopOutCloseEnabled' => $accountValueObject->isStopOutCloseEnabled,
            'balance'          => 0,
            'balanceSystem'    => 0,
            'credit'           => 0,
            'creditSystem'     => 0,
            'equity'           => 0,
            'equitySystem'     => 0,
            'openPnl'          => 0,
            'openPnlSystem'    => 0,
            'margin'           => 0,
            'marginSystem'     => 0,
            'freeMargin'       => 0,
            'freeMarginSystem' => 0,
            'marginLevel'      => 0,
        ]);

        return AccountMapper::entityFromModel($accountModel);
    }

    public function find(int $id, bool $withGroup = false) : ?Account
    {
        /** @var AccountModel|null $model */
        $model = AccountModel::query()
            ->where('id', '=', $id)
            /** @uses AccountModel::group() */
            ->when($withGroup, fn (Builder $builder) => $builder->with('group'))
            ->first();

        return $model ? AccountMapper::entityFromModel($model) : null;
    }

    public function get(int $id, bool $withGroup = false) : Account
    {
        /** @var AccountModel $model */
        $model = AccountModel::query()
            ->where('id', '=', $id)
            /** @uses AccountModel::group() */
            ->when($withGroup, fn (Builder $builder) => $builder->with('group'))
            ->firstOrFail();

        return AccountMapper::entityFromModel($model);
    }

    /**
     * @param int $leadId
     * @param bool $withGroup
     * @return Account[]
     */
    public function findByLead(int $leadId, bool $withGroup = false) : array
    {
        /** @var AccountModel[] $models */
        $models = AccountModel::query()
            ->where('leadId', '=', $leadId)
            /** @uses AccountModel::group() */
            ->when($withGroup, fn (Builder $builder) => $builder->with('group'))
            ->get()
            ->all();

        return array_map([AccountMapper::class, 'entityFromModel'], $models);
    }

    public function findByLeadAndLogin(int $leadId, int $login, bool $withGroup = false) : ?Account
    {
        /** @var AccountModel|null $model */
        $model = AccountModel::query()
            ->where('leadId', '=', $leadId)
            ->where('login', '=', $login)
            /** @uses AccountModel::group() */
            ->when($withGroup, fn (Builder $builder) => $builder->with('group'))
            ->first();

        return $model ? AccountMapper::entityFromModel($model) : null;
    }

    public function update(Account $account) : void
    {
        AccountModel::query()
            ->whereKey($account->id)
            ->update([
                'leadId'       => $account->leadId,
                'login'        => $account->login,
                'groupId'      => $account->groupId,
                'leverage'     => $account->leverage,
                'isEnabled'    => $account->enabled,
                'isTradable'   => $account->tradable,
                'margin'       => $account->margin,
                'marginSystem' => $account->marginSystem,
                'isStopOutCloseEnabled' => $account->isStopOutCloseEnabled,
            ]);
    }

    public function delete(int $id) : void
    {
        AccountModel::query()->whereKey($id)->delete();
    }

    public function updateBalance(int $accountId, float $amount, float $amountSystem) : bool
    {
        try {
            DB::update(
                'UPDATE `accounts` SET balance = balance + (?), balanceSystem = balanceSystem + (?) WHERE id = ?',
                [$amount, $amountSystem, $accountId],
            );

            return true;
        } catch (\Throwable $throwable) {
            $this->logger->critical('unexpected error', [
                'accountId' => $accountId,
                'method'    => __METHOD__,
                'error'     => Arr::formatThrowable($throwable),
            ]);

            return false;
        }
    }

    public function updateCredit(int $accountId, float $amount, float $amountSystem) : bool
    {
        try {
            DB::update(
                'UPDATE `accounts` SET credit = credit + (?), creditSystem = creditSystem + (?)  WHERE id = ?',
                [$amount, $amountSystem, $accountId],
            );

            return true;
        } catch (\Throwable $throwable) {
            $this->logger->critical('unexpected error', [
                'accountId' => $accountId,
                'method'    => __METHOD__,
                'error'     => Arr::formatThrowable($throwable),
            ]);

            return false;
        }
    }

    public function findByLogin(int $login, bool $withGroup = false) : ?Account
    {
        /** @var AccountModel|null $model */
        $model = AccountModel::query()
            ->where('login', $login)
            /** @uses AccountModel::group() */
            ->when($withGroup, fn (Builder $builder) => $builder->with('group'))
            ->first();

        return $model ? AccountMapper::entityFromModel($model) : null;
    }

    public function updateMargin(int $id, float $margin, float $marginSystem) : bool
    {
        try {
            AccountModel::query()->whereKey($id)->update([
                'margin'       => $margin,
                'marginSystem' => $marginSystem,
            ]);

            return true;
        } catch (\Throwable $throwable) {
            $this->logger->critical('unexpected error', [
                'accountId' => $id,
                'method'    => __METHOD__,
                'error'     => Arr::formatThrowable($throwable),
            ]);

            return false;
        }
    }

    public function saveChanges(int $accountId, array $diffs, bool $isFromMt) : int
    {
        /** @var  ChangeModel $model */
        $model = ChangeModel::query()->create([
            'accountId' => $accountId,
            'diffs'     => $diffs,
            'isFromMt'  => $isFromMt,
            'createdAt' => DateTime::now(),
        ]);

        return $model->id;
    }

    public function unselectedAll(int $leadId) : void
    {
        AccountModel::query()->where('leadId', $leadId)->update(['isSelected' => false]);
    }

    public function select(int $accountId, int $leadId) : bool
    {
        $updated = AccountModel::query()
            ->where('id', $accountId)
            ->where('leadId', $leadId)
            ->update(['isSelected' => true]);

        return $updated !== 0;
    }

    public function getSelectedAccountId(int $leadId) : int
    {
        /** @var AccountModel|null $account */
        $account = AccountModel::query()
            ->where('leadId', '=', $leadId)
            ->where('isSelected', '=', true)
            ->first();

        return $account === null ? 0 : $account->id;
    }

    /**
     * @inheritDoc
     */
    public function findByLogins(array $logins) : array
    {
        /** @var Collection<array-key, AccountModel> $accounts */
        $accounts = AccountModel::query()
            ->whereIn('login', $logins)
            ->with('group')
            ->get();

        return array_map([AccountMapper::class, 'entityFromModel'], $accounts->keyBy('login')->all());
    }

    /**
     * @param int $accountId
     * @param bool $isEnter
     * @return bool
     */
    public function setMarginCall(int $accountId, bool $isEnter) : bool
    {
        return (bool) AccountModel::query()
            ->whereKey($accountId)
            ->update(['isMarginCall' => $isEnter]);
    }

    /**
     * @param int $accountId
     * @param bool $isEnter
     * @return bool
     */
    public function setStopOut(int $accountId, bool $isEnter) : bool
    {
        return (bool) AccountModel::query()
            ->whereKey($accountId)
            ->update(['isStopOut' => $isEnter]);
    }

    public function sumSystemBalanceOfLeads(array $leadIds) : float
    {
        return (float) AccountModel::query()
            ->whereIn('leadId', $leadIds)
            ->sum('balanceSystem');
    }

    /**
     * @inheritDoc
     */
    public function getLastAccounts(int $lastLogin, int $limit) : array
    {
        /** @var Collection<int, AccountModel> $accounts */
        $accounts = AccountModel::query()
            ->with('group')
            ->where('login', '>', $lastLogin)
            ->orderBy('login', 'ASC')
            ->limit($limit)
            ->get()
            ->keyBy('login');

        return array_map([AccountMapper::class, 'entityFromModel'], $accounts->all());
    }

    /**
     * @param int $accountId
     * @return AccountBalancesDto|null
     */
    public function findBalances(int $accountId) : ?AccountBalancesDto
    {
        /** @var AccountModel|null $accountModel */
        $accountModel = AccountModel::query()
            ->whereKey($accountId)
            ->with('balances')
            ->first();

        if ( ! $accountModel) {
            return null;
        }

        return new AccountBalancesDto(
            leadId: $accountModel->leadId,
            accountId: $accountModel->id,
            login: $accountModel->login,
            balance: $accountModel->balance,
            balanceSystem: $accountModel->balanceSystem,
            credit: $accountModel->credit,
            creditSystem: $accountModel->creditSystem,
            equity: (float) $accountModel->balances?->equity,
            equitySystem: (float) $accountModel->balances?->equitySystem,
            openPnl: (float) $accountModel->balances?->openPnl,
            openPnlSystem: (float) $accountModel->balances?->openPnlSystem,
            margin: $accountModel->margin,
            marginSystem: $accountModel->marginSystem,
            freeMargin: (float) $accountModel->balances?->freeMargin,
            freeMarginSystem: (float) $accountModel->balances?->freeMarginSystem,
            availableWithdrawal: (float) $accountModel->balances?->availableWithdrawal,
            marginLevel: (int) $accountModel->balances?->marginLevel,
        );
    }

    /**
     * @inheritDoc
     */
    public function findByIds(array $ids) : array
    {
        /** @var Collection<array-key, AccountModel> $accounts */
        $accounts = AccountModel::query()
            ->whereIn('id', $ids)
            ->with('group')
            ->get();

        return array_map([AccountMapper::class, 'entityFromModel'], $accounts->keyBy('id')->all());
    }
}
