<?php

declare(strict_types=1);

namespace Platform\Account\Domain\Services\SetStopOut;

interface SetStopOutInput
{
    public function set(SetStopOutRequest $request) : bool;
}
