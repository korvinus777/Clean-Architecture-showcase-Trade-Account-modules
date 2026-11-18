<?php

declare(strict_types=1);

namespace Platform\Account\Domain\Services\Update;

interface UpdateOutput
{
    public function emptyRequest() : UpdateResult;

    public function accountNotFound() : UpdateResult;

    public function groupNotFound() : UpdateResult;

    public function unableToUpdate() : UpdateResult;

    public function notUpdatedOnMetatrader() : UpdateResult;

    public function notUpdatedOnNativeTradeEngine() : UpdateResult;

    public function mainPasswordNotChanged() : UpdateResult;

    public function investorPasswordNotChanged() : UpdateResult;

    public function updated() : UpdateResult;
}
