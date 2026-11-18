<?php

declare(strict_types=1);

namespace Platform\Account\Infrastructure\Repositories;

use Platform\Account\Infrastructure\Models\AccountSettingModel;
use Platform\Account\Domain\Repositories\SettingRepository as ISettingRepository;

class SettingRepository implements ISettingRepository
{
    public function defaultLeverage() : int
    {
        return (int) AccountSettingModel::query()
            ->where('name', '=', 'Default leverage')
            ->value('value');
    }
}
