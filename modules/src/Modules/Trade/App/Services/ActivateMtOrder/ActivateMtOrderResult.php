<?php

declare(strict_types=1);

namespace Platform\Trade\App\Services\ActivateMtOrder;

use Platform\Trade\Domain\Entities\Trade;

class ActivateMtOrderResult
{
    public function __construct(
        public ?Trade $trade = null,
        public bool   $alreadyProcessed = false,
    ) {
    }
}
