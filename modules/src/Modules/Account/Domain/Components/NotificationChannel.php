<?php

declare(strict_types=1);

namespace Platform\Account\Domain\Components;

use Platform\Account\Domain\Entities\Notification;

interface NotificationChannel
{
    public function send(Notification $notification) : void;
}
