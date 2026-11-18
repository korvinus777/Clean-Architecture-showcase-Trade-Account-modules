<?php

declare(strict_types=1);

namespace Platform\Account\Domain\Repositories;

interface SettingRepository
{
    public function defaultLeverage() : int;
}
