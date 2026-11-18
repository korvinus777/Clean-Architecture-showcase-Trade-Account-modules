<?php

declare(strict_types=1);

namespace Platform\Account\Domain\Services\Delete;

interface DeleteInput
{
    public function delete(DeleteRequest $request) : DeleteResult;
}
