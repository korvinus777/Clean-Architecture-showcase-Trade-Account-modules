<?php

declare(strict_types=1);

namespace Platform\Trade\App\Services\EditTrade;

class EditTradeResult
{
    public function __construct(
        public bool $success,
    ) {
    }
}
