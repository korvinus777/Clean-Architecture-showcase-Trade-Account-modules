<?php

declare(strict_types=1);

namespace Platform\Trade\Domain\Dto;

class NTPositionDto
{
    public function __construct(
        public NTResultCode $resultCode,
        public int          $login = 0,
        public int          $symbolId = 0,
        public float        $volume = 0,
        public int          $transactionId = 0,
        public int          $positionId = 0,
        public float        $price = 0,
        public float        $stopLoss = 0,
        public float        $takeProfit = 0,
        public int          $time = 0,
        public int          $transactionTime = 0,
        public float        $commission = 0,
        public float        $swap = 0,
        public ?float       $accountMargin = null,
    ) {
    }
}
