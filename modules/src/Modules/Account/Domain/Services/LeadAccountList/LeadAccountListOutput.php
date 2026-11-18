<?php

declare(strict_types=1);

namespace Platform\Account\Domain\Services\LeadAccountList;

use Platform\Account\Domain\Dto\AccountDto;

interface LeadAccountListOutput
{
    /**
     * @param AccountDto[] $accountList
     */
    public function output(array $accountList) : LeadAccountListResult;

    public function leadNotFound() : LeadAccountListResult;
}
