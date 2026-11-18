<?php

declare(strict_types=1);

namespace Platform\Account\Infrastructure\Models\Factories;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\Factory;
use Platform\Account\Infrastructure\Models\AccountModel;
use Platform\Account\Infrastructure\Models\AccountGroupModel;

/**
 * @method AccountModel create($attributes = [], ?Model $parent = null)
 */
class AccountFactory extends Factory
{
    protected $model = AccountModel::class;

    public function definition() : array
    {
        return [
            'login'            => $this->faker->randomNumber(5),
            'leverage'         => $this->faker->numberBetween(1, 1000),
            'isEnabled'        => $this->faker->boolean,
            'isTradable'       => $this->faker->boolean,
            'isSelected'       => false,
            'isMarginCall'     => false,
            'isStopOut'        => false,
            'balance'          => $this->faker->randomFloat(10, 10_000),
            'balanceSystem'    => $this->faker->randomFloat(10, 10_000),
            'credit'           => $this->faker->randomFloat(10, 10_000),
            'creditSystem'     => $this->faker->randomFloat(10, 10_000),
            'margin'           => $this->faker->randomFloat(10, 10_000),
            'marginSystem'     => $this->faker->randomFloat(10, 10_000),
            'isStopOutCloseEnabled' => true,
        ];
    }

    public function forLead(int $leadId, int $brandId) : self
    {
        /** @var AccountGroupModel $group */
        $group = AccountGroupModel::valid()
            ->inRandomOrder()
            ->firstWhere('brandId', $brandId);

        return $this->state(['groupId' => $group->id, 'leadId' => $leadId]);
    }
}
