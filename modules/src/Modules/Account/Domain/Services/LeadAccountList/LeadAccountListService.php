<?php

declare(strict_types=1);

namespace Platform\Account\Domain\Services\LeadAccountList;

use Platform\Account\Domain\Dto\AccountDto;
use Platform\Account\Domain\Entities\Account;
use Platform\Account\Domain\Adapters\LeadAdapter;
use Platform\Account\Domain\Concerns\MakeAccountDto;
use Platform\Package\Components\Log\Contracts\Logger;
use Platform\Account\Domain\Repositories\AccountRepository;

class LeadAccountListService implements LeadAccountListInput
{
    use MakeAccountDto;

    public function __construct(
        private Logger                $logger,
        private LeadAccountListOutput $output,
        private AccountRepository     $accountRepository,
        private LeadAdapter           $leadAdapter,
    ) {
    }

    public function get(LeadAccountListRequest $request) : LeadAccountListResult
    {
        $this->logger->withContext([
            'module'     => 'account',
            'service'    => class_basename(self::class),
            'leadHashId' => $request->leadHashId,
        ]);

        if ( ! $leadId = $this->getLeadId($request)) {
            return $this->output->leadNotFound();
        }

        $accounts = $this->accountRepository->findByLead($leadId, true);

        return $this->output->output(
            $this->buildAccountList($accounts),
        );
    }

    /**
     * @param Account[] $accounts
     * @return AccountDto[]
     */
    private function buildAccountList(array $accounts) : array
    {
        return array_map(fn (Account $account) => $this->makeDto($account), $accounts);
    }

    private function getLeadId(LeadAccountListRequest $request) : ?int
    {
        if ($request->leadId) {
            return $request->leadId;
        }

        $leadDto = $this->leadAdapter->findByHashId($request->leadHashId);

        return $leadDto->id ?? null;
    }
}
