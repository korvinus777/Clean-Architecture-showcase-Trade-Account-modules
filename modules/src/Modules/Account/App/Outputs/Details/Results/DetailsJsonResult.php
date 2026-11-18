<?php

declare(strict_types=1);

namespace Platform\Account\App\Outputs\Details\Results;

use Platform\Account\Domain\Services\Details\DetailsResult;
use Platform\Package\Components\Http\Response\Concerns\HttpJsonResult;

class DetailsJsonResult extends DetailsResult
{
    use HttpJsonResult;

    public function getData() : array
    {
        return $this->account ? $this->account->toArray() : [];
    }
}
