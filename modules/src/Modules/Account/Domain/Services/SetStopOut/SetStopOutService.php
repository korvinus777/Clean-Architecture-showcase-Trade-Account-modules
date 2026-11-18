<?php

declare(strict_types=1);

namespace Platform\Account\Domain\Services\SetStopOut;

use Platform\Package\Components\Collection\Arr;
use Platform\Package\Components\Log\Contracts\Logger;
use Platform\Account\Domain\Repositories\AccountRepository;
use Platform\Package\Components\Events\Contracts\Dispatcher;

class SetStopOutService implements SetStopOutInput
{
    public function __construct(
        private Logger            $logger,
        private Dispatcher        $dispatcher,
        private AccountRepository $accountRepository,
    ) {
    }

    public function set(SetStopOutRequest $request) : bool
    {
        $this->logger->withContext([
            'module'    => 'account',
            'service'   => class_basename(self::class),
            'accountId' => $request->accountId,
            'isEnter'   => $request->isEnter,
        ]);

        try {
            $isSuccess = $this->accountRepository->setStopOut(
                $request->accountId,
                $request->isEnter,
            );

            if ( ! $isSuccess) {
                $this->logger->info('account already updated');
            }

            $this->dispatcher->dispatch(
                $request->isEnter ? 'account.stop-out.enter' : 'account.stop-out.leave',
                [['accountId' => $request->accountId]],
            );
        } catch (\Throwable $throwable) {
            $this->logger->critical('unexpected error', [
                'error' => Arr::formatThrowable($throwable),
            ]);

            return false;
        }

        return true;
    }
}
