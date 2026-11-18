<?php

declare(strict_types=1);

namespace Platform\Account\Domain\Dto;

class MetatraderUserDto
{
    public function __construct(
        public int    $login,
        public string $name,
        public string $group,
        public int    $leverage,
        public bool   $enabled,
        public bool   $tradable,
    ) {
    }
}
