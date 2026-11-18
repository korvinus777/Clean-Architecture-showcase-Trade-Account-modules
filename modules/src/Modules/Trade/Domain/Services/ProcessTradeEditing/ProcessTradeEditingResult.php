<?php

declare(strict_types=1);

namespace Platform\Trade\Domain\Services\ProcessTradeEditing;

use Platform\Trade\Domain\Entities\Trade;

class ProcessTradeEditingResult
{
    public function __construct(
        public ?Trade $trade,
    ) {
    }
}
