<?php

declare(strict_types=1);

namespace Platform\Account\Domain\ValueObjects;

class AccountValueObject
{
    public function __construct(
        public ?int  $leadId = null,
        public ?int  $login = null,
        public ?int  $groupId = null,
        public ?int  $leverage = null,
        public ?bool $enabled = null,
        public ?bool $tradable = null,
        public ?bool $isStopOutCloseEnabled = null,
    ) {
    }
}
