<?php

declare(strict_types=1);

namespace Platform\Account\Infrastructure\Listeners;

use Platform\Account\Domain\Enums\RoutingKey;

class TradeOpenedListener extends SendMessageListener
{
    /**
     * @return string
     */
    public function routingKey() : string
    {
        return RoutingKey::TRADE_OPENED->value;
    }
}
