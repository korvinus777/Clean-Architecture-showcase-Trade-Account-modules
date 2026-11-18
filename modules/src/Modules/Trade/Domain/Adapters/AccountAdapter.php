<?php

declare(strict_types=1);

namespace Platform\Trade\Domain\Adapters;

use Platform\Trade\Domain\Dto\AccountDto;

interface AccountAdapter
{
    public function findAccount(int $accountId) : ?AccountDto;

    public function findByLogin(int $login) : ?AccountDto;

    public function updateMargin(int $login, float $margin, bool $isFromMt = false) : bool;
}
