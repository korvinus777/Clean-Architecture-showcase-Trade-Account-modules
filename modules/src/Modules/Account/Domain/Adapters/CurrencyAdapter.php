<?php

declare(strict_types=1);

namespace Platform\Account\Domain\Adapters;

use Platform\Account\Domain\Dto\CurrencyDto;

interface CurrencyAdapter
{
    public function find(int $currencyId) : ?CurrencyDto;

    public function findIdByName(string $name) : ?int;

    public function converseToSystem(float $amount, int $fromCurrencyId, int $groupId) : float;

    public function findOrCreate(string $name, int $precision) : CurrencyDto;
}
