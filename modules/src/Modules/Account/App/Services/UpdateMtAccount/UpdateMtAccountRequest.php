<?php

declare(strict_types=1);

namespace Platform\Account\App\Services\UpdateMtAccount;

class UpdateMtAccountRequest
{
    public function __construct(
        public int     $login,
        public ?string $groupName = null,
        public ?int    $leverage = null,
        public ?bool   $isEnabled = null,
        public ?bool   $isTradable = null,
        public ?float  $margin = null,
    ) {
    }
}
