<?php

declare(strict_types=1);

namespace Platform\Account\Infrastructure\Listeners;

use Platform\Account\Domain\Enums\RoutingKey;

class TradeClosedListener extends SendMessageListener
{
    /**
     * @return string
     */
    public function routingKey() : string
    {
        return RoutingKey::TRADE_CLOSED->value;
    }
}
