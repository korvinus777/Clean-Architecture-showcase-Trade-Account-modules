<?php

declare(strict_types=1);

namespace Platform\Account\Domain\Entities;

enum SpecificGroupType : int
{
    case REGULAR = 1;
    case CUSTOM  = 2;
    case MAM     = 3;
}
