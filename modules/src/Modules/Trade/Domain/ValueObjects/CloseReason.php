<?php

declare(strict_types=1);

namespace Platform\Trade\Domain\ValueObjects;

enum CloseReason : int
{
    case DEFAULT     = 0;
    case USER        = 1;
    case STOP_OUT    = 2;
    case STOP_LOSS   = 3;
    case TAKE_PROFIT = 4;
    case DEALER      = 5;

    public static function fromDealReason(int $dealReason) : self
    {
        return match ($dealReason) {
            0, 1, 2 => self::USER,
            3       => self::STOP_LOSS,
            4       => self::TAKE_PROFIT,
            5       => self::STOP_OUT,
            18      => self::DEALER,
            default => self::USER,
        };
    }

    public static function fromNativeEngineReason(int $nativeReason) : self
    {
        return match ($nativeReason) {
            1 => self::USER,        // CloseReasonByUser - Manual Close
            2 => self::STOP_LOSS,   // CloseReasonStopLoss
            3 => self::TAKE_PROFIT, // CloseReasonTakeProfit
            5 => self::STOP_OUT,    // CloseReasonStopOut
            6 => self::DEALER,        // CloseReasonSystem
            default => self::USER,  // CloseReasonUnknown or other
        };
    }
}
