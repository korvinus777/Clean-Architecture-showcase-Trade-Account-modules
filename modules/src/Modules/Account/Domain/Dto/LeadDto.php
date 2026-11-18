<?php

declare(strict_types=1);

namespace Platform\Account\Domain\Dto;

class LeadDto
{
    public function __construct(
        public int  $id,
        public int  $hashId,
        public int  $brandId,
        public bool $withdrawalBlocked,
        public string $name,
    ) {
    }
}
