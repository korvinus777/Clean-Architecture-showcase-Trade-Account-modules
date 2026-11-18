<?php

declare(strict_types=1);

namespace Platform\Account\App\Services\SyncMtAccount;

use Platform\Account\Domain\Entities\Account;
use Platform\Package\Components\Time\DateTime;
use Platform\Account\Domain\Adapters\CurrencyAdapter;
use Platform\Package\Components\Log\Contracts\Logger;
use Platform\Account\Domain\Dto\MetatraderFullUserDto;
use Platform\Account\Domain\Services\Delete\DeleteInput;
use Platform\Account\Domain\Services\Update\UpdateInput;
use Platform\Account\Domain\Repositories\GroupRepository;
use Platform\Package\Components\Collection\ObjectChanger;
use Platform\Account\Domain\Services\Delete\DeleteRequest;
use Platform\Account\Domain\Services\Update\UpdateRequest;
use Platform\Account\Domain\Repositories\AccountRepository;
use Platform\Package\Components\Events\Contracts\Dispatcher;

class SyncMtAccountService implements SyncMtAccountInput
{
    public function __construct(
        private Logger              $logger,
        private Dispatcher          $dispatcher,
        private UpdateInput         $updateService,
        private DeleteInput         $deleteService,
        private CurrencyAdapter     $currencyAdapter,
        private GroupRepository     $groupRepository,
        private AccountRepository   $accountRepository,
        private SyncMtAccountOutput $output,
    ) {
    }

    public function sync(SyncMtAccountRequest $request) : SyncMtAccountResult
    {
        $mtUser  = $request->aggregate->mtUser;
        $account = $request->aggregate->account;

        $this->logger->withContext([
            'module'    => 'account',
            'service'   => class_basename(self::class),
            'accountId' => $account->id,
            'login'     => $account->login,
        ]);

        if ( ! $mtUser) {
            $this->logger->error('account not found on mt');

            if ($request->syncDeleting) {
                $deleteResult = $this->deleteService->delete(new DeleteRequest(
                    accountId: $account->id,
                    isFromMt: true,
                ));

                return $this->output->deleted($deleteResult->isDeleted());
            }

            return $this->output->deleted(deleted: true);
        }

        $group = $this->groupRepository->findByName($mtUser->group, withType: true);

        if ( ! $group) {
            $this->logger->error('group not found', [
                'group' => $mtUser->group,
            ]);
        }

        $updateResult = $this->updateService->update(new UpdateRequest(
            accountId: $request->aggregate->account->id,
            type: $group?->typeName,
            specificGroupType: $group?->specificType,
            leverage: $mtUser->leverage,
            enabled: $mtUser->enabled,
            tradable: $mtUser->tradable,
            margin: $mtUser->hasAccount ? $mtUser->margin : null,
            isFromMt: true,
            isStopOutCloseEnabled: $request->aggregate->account->isStopOutCloseEnabled,
        ));

        $balancesSynced = $this->syncBalances($request->syncBalances, $account, $mtUser);

        return $this->output->synced(
            synced: $updateResult->isOk(),
            balancesSynced: $balancesSynced,
        );
    }

    private function syncBalances(bool $sync, Account $account, MetatraderFullUserDto $mtUser) : bool
    {
        $changer = (new ObjectChanger($account))
            ->change('balance', $mtUser->balance)
            ->change('credit', $mtUser->credit);

        if ( ! $changer->getChanges()) {
            return true;
        }

        $changeId = 0;

        $balanceUpdated = $creditUpdated = true;

        // sync balance and log changes
        if ($changer->isChanged('balance') && $sync) {
            [$amount, $amountSystem] = $this->calcAmount(
                account: $account,
                current: (float) $changer->getOldValueFloat('balance'),
                new: (float) $changer->getNewValueFloat('balance'),
            );

            if ($balanceUpdated = $this->accountRepository->updateBalance($account->id, $amount, $amountSystem)) {
                $changeId = $this->accountRepository->saveChanges(
                    accountId: $account->id,
                    diffs: $changer->getChanges(only: ['balance']),
                    isFromMt: true,
                );
            }
        }

        // sync credit and log changes
        if ($changer->isChanged('credit') && $sync) {
            [$amount, $amountSystem] = $this->calcAmount(
                account: $account,
                current: (float) $changer->getOldValueFloat('credit'),
                new: (float) $changer->getNewValueFloat('credit'),
            );

            if ($creditUpdated = $this->accountRepository->updateCredit($account->id, $amount, $amountSystem)) {
                $changeId = $this->accountRepository->saveChanges(
                    accountId: $account->id,
                    diffs: $changer->getChanges(only: ['credit']),
                    isFromMt: true,
                );
            }
        }

        if ($sync) {
            if ( ! $balanceUpdated || ! $creditUpdated) {
                $this->logger->critical('balance not updated', [
                    'reason'  => json_encode(compact('balanceUpdated', 'creditUpdated')),
                    'changes' => json_encode($changer->getChanges()),
                ]);

                return false;
            }

            $this->dispatcher->dispatch(
                'account.balance.synced.from-mt',
                [$account->toArray([
                    'changeId'         => $changeId,
                    'updatedTimestamp' => DateTime::now()->getTimestamp(),
                ])],
            );

            return true;
        }

        $this->logger->critical('balances are different', [
            'changes' => json_encode($changer->getChanges()),
        ]);

        return true;
    }

    /**
     * @param Account $account
     * @param float $current
     * @param float $new
     * @return array{float, float}
     */
    private function calcAmount(Account $account, float $current, float $new) : array
    {
        $amount       = $new - $current;
        $amountSystem = $this->currencyAdapter->converseToSystem($amount, $account->getCurrencyId(), $account->groupId);

        return [$amount, $amountSystem];
    }
}
