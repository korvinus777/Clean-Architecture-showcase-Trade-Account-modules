<?php

declare(strict_types=1);

namespace Platform\Trade\Domain\Dto;

use Platform\Package\Components\Http\Response\Code;

enum MTResultCode : int
{
    case OK                 = 0;
    case OK_NONE            = 1;
    case ERROR              = 2;
    case INVALID_PARAMS     = 3;
    case REQUEST_REJECTED   = 10006;
    case MT_REQUEST_INVALID = 10013;
    case INVALID_VOLUME     = 10014;
    case INVALID_PRICE      = 10015;
    case INVALID_STOPS      = 10016;
    case TRADE_DISABLED     = 10017;
    case MARKET_CLOSED      = 10018;
    case NO_MONEY           = 10019;
    case NO_PRICE           = 10021;
    case ALREADY_CLOSED     = 10036;
    case LONG_ONLY          = 10042;
    case SHORT_ONLY         = 10043;
    case CLOSE_ONLY         = 10044;

    public function isError() : bool
    {
        return ! in_array($this, [self::OK, self::OK_NONE], true);
    }

    public function getAppCode(Code $default) : Code
    {
        return match ($this) {
            MTResultCode::INVALID_STOPS  => Code::TRADE_INCORRECT_SL_TP,
            MTResultCode::LONG_ONLY      => Code::TRADE_BUY_ONLY,
            MTResultCode::SHORT_ONLY     => Code::TRADE_SELL_ONLY,
            MTResultCode::CLOSE_ONLY     => Code::TRADE_CLOSE_ONLY,
            MTResultCode::TRADE_DISABLED => Code::TRADE_DISABLED_SYMBOL,
            MTResultCode::MARKET_CLOSED  => Code::TRADE_MARKET_CLOSED,
            MTResultCode::NO_MONEY       => Code::TRADE_NO_MONEY,
            MTResultCode::NO_PRICE       => Code::TRADE_NO_PRICE,
            MTResultCode::INVALID_VOLUME => Code::TRADE_INCORRECT_VOLUME,
            MTResultCode::ALREADY_CLOSED => Code::TRADE_ALREADY_CLOSED,
            MTResultCode::INVALID_PRICE  => Code::TRADE_PENDING_ORDER_INCORRECT_PRICE,
            default                      => $default,
        };
    }
}
