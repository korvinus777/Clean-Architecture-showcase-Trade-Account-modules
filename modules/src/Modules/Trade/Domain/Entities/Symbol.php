<?php

declare(strict_types=1);

namespace Platform\Trade\Domain\Entities;

use Platform\Package\Components\Number\Number;

class Symbol
{
    private const TRADE_MODE_DISABLED = 0;

    public function __construct(
        public int    $id,
        public string $name,
        public string $displayName,
        public int    $precision,
        public float  $minVolume,
        public float  $maxVolume,
        public float  $stepVolume,
        public int    $tradeMode,
        public bool   $isMarketOpened,
        public float  $contractSize,
        public int    $profitCurrencyId = 0,
    ) {
    }

    public function isValidVolumeStep(float $volume) : bool
    {
        // объем не может быть меньше шага
        if ($volume < $this->stepVolume) {
            return false;
        }

        // объем должен быть кратен шагу
        if (Number::modulo($volume, $this->stepVolume) > 0) {
            return false;
        }

        return true;
    }

    public function isValidMinVolume(float $volume) : bool
    {
        return $this->minVolume <= $volume;
    }

    public function isValidMaxVolume(float $volume) : bool
    {
        return $volume <= $this->maxVolume;
    }

    public function isDisabled() : bool
    {
        return $this->tradeMode === self::TRADE_MODE_DISABLED;
    }

    public function amountFromLots(float $lots, ?float $contractSize = null) : float
    {
        return (float) number_format(
            num: $lots * ($contractSize ?? $this->contractSize),
            decimals: 8,
            thousands_separator: '',
        );
    }

    public function displayName() : string
    {
        return $this->displayName ?: $this->name;
    }
}
