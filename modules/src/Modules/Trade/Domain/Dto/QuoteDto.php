<?php

declare(strict_types=1);

namespace Platform\Trade\Domain\Dto;

class QuoteDto
{
    public function __construct(
        public int   $symbolId,
        public float $ask,
        public float $bid,
        public float $middle,
        public float $spread,
        public int   $createdTimestamp,
    ) {
    }
}
