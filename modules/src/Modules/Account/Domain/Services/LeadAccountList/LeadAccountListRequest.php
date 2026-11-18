<?php

declare(strict_types=1);

namespace Platform\Account\Domain\Services\LeadAccountList;

class LeadAccountListRequest
{
    public function __construct(
        public int $leadId = 0,
        public int $leadHashId = 0,
    ) {
    }
}
