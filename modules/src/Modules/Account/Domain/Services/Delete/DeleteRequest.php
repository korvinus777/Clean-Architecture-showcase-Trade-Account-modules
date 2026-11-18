<?php

declare(strict_types=1);

namespace Platform\Account\Domain\Services\Delete;

class DeleteRequest
{
    public function __construct(
        public int  $accountId,
        public bool $isFromMt = false,
    ) {
    }
}
