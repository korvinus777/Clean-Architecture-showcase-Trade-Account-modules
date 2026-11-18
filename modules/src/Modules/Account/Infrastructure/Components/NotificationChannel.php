<?php

declare(strict_types=1);

namespace Platform\Account\Infrastructure\Components;

use Platform\Package\Components\Queue\Connection;
use Platform\Account\Domain\Entities\Notification;
use Platform\Account\Domain\Components\NotificationChannel as INotificationChannel;

class NotificationChannel implements INotificationChannel
{
    public const DESTINATION = 'account_notifications';

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

        $this->connection->push($this->format($notification), self::DESTINATION, [
            'routing_key' => $notification->routingKey,
            'json'        => true,
            'persistent'  => true,
        ]);
    }

    /**
     * @param Notification $notification
     * @return bool|string
     */
    private function format(Notification $notification) : string
    {
        return json_encode([
            'tradeId'   => $notification->tradeId,
            'data'      => [
                'leadId'    => $notification->leadId,
                'accountId' => $notification->accountId,
                'isClosed'  => $notification->isClosed,
            ],
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
