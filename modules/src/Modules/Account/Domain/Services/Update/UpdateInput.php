<?php

declare(strict_types=1);

namespace Platform\Account\Domain\Services\Update;

interface UpdateInput
{
    public function update(UpdateRequest $request) : UpdateResult;
}
