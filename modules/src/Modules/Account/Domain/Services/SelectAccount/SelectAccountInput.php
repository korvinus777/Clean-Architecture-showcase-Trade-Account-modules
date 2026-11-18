<?php

declare(strict_types=1);

namespace Platform\Account\Domain\Services\SelectAccount;

interface SelectAccountInput
{
    public function select(SelectAccountRequest $request) : SelectAccountResult;
}
