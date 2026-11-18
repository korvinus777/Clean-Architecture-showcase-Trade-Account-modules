<?php

declare(strict_types=1);

namespace Platform\Account\Domain\Adapters;

use Platform\Package\Components\Time\DateTime;

interface TransactionAdapter
{
    /** @return int[] */
    public function getActiveAccountsAfter(DateTime $after, int $limit, int $page) : array;
}
