<?php

declare(strict_types=1);

namespace Platform\Trade\Domain\Dto;

class NTOrderDto
{
    public function __construct(
        public NTResultCode $resultCode,
        public int          $orderId,
        public int          $login,
        public string       $symbol,
        public float        $volumeInitialExt,
        public int          $type,
        public float        $orderPrice,
        public float        $orderTrigger,
        public float        $priceSl,
        public float        $priceTp,
        public int          $timestampMs,
    ) {
    }
}
