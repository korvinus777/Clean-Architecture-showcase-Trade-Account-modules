<?php

declare(strict_types=1);

namespace Platform\Trade\Domain\Entities;

use Platform\Package\Components\Time\DateTime;
use Platform\Trade\Domain\ValueObjects\Direction;
use Platform\Package\Components\Collection\ObjectChanger;

class Trade
{
    public function __construct(
        public int       $id,
        public int       $symbolId,
        public float     $contractSize,
        public int       $accountId,
        public int       $leadId,
        public Direction $direction,
        public float     $lots,
        public float     $lotsLeft,
        public float     $amount,
        public float     $amountLeft,
        public float     $openPrice,
        public DateTime  $openTime,
        public float     $stopLoss,
        public float     $takeProfit,
        public float     $swap,
        public float     $swapSystem,
        public bool      $isClosed,
        public float     $turnover = 0,
        public float     $turnoverSystem = 0,
    ) {
    }

    /**
     * @param array $with
     * @return array
     */
    public function toArray(array $with = []) : array
    {
        return array_merge($with, [
            'id'           => $this->id,
            'symbolId'     => $this->symbolId,
            'contractSize' => $this->contractSize,
            'accountId'    => $this->accountId,
            'leadId'       => $this->leadId,
            'direction'    => $this->direction->value,
            'lots'         => $this->lots,
            'lotsLeft'     => $this->lotsLeft,
            'amount'       => $this->amount,
            'amountLeft'   => $this->amountLeft,
            'openPrice'    => $this->openPrice,
            'openTime'     => $this->openTime->getTimestampMs(),
            'stopLoss'     => $this->stopLoss,
            'takeProfit'   => $this->takeProfit,
            'swap'           => $this->swap,
            'swapSystem'     => $this->swapSystem,
            'isClosed'       => $this->isClosed,
            'turnover'       => $this->turnover,
            'turnoverSystem' => $this->turnoverSystem,
        ]);
    }

    /**
     * @param ClosedTrade $closedTrade
     * @return ObjectChanger
     * @psalm-suppress PossiblyUnusedReturnValue
     */
    public function close(ClosedTrade $closedTrade) : ObjectChanger
    {
        $changer = new ObjectChanger($this);

        /**
         * @see Trade::$lotsLeft
         * @see Trade::$amountLeft
         * @see Trade::$swap
         * @see Trade::$isClosed
         * @see Trade::$turnover
         * @see Trade::$turnoverSystem
         */
        $changer->change('lotsLeft', max($this->lotsLeft - $closedTrade->lots, 0.0))
            ->change('amountLeft', max($this->amountLeft - $closedTrade->amount, 0.0))
            ->change('swap', $this->calcSwap($this->swap, $closedTrade->swap))
            ->change('turnover', max($this->turnover - $closedTrade->turnover, 0.0))
            ->change('turnoverSystem', max($this->turnoverSystem - $closedTrade->turnoverSystem, 0.0))
            ->change('isClosed', true);

        return $changer;
    }

    /**
     * @param float $tradeSwap
     * @param float $closeSwap
     * @return float
     */
    private function calcSwap(float $tradeSwap, float $closeSwap) : float
    {
        return $tradeSwap === 0.0 ? 0.0 : $tradeSwap - $closeSwap;
    }

    /**
     * @param ClosedTrade $closedTrade
     * @return ObjectChanger
     */
    public function cancelClose(ClosedTrade $closedTrade) : ObjectChanger
    {
        $changer = new ObjectChanger($this);

        /**
         * @see Trade::$lotsLeft
         * @see Trade::$amountLeft
         * @see Trade::$swap
         * @see Trade::$swapSystem
         * @see Trade::$turnover
         * @see Trade::$turnoverSystem
         * @see Trade::$isClosed
         */
        $changer->change('lotsLeft', $this->lotsLeft + $closedTrade->lots)
            ->change('amountLeft', $this->amountLeft + $closedTrade->amount)
            ->change('swap', $this->swap + $closedTrade->swap)
            ->change('swapSystem', $this->swapSystem + $closedTrade->swapSystem)
            ->change('turnover', $this->turnover + $closedTrade->turnover)
            ->change('turnoverSystem', $this->turnoverSystem + $closedTrade->turnoverSystem)
            ->change('isClosed', false);

        return $changer;
    }
}
