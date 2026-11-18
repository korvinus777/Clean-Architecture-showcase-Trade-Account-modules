<?php

declare(strict_types=1);

namespace Platform\Account\App\Outputs\Update;

use Platform\Package\Components\Http\Response\Code;
use Platform\Account\Domain\Services\Update\UpdateOutput;
use Platform\Account\App\Outputs\Update\Results\UpdateJsonResult;

class UpdateJsonOutput implements UpdateOutput
{
    public function emptyRequest() : UpdateJsonResult
    {
        return new UpdateJsonResult(400, Code::INVALID);
    }

    public function accountNotFound() : UpdateJsonResult
    {
        return new UpdateJsonResult(404, Code::ACCOUNT_NOT_FOUND);
    }

    public function groupNotFound() : UpdateJsonResult
    {
        return new UpdateJsonResult(404, Code::ACCOUNT_GROUP_NOT_FOUND);
    }

    public function unableToUpdate() : UpdateJsonResult
    {
        return new UpdateJsonResult(500, Code::ACCOUNT_UPDATE_ERROR);
    }

    public function notUpdatedOnMetatrader() : UpdateJsonResult
    {
        return new UpdateJsonResult(503, Code::MT_UPDATE_USER_ERROR);
    }

    public function notUpdatedOnNativeTradeEngine() : UpdateJsonResult
    {
        return new UpdateJsonResult(503, Code::NT_UPDATE_USER_ERROR);
    }

    public function mainPasswordNotChanged() : UpdateJsonResult
    {
        return new UpdateJsonResult(503, Code::MT_MAIN_PASSWORD_NOT_CHANGED);
    }

    public function investorPasswordNotChanged() : UpdateJsonResult
    {
        return new UpdateJsonResult(503, Code::MT_INVEST_PASSWORD_NOT_CHANGED);
    }

    public function updated() : UpdateJsonResult
    {
        return new UpdateJsonResult(200, Code::SUCCESS);
    }
}
