<?php

declare(strict_types=1);

namespace Platform\Account\Infrastructure\Adapters\Lead;

use Platform\Lead\Api\Api;
use Platform\Account\Domain\Dto\LeadDto;
use Platform\Lead\Api\Dto\LeadDto as ApiLeadDto;
use Platform\Account\Domain\Adapters\LeadAdapter as ILeadAdapter;

class LeadAdapter implements ILeadAdapter
{
    public function __construct(private Api $api)
    {
    }

    public function findByHashId(int $hashId) : ?LeadDto
    {
        $lead = $this->api->findByHashId($hashId);

        return $lead ? $this->makeDto($lead) : null;
    }

    public function getById(int $leadId) : LeadDto
    {
        $lead = $this->api->find($leadId);

        return $lead ? $this->makeDto($lead) : throw new \Exception("Lead [$leadId] not found");
    }

    private function makeDto(ApiLeadDto $lead) : LeadDto
    {
        return new LeadDto(
            id: $lead->id,
            hashId: $lead->hashId,
            brandId: $lead->brandId,
            withdrawalBlocked: $lead->withdrawalBlocked,
            name: $lead->name,
        );
    }
}
