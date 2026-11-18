<?php

declare(strict_types=1);

namespace Platform\Account\App\Outputs\LeadAccountList\Results;

use Platform\Account\Domain\Dto\AccountDto;
use Platform\Package\Components\Http\Response\Concerns\HttpJsonResult;
use Platform\Account\Domain\Services\LeadAccountList\LeadAccountListResult;

class LeadAccountListJsonResult extends LeadAccountListResult
{
    use HttpJsonResult;

    public function getData() : array
    {
        return array_map(fn (AccountDto $account) => $account->toArray(), $this->accountList);
    }
}
