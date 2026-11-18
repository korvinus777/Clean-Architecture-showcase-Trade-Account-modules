<?php

declare(strict_types=1);

namespace Platform\Account\Domain\Services\Create;

interface CreateInput
{
    public function create(CreateRequest $request) : CreateResult;
}
