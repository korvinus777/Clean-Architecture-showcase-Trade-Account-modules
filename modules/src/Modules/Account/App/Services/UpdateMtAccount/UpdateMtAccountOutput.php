<?php

declare(strict_types=1);

namespace Platform\Account\App\Services\UpdateMtAccount;

interface UpdateMtAccountOutput
{
    public function success() : UpdateMtAccountResult;

    public function accountNotFound() : UpdateMtAccountResult;

    public function groupNotFound() : UpdateMtAccountResult;

    public function canNotUpdate() : UpdateMtAccountResult;
}
