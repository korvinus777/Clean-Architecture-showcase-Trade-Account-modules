<?php

declare(strict_types=1);

namespace Platform\Trade\Domain\Services;

readonly class TurnoverResult
{
    public function __construct(
        public float $inAccountCurrency,
        public float $inSystemCurrency,
    ) {
    }

    public static function zero() : self
    {
        return new self(0.0, 0.0);
    }
}
