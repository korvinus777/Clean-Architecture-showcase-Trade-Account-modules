<?php

declare(strict_types=1);

namespace Platform\Account\Domain\Services\SyncExternalBalance;

use Platform\Package\Components\Time\DateTime;
use Platform\Account\Domain\Adapters\CurrencyAdapter;
use Platform\Package\Components\Collection\ObjectChanger;
use Platform\Account\Domain\Repositories\AccountRepository;
use Platform\Package\Components\Events\Contracts\Dispatcher;

class SyncExternalBalanceService implements SyncExternalBalanceInput
{
    /**
     * @param AccountRepository $accountRepository
     * @param CurrencyAdapter $currencyAdapter
     * @param Dispatcher $dispatcher
     */
    public function __construct(
        private AccountRepository   $accountRepository,
        private CurrencyAdapter     $currencyAdapter,
        private Dispatcher          $dispatcher,
    ) {

    }

    /**
     * @param SyncExternalBalanceRequest $request
     * @return bool
     */
    public function sync(SyncExternalBalanceRequest $request) : bool
    {
        if ( ! $account = $this->accountRepository->findByLogin($request->login, true)) {
            return false;
        }

        $changer = (new ObjectChanger($account))
            ->change('balance', $request->balance)
            ->change('credit', $request->credit);

        $changeId = 0;

        if ($changer->isChanged('balance')) {
            $balanceDiff = $changer->getNewValueFloat('balance') - $changer->getOldValueFloat('balance');

            $requiredBalanceSystem = $this->currencyAdapter->converseToSystem(
                $changer->getNewValueFloat('balance'),
                $account->getCurrencyId(),
                $account->groupId,
            );

            $balanceSystemDiff = $requiredBalanceSystem - $account->balanceSystem;

            $this->accountRepository->updateBalance($account->id, $balanceDiff, $balanceSystemDiff);

            $changeId = $this->accountRepository->saveChanges(
                accountId: $account->id,
                diffs: $changer->getChanges(only: ['balance']),
                isFromMt: true,
            );
        }

        if ($changer->isChanged('credit')) {
            $creditDiff = $changer->getNewValueFloat('credit') - $changer->getOldValueFloat('credit');

            $requiredCreditSystem = $this->currencyAdapter->converseToSystem(
                $changer->getNewValueFloat('credit'),
                $account->getCurrencyId(),
                $account->groupId,
            );

            $creditSystemDiff = $requiredCreditSystem - $account->creditSystem;

            $this->accountRepository->updateCredit($account->id, $creditDiff, $creditSystemDiff);

            $changeId = $this->accountRepository->saveChanges(
                accountId: $account->id,
                diffs: $changer->getChanges(only: ['credit']),
                isFromMt: true,
            );
        }

        if ($changeId) {
            $this->dispatcher->dispatch('account.balance.synced.from-mt', [[
                'id' => $account->id,
                'updatedTimestamp' => DateTime::now()->getTimestamp(),
                'changeId' => $changeId,
            ]]);
        }

        return true;
    }
}
