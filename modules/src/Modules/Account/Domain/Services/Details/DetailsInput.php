<?php

declare(strict_types=1);

namespace Platform\Account\Domain\Services\Details;

interface DetailsInput
{
    public function get(DetailsRequest $request) : DetailsResult;
}
