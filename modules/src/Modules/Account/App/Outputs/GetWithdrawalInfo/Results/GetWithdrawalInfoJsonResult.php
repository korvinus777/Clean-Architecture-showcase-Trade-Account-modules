<?php

declare(strict_types=1);

namespace Platform\Account\App\Outputs\GetWithdrawalInfo\Results;

use Platform\Package\Components\Http\Response\Concerns\HttpJsonResult;
use Platform\Account\Domain\Services\GetWithdrawalInfo\GetWithdrawalInfoResult;

class GetWithdrawalInfoJsonResult extends GetWithdrawalInfoResult
{
    use HttpJsonResult;

    public function getData() : array
    {
        if ($this->withdrawalInfoDto) {
            return $this->withdrawalInfoDto->toArray();
        }

        return [];
    }
}
