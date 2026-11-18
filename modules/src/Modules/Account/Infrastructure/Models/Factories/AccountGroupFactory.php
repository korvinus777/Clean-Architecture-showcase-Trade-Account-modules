<?php

declare(strict_types=1);

namespace Platform\Account\Infrastructure\Models\Factories;

use Illuminate\Support\Str;
use Platform\Package\Components\Collection\Arr;
use Illuminate\Database\Eloquent\Factories\Factory;
use Platform\Account\Domain\Entities\SpecificGroupType;
use Platform\Account\Infrastructure\Models\AccountGroupModel;

class AccountGroupFactory extends Factory
{
    protected $model = AccountGroupModel::class;

    public function definition() : array
    {
        return [
            'name'           => Str::random(),
            'isDemo'         => $this->faker->boolean,
            'specificTypeId' => Arr::random(SpecificGroupType::cases()),
        ];
    }
}
