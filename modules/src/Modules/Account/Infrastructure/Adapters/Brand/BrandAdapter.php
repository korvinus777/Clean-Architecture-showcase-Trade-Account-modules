<?php

declare(strict_types=1);

namespace Platform\Account\Infrastructure\Adapters\Brand;

use Platform\Brand\Api\Api;
use Platform\Account\Domain\Adapters\BrandAdapter as IBrandAdapter;

class BrandAdapter implements IBrandAdapter
{
    public function __construct(
        private Api $api,
    ) {
    }

    public function findIdByName(string $name) : ?int
    {
        $brand = $this->api->findByName($name);

        return $brand?->id;
    }
}
