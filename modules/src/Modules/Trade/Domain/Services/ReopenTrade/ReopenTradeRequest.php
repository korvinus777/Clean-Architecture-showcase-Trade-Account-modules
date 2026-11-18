<?php

declare(strict_types=1);

namespace Platform\Trade\Domain\Services\ReopenTrade;

use Platform\Trade\Domain\Entities\ClosedTrade;
use Platform\Package\Components\Http\Request\Context;

class ReopenTradeRequest
{
    public function __construct(
        public ClosedTrade $closedTrade,
        public Context     $context,
    ) {
    }
}
