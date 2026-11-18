<?php

declare(strict_types=1);

namespace Platform\Account\Domain\Enums;

enum RoutingKey : string
{
    case TRADE_OPENED = 'eventer.ITradeSink.OnTradeOpened';
    case TRADE_CLOSED = 'eventer.ITradeSink.OnTradeClosed';
}
