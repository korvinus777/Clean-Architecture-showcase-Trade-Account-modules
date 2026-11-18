<?php

declare(strict_types=1);

namespace Platform\Trade\App\Services\EditMtTrade;

use Platform\Trade\Domain\Entities\Trade;

class EditMtTradeResult
{
    public function __construct(
        public ?Trade $trade,
    ) {
    }
}
