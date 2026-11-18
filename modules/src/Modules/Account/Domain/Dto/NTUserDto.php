<?php

declare(strict_types=1);

namespace Platform\Account\Domain\Dto;

class NTUserDto
{
    public function __construct(
        public int $login,
    ) {
    }
}
