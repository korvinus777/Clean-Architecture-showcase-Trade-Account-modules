<?php

declare(strict_types=1);

namespace Platform\Trade\Infrastructure\Listeners;

use Platform\Trade\Domain\Enums\RoutingKey;
use Platform\Package\Components\Collection\Payload;

class TradeClosedListener extends SendMessageListener
{
    public function routingKey() : string
    {
        return RoutingKey::TRADE_CLOSED->value;
    }

    protected function getMessage(Payload $payload) : string
    {
        return json_encode([
            'data' => [
                'tradeId'   => $payload->getInt('id'),
                'accountId' => $payload->getInt('accountId'),
                'createdAt' => $payload->getInt('createdAt'),
                'updatedAt' => $payload->getInt('updatedAt'),
            ],
            'timestamp' => (int) round(microtime(true) * 1000),
        ]);
    }
}
