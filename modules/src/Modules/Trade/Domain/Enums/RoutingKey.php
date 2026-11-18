<?php

declare(strict_types=1);

namespace Platform\Trade\Domain\Enums;

enum RoutingKey : string
{
    case TRADE_CLOSED = 'trade.closed';
    case TRADE_OPENED = 'trade.opened';
    case TRADE_EDITED = 'trade.edited';
    case TRADE_REOPENED = 'trade.reopened';
}
