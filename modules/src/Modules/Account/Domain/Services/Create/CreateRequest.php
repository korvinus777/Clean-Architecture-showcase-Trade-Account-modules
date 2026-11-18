<?php

declare(strict_types=1);

namespace Platform\Account\Domain\Services\Create;

use Platform\Account\Domain\Entities\SpecificGroupType;

/** @psalm-suppress PossiblyUnusedProperty */

class CreateRequest
{
    public function __construct(
        public int               $leadHashId,
        public string            $name,
        public string            $type,
        public SpecificGroupType $specificType,
        public string            $currency,
        public bool              $isDemo,
        public string            $mainPassword,
        public string            $investorPassword,
        public bool              $enabled,
        public bool              $tradable,
        public bool              $isStopOutCloseEnabled,
    ) {
    }
}
