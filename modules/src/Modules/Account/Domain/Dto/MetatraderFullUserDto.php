<?php

declare(strict_types=1);

namespace Platform\Account\Domain\Dto;

class MetatraderFullUserDto
{
    public function __construct(
        public int    $login,
        public string $name,
        public string $group,
        public int    $leverage,
        public float  $balance,
        public float  $credit,
        public bool   $enabled,
        public bool   $tradable,
        public bool   $hasAccount,
        public float  $margin,
    ) {
    }
}
