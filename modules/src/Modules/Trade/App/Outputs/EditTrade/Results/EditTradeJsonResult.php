<?php

declare(strict_types=1);

namespace Platform\Trade\App\Outputs\EditTrade\Results;

use Platform\Trade\App\Services\EditTrade\EditTradeResult;
use Platform\Package\Components\Http\Response\Concerns\HttpJsonResult;

class EditTradeJsonResult extends EditTradeResult
{
    use HttpJsonResult;
}
