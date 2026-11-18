<?php

declare(strict_types=1);

namespace Platform\Account\Infrastructure\Listeners;

use Platform\Package\Components\Collection\Arr;
use Platform\Account\Domain\Adapters\MetatraderAdapter;
use Platform\Account\Domain\Repositories\AccountRepository;

class ToggleBlockAuthListener
{
    public function __construct(
        private MetatraderAdapter $metatraderAdapter,
        private AccountRepository $accountRepository,
    ) {
    }

    /**
     * @param array $payload
     * @return void
     * @throws \Exception
     */
    public function handle(array $payload) : void
    {
        $accounts = $this->accountRepository->findByLead(
            Arr::getIntOrFail($payload, 'id'),
        );

        if ( ! $accounts) {
            return;
        }

        $enabled = ! Arr::getBoolOrFail($payload, 'authBlocked');

        foreach ($accounts as $account) {
            $this->metatraderAdapter->deferredUpdateUser(
                login: $account->login,
                enabled: $enabled,
            );
        }
    }
}
