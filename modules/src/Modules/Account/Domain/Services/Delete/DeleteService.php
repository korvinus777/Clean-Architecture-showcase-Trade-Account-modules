<?php

declare(strict_types=1);

namespace Platform\Account\Domain\Services\Delete;

use Platform\Package\Components\Collection\Arr;
use Platform\Package\Components\Events\Dispatcher;
use Platform\Package\Components\Log\Contracts\Logger;
use Platform\Account\Domain\Adapters\MetatraderAdapter;
use Platform\Account\Domain\Repositories\AccountRepository;

class DeleteService implements DeleteInput
{
    public function __construct(
        private Logger            $logger,
        private Dispatcher        $dispatcher,
        private DeleteOutput      $output,
        private MetatraderAdapter $metatraderAdapter,
        private AccountRepository $accountRepository,
    ) {
    }

    public function delete(DeleteRequest $request) : DeleteResult
    {
        $this->logger->withContext([
            'module'    => 'account',
            'service'   => class_basename(self::class),
            'accountId' => $request->accountId,
        ]);

        $account = $this->accountRepository->find($request->accountId);

        if ( ! $account) {
            $this->logger->error('account not found');

            return $this->output->accountNotFound($request->accountId);
        }

        if ( ! $request->isFromMt && ! $this->metatraderAdapter->deleteUser($account->login)) {
            $this->logger->error('can not delete account on mt', ['login' => $account->login]);

            return $this->output->canNotDeleteOnMt($account->login);
        }

        try {
            $this->accountRepository->delete($account->id);
        } catch (\Throwable $throwable) {
            $this->logger->critical('unexpected error', ['error' => Arr::formatThrowable($throwable)]);

            return $this->output->canNotDeleteFromDb($account, $throwable);
        }

        $this->dispatcher->dispatch('account.deleted', [$account->toArray()]);

        return $this->output->deleted($account);
    }
}
