<?php

declare(strict_types=1);

namespace Platform\Account\Domain\Services\Create;

use Platform\Account\Domain\Entities\Account;
use Platform\Package\Components\Http\Response\Code;

class CreateResult
{
    public function __construct(
        public Code     $code,
        public ?Account $account = null,
    ) {
    }
}
