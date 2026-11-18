<?php

declare(strict_types=1);

namespace Platform\Trade\Infrastructure\Models\Factories;

use Platform\Package\Components\String\Str;
use Platform\Package\Components\Time\DateTime;
use Platform\Package\Components\Collection\Arr;
use Illuminate\Database\Eloquent\Factories\Factory;
use Platform\Trade\Domain\ValueObjects\CloseReason;
use Platform\Trade\Infrastructure\Models\ClosingModel;

class ClosingFactory extends Factory
{
    protected $model = ClosingModel::class;

    /**
     * @return array
     * @psalm-suppress all
     */
    public function definition() : array
    {
        return [
            'tradeId'          => Str::formatContractSize($this->faker->randomFloat(4, 1, 1_000_000)),
            'mt5TransactionId' => $this->faker->randomNumber(),
            'lots'             => Str::formatLots($this->faker->randomFloat(8, 0, 99_999_999)),
            'amount'           => Str::formatAmount($this->faker->randomFloat(8, 0, 10_000_000)),
            'closePrice'       => Str::formatPrice($this->faker->randomFloat(12, 0)),
            'closeReason'      => Arr::random(CloseReason::cases())->value,
            'closeTime'        => DateTime::createFromTimestamp($this->faker->dateTime->getTimestamp()),
            'pnl'              => Str::formatPrice($this->faker->randomFloat(12, 0)),
            'pnlSystem'        => Str::formatPrice($this->faker->randomFloat(12, 0)),
            'commission'       => Str::formatPrice($this->faker->randomFloat(12, 0)),
            'swap'             => Str::formatMoney($this->faker->randomFloat(10, 0)),
            'swapSystem'       => Str::formatMoney($this->faker->randomFloat(10, 0)),
        ];
    }
}
