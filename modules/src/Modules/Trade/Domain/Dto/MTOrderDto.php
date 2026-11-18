<?php

declare(strict_types=1);

namespace Platform\Trade\Domain\Dto;

class MTOrderDto
{
    public function __construct(
        public MTResultCode $resultCode,
        public int          $orderId,
        public int          $dealId,
        public float        $price,
    ) {
    }
}
