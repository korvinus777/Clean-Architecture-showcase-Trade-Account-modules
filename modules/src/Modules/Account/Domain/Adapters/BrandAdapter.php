<?php

declare(strict_types=1);

namespace Platform\Account\Domain\Adapters;

interface BrandAdapter
{
    public function findIdByName(string $name) : ?int;
}
