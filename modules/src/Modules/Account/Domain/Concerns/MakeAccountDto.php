<?php

declare(strict_types=1);

namespace Platform\Account\Domain\Concerns;

use Platform\Account\Domain\Dto\AccountDto;
use Platform\Account\Domain\Entities\Account;
use Platform\Account\Domain\Adapters\CurrencyAdapter;
use Platform\Package\Components\Log\Contracts\Logger;
use Platform\Account\Domain\Repositories\AccountRepository;
use Platform\Account\Domain\Repositories\AccountStatisticsRepository;
use Platform\Package\Components\Container\Concerns\DependencyResolver;

trait MakeAccountDto
{
    use DependencyResolver;

    public function makeDto(Account $account) : AccountDto
    {
        $logger = $this->resolve(Logger::class);

        $logger->withContext([
            'module'    => 'account',
            'service'   => class_basename(static::class),
            'accountId' => $account->id,
        ]);

        $balanceDto = $this->resolve(AccountRepository::class)->findBalances($account->id);
        $statisticsDto = $this->resolve(AccountStatisticsRepository::class)->findByAccountId($account->id);

        if ( ! $account->group) {
            $logger->critical('group not found', [
                'groupId' => $account->groupId,
                'reason'  => 'finding group of account',
            ]);

            return AccountDto::fromEntity($account, $balanceDto, null, $statisticsDto);
        }

        $currencyDto = $this->resolve(CurrencyAdapter::class)->find($account->group->currencyId);

        if ( ! $currencyDto) {
            $logger->critical('currency not found', [
                'groupId'    => $account->group->id,
                'currencyId' => $account->group->currencyId,
                'reason'     => 'finding currency of group',
            ]);
        }

        return AccountDto::fromEntity($account, $balanceDto, $currencyDto, $statisticsDto);
    }
}
