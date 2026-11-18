<?php

declare(strict_types=1);

namespace Platform\Account\Domain\Services\Create;

use Platform\Account\Domain\Entities\Account;
use Platform\Package\Components\Http\Response\Code;

interface CreateOutput
{
    public function leadNotFound(Code $code) : CreateResult;

    public function currencyNotFound(Code $code) : CreateResult;

    public function groupNotFound(Code $code) : CreateResult;

    public function engineTypeNotFound(Code $code) : CreateResult;

    public function metatraderError(Code $code) : CreateResult;

    public function created(Code $code, Account $account) : CreateResult;

    public function canNotCreate(Code $code) : CreateResult;
}
