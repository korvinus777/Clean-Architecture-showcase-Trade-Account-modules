<?php

declare(strict_types=1);

namespace Platform\Trade\Domain\Dto;

class AccountDto
{
    public function __construct(
        public int  $id,
        public int  $login,
        public int  $leadId,
        public int  $currencyId,
        public int  $groupId,
        public bool $isEnabled,
        public bool $isTradable,
    ) {
    }
}
