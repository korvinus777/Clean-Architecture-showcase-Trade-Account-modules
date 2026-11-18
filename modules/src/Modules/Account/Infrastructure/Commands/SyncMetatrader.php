<?php

declare(strict_types=1);

namespace Platform\Account\Infrastructure\Commands;

use Platform\Account\Domain\Entities\Account;
use Platform\Package\Components\Time\DateTime;
use Platform\Package\Components\Collection\Arr;
use Platform\Package\Components\Console\Command;
use Platform\Account\App\Dto\AggregateMtAccountDto;
use Platform\Account\Domain\Dto\MetatraderFullUserDto;
use Platform\Account\Domain\Adapters\MetatraderAdapter;
use Platform\Account\Domain\Adapters\TransactionAdapter;
use Platform\Account\Domain\Repositories\AccountRepository;
use Platform\Account\App\Services\SyncMtAccount\SyncMtAccountInput;
use Platform\Account\App\Services\SyncMtAccount\SyncMtAccountRequest;
use Platform\Package\Components\Container\Concerns\DependencyResolver;

class SyncMetatrader extends Command
{
    use DependencyResolver;

    protected $signature = 'account:sync-mt
                            {--scheduled : Flag for determine if command executed by scheduler}
                            {--syncBalances= : Sync main & credit balances}
                            {--syncDeleting= : Sync account existence}
                            {--from= : Time which take transactions from. Format Y-m-d H:i:s}
                            {--login=* : Logins of MT accounts which need sync}
                            {--chunk= : Number of accounts to sync for one iteration}';

    protected $description = 'Sync accounts data from MT';

    private bool $scheduled;

    public function handle() : int
    {
        $this->scheduled = $this->boolOption('scheduled');

        $syncMtAccountService = $this->resolve(SyncMtAccountInput::class);

        foreach ($this->getAccounts() as $account) {
            $result = $syncMtAccountService->sync(new SyncMtAccountRequest(
                aggregate: $account,
                syncBalances: $this->isSyncBalances(),
                syncDeleting: $this->isSyncDeleting(),
            ));

            if ( ! $result->deletingSynced) {
                $this->error("Account [{$account->account->login}] not deleted");

                continue;
            }

            if ($result->synced) {
                $this->info("Account [{$account->account->login}] synced");
            } else {
                $this->error("Account [{$account->account->login}] not synced");
            }

            if ( ! $result->balancesSynced) {
                $this->error("Account [{$account->account->login}] balances not synced");
            }
        }

        return $this->success();
    }

    /** @return iterable<array-key, AggregateMtAccountDto> */
    private function getAccounts() : iterable
    {
        if ($this->option('login')) {
            return $this->getAccountsByLogins();
        }

        return $this->getAccountsByTransactions();
    }

    /** @return iterable<array-key, AggregateMtAccountDto> */
    private function getAccountsByLogins() : iterable
    {
        $accountRepository = $this->resolve(AccountRepository::class);
        $metatraderAdapter = $this->resolve(MetatraderAdapter::class);

        $logins = array_map('intval', (array) $this->option('login'));

        $accounts = $accountRepository->findByLogins($logins);

        if ( ! $accounts) {
            return;
        }

        $mtUsers = $metatraderAdapter->getFullUsers(array_keys($accounts));

        if (is_null($mtUsers)) {
            $this->error('can not get accounts from mt');

            return;
        }

        yield from $this->makeAggregation($accounts, $mtUsers);
    }

    /** @return iterable<array-key, AggregateMtAccountDto> */
    private function getAccountsByTransactions() : iterable
    {
        $accountRepository  = $this->resolve(AccountRepository::class);
        $metatraderAdapter  = $this->resolve(MetatraderAdapter::class);
        $transactionAdapter = $this->resolve(TransactionAdapter::class);

        $from = $this->option('from')
            ? $this->stringOption('from')
            : $this->contextPayload()->getString('from', DateTime::yesterday()->formatDb());
        $chunk = $this->option('chunk')
            ? $this->intOption('chunk')
            : $this->contextPayload()->getInt('chunk', 50);

        $page = 0;
        $dateTime = DateTime::createFromTimeString($from);

        while (true) {
            ++$page;

            $this->info("Get [$chunk] accounts with transactions after [$from] of page number [$page]");

            $ids = $transactionAdapter->getActiveAccountsAfter(
                after: $dateTime,
                limit: $chunk,
                page: $page,
            );

            if ( ! $ids) {
                break;
            }

            /** @var array<int, Account> $accounts */
            $accounts = Arr::keyBy($accountRepository->findByIds($ids), 'login');

            if ( ! $accounts) {
                break;
            }

            $mtUsers = $metatraderAdapter->getFullUsers(array_keys($accounts));

            if (is_null($mtUsers)) {
                $this->error("can not get accounts from mt for page [$page]");

                break;
            }

            yield from $this->makeAggregation($accounts, $mtUsers);

            // stop iteration when chunk size lower than requested
            if (count($ids) < $chunk) {
                break;
            }
        }

        if ($this->scheduled) {
            $this->contextPayload()
                ->set('from', DateTime::now()->formatDb())
                ->set('chunk', $chunk);
        }
    }

    /**
     * @param array<int, Account> $accounts
     * @param array<int, MetatraderFullUserDto> $mtUsers
     * @return iterable<array-key, AggregateMtAccountDto>
     */
    private function makeAggregation(array $accounts, array $mtUsers) : iterable
    {
        foreach ($accounts as $login => $account) {
            $mtUser = $mtUsers[$login] ?? null;

            yield new AggregateMtAccountDto(
                account: $account,
                mtUser: $mtUser,
            );
        }
    }

    private function isSyncBalances() : bool
    {
        return is_null($this->option('syncBalances'))
            ? $this->contextPayload()->getBool('syncBalances')
            : $this->boolOption('syncBalances');
    }

    private function isSyncDeleting() : bool
    {
        return is_null($this->option('syncDeleting'))
            ? $this->contextPayload()->getBool('syncDeleting')
            : $this->boolOption('syncDeleting');
    }
}
