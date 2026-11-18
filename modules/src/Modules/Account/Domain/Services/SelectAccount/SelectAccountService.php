<?php

declare(strict_types=1);

namespace Platform\Account\Domain\Services\SelectAccount;

use Platform\Package\Components\Collection\Arr;
use Platform\Account\Domain\Concerns\MakeAccountDto;
use Platform\Package\Components\Log\Contracts\Logger;
use Platform\Account\Domain\Repositories\AccountRepository;
use Platform\Package\Components\Database\Contracts\Transaction;

class SelectAccountService implements SelectAccountInput
{
    use MakeAccountDto;

    public function __construct(
        private SelectAccountOutput $output,
        private Logger              $logger,
        private Transaction         $transaction,
        private AccountRepository   $accountRepository,
    ) {
    }

    public function select(SelectAccountRequest $request) : SelectAccountResult
    {
        $this->logger->withContext([
            'module'    => 'account',
            'service'   => class_basename(self::class),
            'accountId' => $request->accountId,
            'leadId'    => $request->leadId,
        ]);

        $account = $this->accountRepository->find($request->accountId, true);

        if ( ! $account || $account->leadId !== $request->leadId) {
            return $this->output->notFound();
        }

        try {
            $this->transaction->forMain(function () use ($account, $request) {
                $this->accountRepository->unselectedAll($request->leadId);

                $selected = $this->accountRepository->select($account->id, $request->leadId);

                if ( ! $selected) {
                    throw new \Exception('Lead account not selected');
                }

                $account->selected = true;
            });

            return $this->output->selected($this->makeDto($account));
        } catch (\Throwable $throwable) {
            $this->logger->critical('unexpected error', [
                'error' => Arr::formatThrowable($throwable),
            ]);

            return $this->output->failed();
        }
    }
}
