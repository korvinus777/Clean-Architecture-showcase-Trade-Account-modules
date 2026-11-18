<?php

declare(strict_types=1);

namespace Platform\Trade\Domain\Components;

use Platform\Trade\Domain\Entities\Notification;

interface NotificationChannel
{
    public function send(Notification $notification) : void;
}
