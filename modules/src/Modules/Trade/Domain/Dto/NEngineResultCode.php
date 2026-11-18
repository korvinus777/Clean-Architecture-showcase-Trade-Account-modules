<?php

declare(strict_types=1);

namespace Platform\Trade\Domain\Dto;

enum NEngineResultCode : int
{
    case OK                 = 0;
    case OK_NONE            = 1;
    case ERROR              = 2;

    public function isError() : bool
    {
        return ! in_array($this, [self::OK, self::OK_NONE], true);
    }

}
