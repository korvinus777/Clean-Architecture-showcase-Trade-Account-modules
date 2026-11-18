<?php

declare(strict_types=1);

namespace Platform\Account\App\Outputs\GetWithdrawalInfo;

use Platform\Account\Domain\Dto\WithdrawalInfoDto;
use Platform\Package\Components\Http\Response\Code;
use Platform\Account\Domain\Services\GetWithdrawalInfo\GetWithdrawalInfoOutput;
use Platform\Account\App\Outputs\GetWithdrawalInfo\Results\GetWithdrawalInfoJsonResult;

class GetWithdrawalInfoJsonOutput implements GetWithdrawalInfoOutput
{
    public function success(WithdrawalInfoDto $withdrawalInfoDto) : GetWithdrawalInfoJsonResult
    {
        return (new GetWithdrawalInfoJsonResult($withdrawalInfoDto))
            ->setCode(Code::SUCCESS)
            ->setHttStatus(200);
    }

    public function unexpectedError() : GetWithdrawalInfoJsonResult
    {
        return (new GetWithdrawalInfoJsonResult())
            ->setCode(Code::UNEXPECTED_ERROR)
            ->setHttStatus(500);
    }

    public function missingData() : GetWithdrawalInfoJsonResult
    {
        return (new GetWithdrawalInfoJsonResult())
            ->setCode(Code::TRANSACTION_WITHDRAWAL_INFO_MISSED)
            ->setHttStatus(404);
    }

    public function withdrawalBlocked() : GetWithdrawalInfoJsonResult
    {
        return (new GetWithdrawalInfoJsonResult())
            ->setCode(Code::LEAD_WITHDRAWAL_BLOCKED)
            ->setHttStatus(403);
    }
}
