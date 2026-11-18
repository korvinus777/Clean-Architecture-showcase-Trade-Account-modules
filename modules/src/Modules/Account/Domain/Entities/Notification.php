<?php

declare(strict_types=1);

namespace Platform\Account\Domain\Entities;

class Notification
{
    public function __construct(
        public string   $routingKey,
        public int      $tradeId,
        public int      $accountId,
        public int      $leadId,
        public bool     $isClosed,
    ) {
    }
}
