<?php

declare(strict_types=1);

namespace Platform\Account\Domain\Services\Update;

use Platform\Package\Components\Http\Response\Code;

class UpdateResult
{
    public function __construct(public Code $code)
    {
    }

    public function isOk() : bool
    {
        return $this->code === Code::SUCCESS;
    }
}
