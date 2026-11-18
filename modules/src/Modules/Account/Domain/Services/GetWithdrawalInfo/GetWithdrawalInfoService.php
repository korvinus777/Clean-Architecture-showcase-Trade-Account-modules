<?php

declare(strict_types=1);

namespace Platform\Account\Domain\Services\GetWithdrawalInfo;

use Platform\Package\Components\Collection\Arr;
use Platform\Account\Domain\Adapters\CrmAdapter;
use Platform\Account\Domain\Adapters\LeadAdapter;
use Platform\Package\Components\Log\Contracts\Logger;
use Platform\Account\Domain\Repositories\AccountRepository;

class GetWithdrawalInfoService implements GetWithdrawalInfoInput
{
    public function __construct(
        private Logger                  $logger,
        private CrmAdapter              $crmAdapter,
        private GetWithdrawalInfoOutput $output,
        private AccountRepository       $accountRepository,
        private LeadAdapter             $leadAdapter,
    ) {
    }

    public function get(GetWithdrawalInfoRequest $request) : GetWithdrawalInfoResult
    {
        $this->logger->withContext([
            'module'    => 'account',
            'service'   => class_basename(self::class),
            'accountId' => $request->accountId,
        ]);

        try {
            $account = $this->accountRepository->get($request->accountId);

            if ($this->leadAdapter->getById($account->leadId)->withdrawalBlocked) {
                return $this->output->withdrawalBlocked();
            }

            $wdInfoDto = $this->crmAdapter->getWithdrawalInfo($request->accountId);

            return $wdInfoDto
                ? $this->output->success($wdInfoDto)
                : $this->output->missingData();
        } catch (\Throwable $throwable) {
            $this->logger->critical('unexpected error', [
                'error' => Arr::formatThrowable($throwable),
            ]);
        }

        return $this->output->unexpectedError();
    }
}
