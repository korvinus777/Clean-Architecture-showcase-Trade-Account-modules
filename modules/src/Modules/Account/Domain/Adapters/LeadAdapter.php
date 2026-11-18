<?php

declare(strict_types=1);

namespace Platform\Account\Domain\Adapters;

use Platform\Account\Domain\Dto\LeadDto;

interface LeadAdapter
{
    public function findByHashId(int $hashId) : ?LeadDto;

    public function getById(int $leadId) : LeadDto;
}
