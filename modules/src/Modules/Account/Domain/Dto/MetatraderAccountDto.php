<?php

declare(strict_types=1);

namespace Platform\Account\Domain\Dto;

class MetatraderAccountDto
{
    public function __construct(
        public int   $login,
        public float $balance,
        public float $credit,
        public float $margin,
        public int   $leverage,
    ) {
    }
}
