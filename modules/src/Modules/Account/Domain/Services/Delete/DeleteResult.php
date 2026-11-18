<?php

declare(strict_types=1);

namespace Platform\Account\Domain\Services\Delete;

use Platform\Account\Domain\Entities\Account;

class DeleteResult
{
    public function __construct(
        public ?\Throwable $throwable = null,
        public ?Account    $account = null,
        public ?int        $accountId = null,
        public ?int        $login = null,
    ) {
    }

    public function isDeleted() : bool
    {
        return $this->account && ! $this->throwable;
    }
}
