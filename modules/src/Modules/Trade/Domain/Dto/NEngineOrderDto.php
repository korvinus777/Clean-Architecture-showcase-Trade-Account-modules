<?php

declare(strict_types=1);

namespace Platform\Trade\Domain\Dto;

class NEngineOrderDto
{
    public function __construct(
        public NEngineResultCode $resultCode,
        public int               $transactionId,
        public int               $positionId,
        public float             $price,
    ) {
    }

}
