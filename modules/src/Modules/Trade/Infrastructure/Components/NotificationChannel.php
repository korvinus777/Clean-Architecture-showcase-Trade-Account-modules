<?php

declare(strict_types=1);

namespace Platform\Trade\Infrastructure\Components;

use Platform\Trade\Domain\Entities\Notification;
use Platform\Package\Components\Queue\Connection;
use Platform\Trade\Domain\Components\NotificationChannel as INotificationChannel;

class NotificationChannel implements INotificationChannel
{
    public const DESTINATION = 'platform.trade.exchange';

    public function __construct(
        private Connection $connection,
    ) {
    }

    /**
     * @param Notification $notification
     * @return void
     */
    public function send(Notification $notification) : void
    {
        $this->ensureConnectionOpen();

        $this->connection->push($notification->message, self::DESTINATION, [
            'routing_key' => $notification->routingKey,
            'json'        => true,
            'persistent'  => true,
        ]);
    }

    /**
     * @return void
     */
    private function ensureConnectionOpen() : void
    {
        if ($this->connection->isLive()) {
            return;
        }

        $this->connection->connect();
    }
}
