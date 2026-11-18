<?php

declare(strict_types=1);

namespace Platform\Trade\Infrastructure\Adapters\Account;

use Platform\Account\Api\Api;
use Platform\Trade\Domain\Dto\AccountDto;
use Platform\Account\Api\Dto\AccountDto as ApiAccountDto;

class AccountAdapter implements \Platform\Trade\Domain\Adapters\AccountAdapter
{
    public function __construct(
        private Api $api,
    ) {
    }

    public function findAccount(int $accountId) : ?AccountDto
    {
        $account = $this->api->getById($accountId);

        return $account ? $this->makeDto($account) : null;
    }

    public function findByLogin(int $login) : ?AccountDto
    {
        $account = $this->api->findByLogin($login);

        return $account ? $this->makeDto($account) : null;
    }

    public function updateMargin(int $login, float $margin, bool $isFromMt = false) : bool
    {
        return $this->api->updateMargin($login, $margin, $isFromMt);
    }

    private function makeDto(ApiAccountDto $account) : AccountDto
    {
        return new AccountDto(
            $account->accountId,
            $account->login,
            $account->leadId,
            $account->currencyId,
            $account->groupId,
            $account->enabled,
            $account->tradable,
        );
    }
}
