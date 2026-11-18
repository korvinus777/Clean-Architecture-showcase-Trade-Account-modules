<?php

declare(strict_types=1);

namespace Platform\Account\Infrastructure\Models\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Platform\Account\Infrastructure\Models\AccountBalanceModel;

class AccountBalanceFactory extends Factory
{
    protected $model = AccountBalanceModel::class;

    public function definition() : array
    {
        return [
            'accountId'           => $this->faker->randomNumber(),
            'equity'              => $this->faker->randomFloat(10, 10_000),
            'equitySystem'        => $this->faker->randomFloat(10, 10_000),
            'openPnl'             => $this->faker->randomFloat(10, 10_000),
            'openPnlSystem'       => $this->faker->randomFloat(10, 10_000),
            'freeMargin'          => $this->faker->randomFloat(10, 10_000),
            'freeMarginSystem'    => $this->faker->randomFloat(10, 10_000),
            'marginLevel'         => $this->faker->randomNumber(),
            'availableWithdrawal' => $this->faker->randomFloat(10, 10_000),
        ];
    }
}
