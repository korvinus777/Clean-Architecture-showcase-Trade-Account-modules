<?php

declare(strict_types=1);

namespace Platform\Account\App\Outputs\LeadAccountList;

use Platform\Package\Components\Http\Response\Code;
use Platform\Account\Domain\Services\LeadAccountList\LeadAccountListOutput;
use Platform\Account\App\Outputs\LeadAccountList\Results\LeadAccountListJsonResult;

class LeadAccountListJsonOutput implements LeadAccountListOutput
{
    /** @inheritDoc */
    public function output(array $accountList) : LeadAccountListJsonResult
    {
        return new LeadAccountListJsonResult($accountList);
    }

    public function leadNotFound() : LeadAccountListJsonResult
    {
        return (new LeadAccountListJsonResult([]))
            ->setCode(Code::LEAD_NOT_FOUND)
            ->setHttStatus(404);
    }
}
