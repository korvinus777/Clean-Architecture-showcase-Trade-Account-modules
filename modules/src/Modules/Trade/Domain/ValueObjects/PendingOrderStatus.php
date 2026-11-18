<?php

declare(strict_types=1);

namespace Platform\Trade\Domain\ValueObjects;

enum PendingOrderStatus : int
{
    case PENDING   = 0;
    case ACTIVATED = 1;
    case CANCELED  = 2;

    public function isPending() : bool
    {
        return $this === self::PENDING;
    }
}
