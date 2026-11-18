<?php

declare(strict_types=1);

namespace Platform\Account\Infrastructure\Models\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Platform\Account\Infrastructure\Models\AccountSettingModel;

class AccountSettingFactory extends Factory
{
    protected $model = AccountSettingModel::class;

    public function definition() : array
    {
        return [
            'name'  => $this->faker->name,
            'value' => $this->faker->randomNumber(),
        ];
    }
}
