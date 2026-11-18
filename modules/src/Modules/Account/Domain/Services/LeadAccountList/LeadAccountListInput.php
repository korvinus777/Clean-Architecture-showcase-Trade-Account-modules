<?php

declare(strict_types=1);

namespace Platform\Account\Domain\Services\LeadAccountList;

interface LeadAccountListInput
{
    public function get(LeadAccountListRequest $request) : LeadAccountListResult;
}
