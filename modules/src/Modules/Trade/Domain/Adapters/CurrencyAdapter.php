<?php

declare(strict_types=1);

namespace Platform\Trade\Domain\Adapters;

interface CurrencyAdapter
{
    public function converseToSystem(float $amount, int $currencyId, int $groupId) : float;

    public function converseFromSystem(float $amount, int $currencyId, int $groupId) : float;
}
