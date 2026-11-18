<?php

declare(strict_types=1);

namespace Platform\Trade\Infrastructure\Adapters\Currency;

use Platform\Currency\Api\Api;
use Platform\Trade\Domain\Adapters\CurrencyAdapter as ICurrencyAdapter;

class CurrencyAdapter implements ICurrencyAdapter
{
    public function __construct(
        private Api $api,
    ) {
    }

    public function converseToSystem(float $amount, int $currencyId, int $groupId) : float
    {
        return $this->api->converseToSystem($amount, $currencyId, $groupId);
    }

    public function converseFromSystem(float $amount, int $currencyId, int $groupId) : float
    {
        return $this->api->converseFromSystem($amount, $currencyId, $groupId);
    }
}
