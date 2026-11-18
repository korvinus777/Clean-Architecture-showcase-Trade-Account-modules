<?php

declare(strict_types=1);

namespace Platform\Trade\App\Outputs\DeleteOpenedTradeOutput;

use Platform\Trade\App\Services\DeleteOpenedTrade\DeleteOpenedTradeResult;
use Platform\Trade\App\Services\DeleteOpenedTrade\DeleteOpenedTradeOutput as IDeleteOpenedTradeOutput;

class DeleteOpenedTradeOutput implements IDeleteOpenedTradeOutput
{
    public function tradeNotFound() : DeleteOpenedTradeResult
    {
        return new DeleteOpenedTradeResult(
            notFound: true,
        );
    }

    public function closedTradeNotDeletes() : DeleteOpenedTradeResult
    {
        return new DeleteOpenedTradeResult(
            alreadyClosed: true,
        );
    }

    public function deleted() : DeleteOpenedTradeResult
    {
        return new DeleteOpenedTradeResult(
            deleted: true,
        );
    }

    public function unexpectedError() : DeleteOpenedTradeResult
    {
        return new DeleteOpenedTradeResult(
            deleted: false,
        );
    }
}
