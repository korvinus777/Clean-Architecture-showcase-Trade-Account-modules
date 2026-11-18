<?php

declare(strict_types=1);

namespace Platform\Trade\Infrastructure\Models\Factories;

use Illuminate\Database\Eloquent\Model;
use Platform\Package\Components\String\Str;
use Platform\Package\Components\Time\DateTime;
use Platform\Package\Components\Collection\Arr;
use Platform\Trade\Domain\ValueObjects\Direction;
use Illuminate\Database\Eloquent\Factories\Factory;
use Platform\Trade\Infrastructure\Models\TradeModel;

/**
 * @method TradeModel create($attributes = [], ?Model $parent = null)
 */
class TradeFactory extends Factory
{
    protected $model = TradeModel::class;

    /**
     * @return array
     * @psalm-suppress all
     */
    public function definition() : array
    {
        $lots = Str::formatLots($this->faker->randomFloat(8, 0, 99_999_999));
        $amount = Str::formatAmount($this->faker->randomFloat(8, 0, 10_000_000));

        return [
            'contractSize' => Str::formatContractSize($this->faker->randomFloat(4, 1, 1_000_000)),
            'lots'         => $lots,
            'lotsLeft'     => $lots,
            'amount'       => $amount,
            'amountLeft'   => $amount,
            'openPrice'    => Str::formatPrice($this->faker->randomFloat(12, 0)),
            'openTime'     => DateTime::createFromTimestampUTC(
                $this->faker->dateTime->getTimestamp(),
            ),
            'takeProfit'   => Str::formatPrice($this->faker->randomFloat(12, 0)),
            'stopLoss'     => Str::formatPrice($this->faker->randomFloat(12, 0)),
            'swap'         => Str::formatMoney($this->faker->randomFloat(10, 0)),
            'swapSystem'   => Str::formatMoney($this->faker->randomFloat(10, 0)),
            'direction'    => Arr::random(Direction::cases())->value,
            'isClosed'     => false,
        ];
    }
}
