<?php

declare(strict_types=1);

namespace Platform\Account\Infrastructure\Adapters\Currency;

use Platform\Currency\Api\Api;
use Platform\Account\Domain\Dto\CurrencyDto;
use Platform\Currency\Api\Dto\CurrencyDto as ApiCurrencyDto;
use Platform\Account\Domain\Adapters\CurrencyAdapter as ICurrencyAdapter;

class CurrencyAdapter implements ICurrencyAdapter
{
    public function __construct(private Api $api)
    {
    }

    public function find(int $currencyId) : ?CurrencyDto
    {
        $currency = $this->api->find($currencyId);

        return $currency ? $this->makeDto($currency) : null;
    }

    public function findIdByName(string $name) : ?int
    {
        $currency = $this->api->findByName($name);

        return $currency?->id;
    }

    public function converseToSystem(float $amount, int $fromCurrencyId, int $groupId) : float
    {
        return $this->api->converseToSystem($amount, $fromCurrencyId, $groupId);
    }

    public function findOrCreate(string $name, int $precision) : CurrencyDto
    {
        if ($currency = $this->api->findByName($name)) {
            return $this->makeDto($currency);
        }

        $currency = $this->api->create(
            name: $name,
            sign: '',
            precision: $precision,
        );

        return $this->makeDto($currency);
    }

    private function makeDto(ApiCurrencyDto $currency) : CurrencyDto
    {
        return new CurrencyDto(
            $currency->id,
            $currency->name,
            $currency->sign,
            $currency->precision,
        );
    }
}
