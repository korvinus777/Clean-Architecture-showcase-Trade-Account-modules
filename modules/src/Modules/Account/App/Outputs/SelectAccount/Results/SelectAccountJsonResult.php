<?php

declare(strict_types=1);

namespace Platform\Account\App\Outputs\SelectAccount\Results;

use Platform\Package\Components\Http\Response\Concerns\HttpJsonResult;
use Platform\Account\Domain\Services\SelectAccount\SelectAccountResult;

class SelectAccountJsonResult extends SelectAccountResult
{
    use HttpJsonResult;

    public function getData() : array
    {
        return $this->account ? $this->account->toArray() : [];
    }
}
