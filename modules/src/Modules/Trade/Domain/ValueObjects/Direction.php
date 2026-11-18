<?php

declare(strict_types=1);

namespace Platform\Trade\Domain\ValueObjects;

enum Direction : int
{
    case SELL = -1;
    case BUY  = 1;

    public function isBuy() : bool
    {
        return $this === self::BUY;
    }

    public function get() : int
    {
        return $this->isBuy() ? 0 : 1;
    }

    public static function convertTradeDirectionFromEngine(int $ntEngineDirection) : self
    {
        return $ntEngineDirection === 0 ? self::BUY : self::SELL;
    }
}
