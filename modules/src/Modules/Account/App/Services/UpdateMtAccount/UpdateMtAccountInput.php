<?php

declare(strict_types=1);

namespace Platform\Account\App\Services\UpdateMtAccount;

interface UpdateMtAccountInput
{
    public function update(UpdateMtAccountRequest $request) : UpdateMtAccountResult;
}
