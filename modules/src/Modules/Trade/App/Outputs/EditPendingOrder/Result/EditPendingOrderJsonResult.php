<?php

declare(strict_types=1);

namespace Platform\Trade\App\Outputs\EditPendingOrder\Result;

use Platform\Package\Components\Http\Response\Concerns\HttpJsonResult;
use Platform\Trade\Domain\Services\EditPendingOrder\EditPendingOrderResult;

class EditPendingOrderJsonResult extends EditPendingOrderResult
{
    use HttpJsonResult;
}
