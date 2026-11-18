<?php

declare(strict_types=1);

namespace Platform\Trade\Domain\Entities;

use Platform\Package\Components\Time\DateTime;
use Platform\Trade\Domain\ValueObjects\CloseReason;

class ClosedTrade
{
    public function __construct(
        public int         $id,
        public int         $tradeId,
        public int         $mt5TransactionId,
        public float       $lots,
        public float       $amount,
        public float       $price,
        public CloseReason $reason,
        public DateTime    $time,
        public float       $pnl,
        public float       $pnlSystem,
        public float       $swap,
        public float       $swapSystem,
        public float       $commission,
        public float       $turnover = 0,
        public float       $turnoverSystem = 0,
    ) {
    }

    public function toArray(array $with = []) : array
    {
        return array_merge($with, [
            'id'               => $this->id,
            'tradeId'          => $this->tradeId,
            'mt5TransactionId' => $this->mt5TransactionId,
            'lots'             => $this->lots,
            'amount'           => $this->amount,
            'price'            => $this->price,
            'reason'           => $this->reason->value,
            'time'             => $this->time->getTimestampMs(),
            'pnl'              => $this->pnl,
            'pnlSystem'        => $this->pnlSystem,
            'swap'             => $this->swap,
            'swapSystem'       => $this->swapSystem,
            'commission'       => $this->commission,
            'turnover'         => $this->turnover,
            'turnoverSystem'   => $this->turnoverSystem,
        ]);
    }
}
