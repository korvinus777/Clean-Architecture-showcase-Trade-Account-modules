<?php

declare(strict_types=1);

namespace Platform\Account\Infrastructure\Listeners;

use Platform\Package\Components\Collection\Arr;
use Platform\Account\Domain\Entities\Notification;
use Platform\Package\Components\Collection\Payload;
use Platform\Package\Components\Log\Contracts\Logger;
use Platform\Account\Domain\Components\NotificationChannel as INotificationChannel;

abstract class SendMessageListener
{
    public function __construct(
        private Logger               $logger,
        private INotificationChannel $notificationChannel,
    ) {
    }

    /**
     * @param array $data
     * @return void
     */
    final public function handle(array $data) : void
    {
        $payload = new Payload($data);

        try {
            $this->notificationChannel->send(
                new Notification(
                    $this->routingKey(),
                    $payload->getInt('id'),
                    $payload->getInt('accountId'),
                    $payload->getInt('leadId'),
                    $payload->getBool('isClosed'),
                ),
            );
        } catch (\Throwable $throwable) {
            $this->logger->critical('unexpected error', [
                'module'  => 'account',
                'service' => class_basename(static::class),
                'error'   => Arr::formatThrowable($throwable),
                'payload' => $data,
            ]);
        }
    }

    /**
     * @return string
     */
    abstract public function routingKey() : string;
}
