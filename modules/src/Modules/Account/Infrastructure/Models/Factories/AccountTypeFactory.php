<?php

declare(strict_types=1);

namespace Platform\Account\Infrastructure\Models\Factories;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\Factory;
use Platform\Account\Infrastructure\Models\AccountType;

/**
 * @method AccountType create($attributes = [], ?Model $parent = null)
 */
class AccountTypeFactory extends Factory
{
    protected $model = AccountType::class;

    public function definition() : array
    {
        return [
            'name' => $this->faker->firstNameFemale(),
        ];
    }
}
