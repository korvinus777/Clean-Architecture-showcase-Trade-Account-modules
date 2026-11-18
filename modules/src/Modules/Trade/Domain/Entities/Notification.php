<?php

declare(strict_types=1);

namespace Platform\Trade\Domain\Entities;

class Notification
{
    public function __construct(
        public string $routingKey,
        public string $message,
    ) {
    }
}
