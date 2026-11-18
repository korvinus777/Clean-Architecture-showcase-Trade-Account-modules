<?php

declare(strict_types=1);

namespace Platform\Trade\Infrastructure\Models\Factories;

use Platform\Package\Components\String\Str;
use Platform\Package\Components\Collection\Arr;
use Illuminate\Database\Eloquent\Factories\Factory;
use Platform\Trade\Domain\ValueObjects\PendingOrderType;
use Platform\Trade\Domain\ValueObjects\PendingOrderStatus;
use Platform\Trade\Infrastructure\Models\PendingOrderModel;

/**
 * @psalm-suppress UnusedClass
 */
class PendingOrderFactory extends Factory
{
    protected $model = PendingOrderModel::class;

    /**
     * @return array
     * @psalm-suppress all
     */
    public function definition() : array
    {
        return [
            'lots'           => Str::formatLots($this->faker->randomFloat(8, 0, 100_000)),
            'price'          => Str::formatPrice($this->faker->randomFloat(12, 0)),
            'stopLimitPrice' => 0.,
            'takeProfit'     => Str::formatPrice($this->faker->randomFloat(12, 0)),
            'stopLoss'       => Str::formatPrice($this->faker->randomFloat(12, 0)),
            'type'           => Arr::random(PendingOrderType::cases())->value,
            'status'         => Arr::random(PendingOrderStatus::cases())->value,
            'openTime'       => $this->faker->dateTime->format('Y-m-d H:i:s'),
        ];
    }
}
