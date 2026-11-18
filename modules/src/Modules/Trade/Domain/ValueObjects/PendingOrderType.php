<?php

declare(strict_types=1);

namespace Platform\Trade\Domain\ValueObjects;

enum PendingOrderType : int
{
    case BUY_LIMIT       = 2; // Ордер Buy Limit
    case SELL_LIMIT      = 3; // Ордер Sell Limit
    case BUY_STOP        = 4; // Ордер Buy Stop
    case SELL_STOP       = 5; // Ордер Sell Stop
    case BUY_STOP_LIMIT  = 6; // Ордер Buy Stop Limit
    case SELL_STOP_LIMIT = 7; // Ордер Sell Stop Limit

    public function isBuy() : bool
    {
        return in_array($this, [self::BUY_STOP, self::BUY_LIMIT, self::BUY_STOP_LIMIT], true);
    }

    public function isSell() : bool
    {
        return in_array($this, [self::SELL_STOP, self::SELL_LIMIT, self::SELL_STOP_LIMIT], true);
    }
}
