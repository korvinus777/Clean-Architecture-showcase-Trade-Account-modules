<?php

declare(strict_types=1);

namespace Platform\Trade\Infrastructure\Models\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Platform\Trade\Infrastructure\Models\OpeningMetricModel;

class OpeningMetricFactory extends Factory
{
    protected $model = OpeningMetricModel::class;

    public function definition() : array
    {
        return [
            'requestedFromClient' => $this->faker->dateTime->getTimestamp() * 1000,
            'acceptedOnServer' => $this->faker->dateTime->getTimestamp() * 1000,
            'sentOnMt' => $this->faker->dateTime->getTimestamp() * 1000,
            'answeredFromMt' => $this->faker->dateTime->getTimestamp() * 1000,
            'openedOnMt' => $this->faker->dateTime->getTimestamp() * 1000,
            'acceptedEventOnServer' => $this->faker->dateTime->getTimestamp() * 1000,
            'openedOnServer' => $this->faker->dateTime->getTimestamp() * 1000,
        ];
    }
}
