<?php

declare(strict_types=1);

namespace Platform\Account\Domain\Services\Details;

use Platform\Account\Domain\Concerns\MakeAccountDto;
use Platform\Package\Components\Log\Contracts\Logger;
use Platform\Account\Domain\Repositories\AccountRepository;

class DetailsService implements DetailsInput
{
    use MakeAccountDto;

    public function __construct(
        private Logger            $logger,
        private DetailsOutput     $output,
        private AccountRepository $accountRepository,
    ) {
    }

    public function get(DetailsRequest $request) : DetailsResult
    {
        $this->logger->withContext([
            'module'    => 'account',
            'service'   => class_basename(self::class),
            'accountId' => $request->accountId,
        ]);

        $account = $this->accountRepository->find($request->accountId, true);

        if ( ! $account) {
            return $this->output->accountNotFound();
        }

        return $this->output->output($this->makeDto($account));
    }
}
