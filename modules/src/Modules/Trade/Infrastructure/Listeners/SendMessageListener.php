<?php

declare(strict_types=1);

namespace Platform\Trade\Infrastructure\Listeners;

use Platform\Package\Components\Collection\Arr;
use Platform\Trade\Domain\Entities\Notification;
use Platform\Package\Components\Collection\Payload;
use Platform\Package\Components\Log\Contracts\Logger;
use Platform\Trade\Domain\Components\NotificationChannel as INotificationChannel;

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
                    $this->getMessage($payload),
                ),
            );
        } catch (\Throwable $throwable) {
            $this->logger->critical('unexpected error', [
                'module'  => 'trade',
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

    /**
     * @param Payload $payload
     * @return string
     */
    abstract protected function getMessage(Payload $payload) : string;
}
