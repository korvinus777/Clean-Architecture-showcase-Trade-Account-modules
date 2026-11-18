<?php

declare(strict_types=1);

namespace Platform\Trade\App\Services\EditMtPendingOrder;

use Platform\Trade\Domain\Entities\PendingOrder;

class EditMtPendingOrderResult
{
    public function __construct(
        public ?PendingOrder $order = null,
    ) {
    }
}
