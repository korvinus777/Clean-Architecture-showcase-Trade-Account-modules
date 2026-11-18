<?php

declare(strict_types=1);

namespace Platform\Account\Domain\Services\SetMarginCall;

interface SetMarginCallInput
{
    public function set(SetMarginCallRequest $request) : bool;
}
