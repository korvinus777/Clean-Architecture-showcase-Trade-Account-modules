<?php

declare(strict_types=1);

namespace Platform\Account\Domain\Services\Details;

class DetailsRequest
{
    public function __construct(
        public int $accountId,
    ) {
    }
}
