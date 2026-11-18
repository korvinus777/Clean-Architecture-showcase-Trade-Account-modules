<?php

declare(strict_types=1);

namespace Platform\Account\App\Outputs\UpdateMtAccount;

use Platform\Account\App\Services\UpdateMtAccount\UpdateMtAccountResult;
use Platform\Account\App\Services\UpdateMtAccount\UpdateMtAccountOutput as IUpdateMtAccountOutput;

class UpdateMtAccountOutput implements IUpdateMtAccountOutput
{
    public function success() : UpdateMtAccountResult
    {
        return new UpdateMtAccountResult(true);
    }

    public function groupNotFound() : UpdateMtAccountResult
    {
        return new UpdateMtAccountResult();
    }

    public function accountNotFound() : UpdateMtAccountResult
    {
        return new UpdateMtAccountResult();
    }

    public function canNotUpdate() : UpdateMtAccountResult
    {
        return new UpdateMtAccountResult();
    }
}
