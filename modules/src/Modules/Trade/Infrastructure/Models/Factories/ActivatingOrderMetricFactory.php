<?php

declare(strict_types=1);

namespace Platform\Trade\Infrastructure\Models\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Platform\Trade\Infrastructure\Models\ActivatingOrderMetricModel;

class ActivatingOrderMetricFactory extends Factory
{
    protected $model = ActivatingOrderMetricModel::class;

    public function definition() : array
    {
        return [
            'activatedOnMt' => $this->faker->dateTime->getTimestamp() * 1000,
            'acceptedEventOnServer' => $this->faker->dateTime->getTimestamp() * 1000,
            'activatedOnServer' => $this->faker->dateTime->getTimestamp() * 1000,
        ];
    }
}
