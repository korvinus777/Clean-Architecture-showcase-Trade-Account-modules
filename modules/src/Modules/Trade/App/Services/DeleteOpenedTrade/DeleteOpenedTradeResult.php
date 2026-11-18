<?php

declare(strict_types=1);

namespace Platform\Trade\App\Services\DeleteOpenedTrade;

class DeleteOpenedTradeResult
{
    public function __construct(
        public bool $deleted = false,
        public bool $notFound = false,
        public bool $alreadyClosed = false,
    ) {
    }
}
