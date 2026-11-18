<?php

declare(strict_types=1);

namespace Platform\Trade\Infrastructure\Models\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Platform\Trade\Infrastructure\Models\ClosingMetricModel;

class ClosingMetricFactory extends Factory
{
    protected $model = ClosingMetricModel::class;

    public function definition() : array
    {
        return [
            'mt5TransactionId' => $this->faker->randomNumber(),
            'requestedFromClient' => $this->faker->dateTime->getTimestamp() * 1000,
            'acceptedOnServer' => $this->faker->dateTime->getTimestamp() * 1000,
            'sentOnMt' => $this->faker->dateTime->getTimestamp() * 1000,
            'answeredFromMt' => $this->faker->dateTime->getTimestamp() * 1000,
            'closedOnMt' => $this->faker->dateTime->getTimestamp() * 1000,
            'acceptedEventOnServer' => $this->faker->dateTime->getTimestamp() * 1000,
            'closedOnServer' => $this->faker->dateTime->getTimestamp() * 1000,
        ];
    }
}
