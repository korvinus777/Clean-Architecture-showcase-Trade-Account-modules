<?php

declare(strict_types=1);

namespace Platform\Trade\Domain\Dto;

use Platform\Package\Components\Http\Response\Code;

enum NTResultCode : int
{
    // Success codes
    case OK                           = 0;
    case OK_NONE                      = 1;

    // Common errors
    case ERROR                        = 2;
    case ERR_REQUEST                  = 3;
    case ERR_VALIDATION               = 4;
    case ERR_REQUEST_LOGIN_MISSING    = 5;

    // Account errors (trading-related)
    case ACCOUNT_NOT_FOUND            = 1001;
    case ACCOUNT_INACTIVE             = 1002;

    // Trading session errors
    case TRADING_SESSION_CLOSED       = 3001;
    case TRADING_QUOTE_SESSION_CLOSED = 3002;

    // Order errors
    case ORDER_NOT_FOUND              = 4001;
    case ORDER_OPEN_ERROR             = 4002;
    case ORDER_MODIFY_ERROR           = 4003;
    case ORDER_CLOSE_ERROR            = 4004;
    case ORDER_INVALID_DIRECTION      = 4005;
    case ORDER_INVALID_PRICE          = 4006;
    case ORDER_INVALID_STOP_LOSS      = 4007;
    case ORDER_INVALID_TAKE_PROFIT    = 4008;
    case ORDER_INVALID_LOGIN          = 4009;

    // Position errors
    case POSITION_NOT_FOUND           = 5001;
    case POSITION_OPEN_ERROR          = 5002;
    case POSITION_MODIFY_ERROR        = 5003;
    case POSITION_CLOSE_ERROR         = 5004;
    case POSITION_REOPEN_FAILED       = 5005;
    case POSITION_ACCOUNT_INVALID     = 5006;
    case POSITION_INVALID_VOLUME_STEP = 5007;
    case POSITION_INVALID_VOLUME_MIN  = 5008;
    case POSITION_INVALID_VOLUME_MAX  = 5009;
    case POSITION_INVALID_STOP_LOSS   = 5010;
    case POSITION_INVALID_TAKE_PROFIT = 5011;
    case POSITION_INVALID_TIME_CREATE = 5012;

    // Symbol errors
    case SYMBOL_NOT_FOUND             = 7001;
    case SYMBOL_TICK_UNAVAILABLE      = 7002;

    // Margin errors
    case MARGIN_NOT_ENOUGH            = 9003;
    case EQUITY_NOT_ENOUGH            = 9005;

    public function isError() : bool
    {
        return ! in_array($this, [self::OK, self::OK_NONE], true);
    }

    public function getAppCode(Code $default) : Code
    {
        return match ($this) {
            // Order validation errors
            NTResultCode::ORDER_INVALID_STOP_LOSS      => Code::TRADE_INCORRECT_SL_TP,
            NTResultCode::ORDER_INVALID_TAKE_PROFIT    => Code::TRADE_INCORRECT_SL_TP,
            NTResultCode::ORDER_INVALID_PRICE          => Code::TRADE_PENDING_ORDER_INCORRECT_PRICE,
            NTResultCode::ORDER_INVALID_DIRECTION      => Code::TRADE_UNEXPECTED_ERROR,

            // Position validation errors
            NTResultCode::POSITION_INVALID_STOP_LOSS   => Code::TRADE_INCORRECT_SL_TP,
            NTResultCode::POSITION_INVALID_TAKE_PROFIT => Code::TRADE_INCORRECT_SL_TP,
            NTResultCode::POSITION_INVALID_VOLUME_STEP => Code::TRADE_INCORRECT_VOLUME_STEP,
            NTResultCode::POSITION_INVALID_VOLUME_MIN  => Code::TRADE_INCORRECT_VOLUME_LESS_MIN,
            NTResultCode::POSITION_INVALID_VOLUME_MAX  => Code::TRADE_INCORRECT_VOLUME_MORE_MAX,
            NTResultCode::POSITION_INVALID_TIME_CREATE => Code::TRADE_INCORRECT_TIME_CREATE,

            // Trading session errors
            NTResultCode::TRADING_SESSION_CLOSED       => Code::TRADE_MARKET_CLOSED,
            NTResultCode::TRADING_QUOTE_SESSION_CLOSED => Code::TRADE_MARKET_CLOSED,

            // Account errors
            NTResultCode::ACCOUNT_NOT_FOUND            => Code::TRADE_ACCOUNT_NOT_FOUND,
            NTResultCode::ACCOUNT_INACTIVE             => Code::TRADE_ACCOUNT_NOT_TRADABLE,

            // Symbol errors
            NTResultCode::SYMBOL_NOT_FOUND             => Code::TRADE_UNAVAILABLE_SYMBOL,
            NTResultCode::SYMBOL_TICK_UNAVAILABLE      => Code::TRADE_NO_PRICE,

            // Margin errors
            NTResultCode::MARGIN_NOT_ENOUGH            => Code::TRADE_NO_MONEY,
            NTResultCode::EQUITY_NOT_ENOUGH            => Code::TRADE_NO_MONEY,

            // Order operation errors
            NTResultCode::ORDER_OPEN_ERROR             => Code::TRADE_NT_ORDER_OPEN_ERROR,
            NTResultCode::ORDER_MODIFY_ERROR           => Code::TRADE_NT_ORDER_EDIT_ERROR,
            NTResultCode::ORDER_CLOSE_ERROR            => Code::TRADE_NT_ORDER_CLOSE_ERROR,

            // Position operation errors
            NTResultCode::POSITION_OPEN_ERROR          => Code::NT_TRADE_OPEN_ERROR,
            NTResultCode::POSITION_MODIFY_ERROR        => Code::NT_TRADE_EDIT_ERROR,
            NTResultCode::POSITION_CLOSE_ERROR         => Code::NT_TRADE_CLOSE_ERROR,
            NTResultCode::POSITION_REOPEN_FAILED       => Code::NT_TRADE_EDIT_ERROR,

            // Common validation errors
            NTResultCode::ERR_VALIDATION               => Code::TRADE_UNEXPECTED_ERROR,
            NTResultCode::ERR_REQUEST_LOGIN_MISSING    => Code::TRADE_ACCOUNT_NOT_FOUND,

            // Default fallback
            default                                    => $default,
        };
    }
}
