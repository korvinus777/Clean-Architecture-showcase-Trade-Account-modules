<?php

declare(strict_types=1);

namespace Platform\Account\App\Services\SyncMtAccount;

use Platform\Account\App\Dto\AggregateMtAccountDto;

class SyncMtAccountRequest
{
    public function __construct(
        public AggregateMtAccountDto $aggregate,
        public bool                  $syncBalances,
        public bool                  $syncDeleting,
    ) {
    }
}
