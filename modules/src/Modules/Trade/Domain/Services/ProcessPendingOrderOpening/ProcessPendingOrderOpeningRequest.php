<?php

declare(strict_types=1);

namespace Platform\Trade\Domain\Services\ProcessPendingOrderOpening;

use Platform\Trade\Domain\Dto\MetricDto;
use Platform\Trade\Domain\Dto\AccountDto;
use Platform\Trade\Domain\Entities\Symbol;
use Platform\Package\Components\Time\DateTime;
use Platform\Trade\Domain\ValueObjects\PendingOrderType;

class ProcessPendingOrderOpeningRequest
{
    public function __construct(
        public int              $id,
        public float            $lots,
        public float            $price,
        public float            $stopLimitPrice,
        public float            $stopLoss,
        public float            $takeProfit,
        public PendingOrderType $type,
        public DateTime         $time,
        public Symbol           $symbol,
        public AccountDto       $account,
        public MetricDto        $metric,
    ) {
    }

    public function toArray() : array
    {
        return [
            'orderId'        => $this->id,
            'lots'           => $this->lots,
            'price'          => $this->price,
            'stopLimitPrice' => $this->stopLimitPrice,
            'stopLoss'       => $this->stopLoss,
            'takeProfit'     => $this->takeProfit,
            'type'           => $this->type->name,
            'time'           => $this->time->format('Y-m-d H:i:s'),
            'symbol'         => $this->symbol->name,
            'account'        => $this->account->id,
            'login'          => $this->account->login,
        ];
    }
}
